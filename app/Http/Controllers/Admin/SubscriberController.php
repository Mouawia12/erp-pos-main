<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscriber;
use App\Models\SubscriberDocument;
use App\Models\SubscriberRenewal;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

class SubscriberController extends Controller
{
    public function index()
    {
        $subscribers = Subscriber::with(['renewals' => function ($q) {
            $q->latest();
        }, 'documents'])
            ->orderByDesc('id')
            ->get()
            ->map(function (Subscriber $subscriber) {
                $this->refreshStatus($subscriber);
                return $subscriber;
            });

        $stats = [
            'total' => $subscribers->count(),
            'active' => $subscribers->where('status', 'active')->count(),
            'near_expiry' => $subscribers->where('status', 'near_expiry')->count(),
            'expired' => $subscribers->where('status', 'expired')->count(),
        ];

        return view('admin.subscribers.index', compact('subscribers', 'stats'));
    }

    public function create()
    {
        return view('admin.subscribers.create');
    }

    public function store(Request $request)
    {
        $data = $this->validateRequest($request);
        $data['created_by'] = Auth::id();

        [$loginEmail, $loginPassword, $loginPlain] = $this->prepareCredentials($request);
        $data['login_email'] = $loginEmail;
        $data['login_password'] = $loginPassword;
        $data['login_password_plain'] = $loginPlain;

        $subscriber = Subscriber::create($data);
        $this->syncUserAccount($subscriber, $loginEmail, $loginPlain);
        $this->refreshStatus($subscriber);

        $this->storeDocuments($request, $subscriber->id);

        return redirect()->route('owner.subscribers.index')->with('success', __('main.saved_successfully') ?? 'تم الحفظ بنجاح');
    }

    public function edit(Subscriber $subscriber)
    {
        $subscriber->load('documents', 'renewals');
        return view('admin.subscribers.edit', compact('subscriber'));
    }

    public function update(Request $request, Subscriber $subscriber)
    {
        $data = $this->validateRequest($request, $subscriber->id);
        [$loginEmail, $loginPassword, $loginPlain] = $this->prepareCredentials($request, $subscriber);
        $data['login_email'] = $loginEmail;
        if ($loginPassword) {
            $data['login_password'] = $loginPassword;
            $data['login_password_plain'] = $loginPlain;
        }

        $subscriber->update($data);
        $this->syncUserAccount($subscriber, $loginEmail, $loginPlain);
        $this->refreshStatus($subscriber);

        $this->storeDocuments($request, $subscriber->id);

        return redirect()->route('owner.subscribers.index')->with('success', __('main.updated') ?? 'تم التحديث');
    }

    public function destroy(Subscriber $subscriber)
    {
        foreach ($subscriber->documents as $doc) {
            Storage::disk('public')->delete($doc->file_path);
        }
        $subscriber->delete();

        return redirect()->route('owner.subscribers.index')->with('success', __('main.deleted') ?? 'تم الحذف');
    }

    public function renew(Request $request, Subscriber $subscriber)
    {
        $validated = $request->validate([
            'add_days' => 'nullable|integer|min:0',
            'add_months' => 'nullable|integer|min:0',
            'add_years' => 'nullable|integer|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        $base = $subscriber->subscription_end ? Carbon::parse($subscriber->subscription_end) : now();
        $previousEnd = $subscriber->subscription_end;

        $base = $base->addYears($validated['add_years'] ?? 0)
            ->addMonths($validated['add_months'] ?? 0)
            ->addDays($validated['add_days'] ?? 0);

        $subscriber->update([
            'subscription_end' => $base,
        ]);

        SubscriberRenewal::create([
            'subscriber_id' => $subscriber->id,
            'previous_end_date' => $previousEnd,
            'new_end_date' => $subscriber->subscription_end,
            'added_days' => $validated['add_days'] ?? 0,
            'added_months' => $validated['add_months'] ?? 0,
            'added_years' => $validated['add_years'] ?? 0,
            'notes' => $validated['notes'] ?? null,
            'renewed_by' => Auth::id(),
        ]);

        $this->refreshStatus($subscriber);

        return redirect()->route('owner.subscribers.index')->with('success', __('main.saved_successfully') ?? 'تم التجديد');
    }

    public function deleteDocument(SubscriberDocument $document)
    {
        if (!Auth::user() || !Auth::user()->hasRole('system_owner')) {
            abort(403);
        }
        Storage::disk('public')->delete($document->file_path);
        $document->delete();
        return back()->with('success', __('main.deleted') ?? 'تم الحذف');
    }

    private function validateRequest(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'company_name' => 'required|string|max:255',
            'cr_number' => 'nullable|string|max:255',
            'tax_number' => 'nullable|string|max:255',
            'responsible_person' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:255',
            'login_email' => 'nullable|email|max:255',
            'login_password' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'system_url' => 'nullable|string|max:255',
            'users_limit' => 'nullable|integer|min:1',
            'subscription_start' => 'nullable|date',
            'subscription_end' => 'nullable|date|after_or_equal:subscription_start',
            'status' => 'nullable|in:active,near_expiry,expired',
            'notes' => 'nullable|string',
            'documents.*' => 'nullable|file|max:2048',
            'document_titles.*' => 'nullable|string|max:255'
        ]);
    }

    private function prepareCredentials(Request $request, ?Subscriber $existing = null): array
    {
        $loginEmail = $request->input('login_email', $existing->login_email ?? null);
        $passwordInput = $request->input('login_password');

        if ($existing && !$passwordInput) {
            return [$loginEmail, null, $existing->login_password_plain];
        }

        $plain = $passwordInput ?: 'password123';
        $hashed = bcrypt($plain);

        return [$loginEmail, $hashed, $plain];
    }

    private function syncUserAccount(Subscriber $subscriber, ?string $loginEmail, ?string $plainPassword): void
    {
        if (!$loginEmail || !$plainPassword) {
            return;
        }

        $role = Role::firstOrCreate(['name' => 'مدير النظام', 'guard_name' => 'admin-web']);

        $user = User::updateOrCreate(
            ['email' => $loginEmail],
            [
                'name' => $subscriber->responsible_person ?: $subscriber->company_name,
                'password' => Hash::make($plainPassword),
                'branch_id' => 1,
                'subscriber_id' => $subscriber->id,
                'role_name' => 'مدير النظام',
                'status' => 1,
                'phone_number' => $subscriber->contact_phone ?? '0000000000',
                'profile_pic' => '',
            ]
        );

        $user->assignRole($role->name);

        if (!$subscriber->user_id || $subscriber->user_id !== $user->id) {
            $subscriber->update(['user_id' => $user->id]);
        }
    }

    private function refreshStatus(Subscriber $subscriber): void
    {
        if (!$subscriber->subscription_end) {
            return;
        }

        $today = now()->startOfDay();
        $end = Carbon::parse($subscriber->subscription_end)->startOfDay();

        $newStatus = 'active';
        if ($end->lt($today)) {
            $newStatus = 'expired';
        } elseif ($end->diffInDays($today) <= 30) {
            $newStatus = 'near_expiry';
        }

        if ($subscriber->status !== $newStatus) {
            $subscriber->update(['status' => $newStatus]);
        }
    }

    private function storeDocuments(Request $request, int $subscriberId): void
    {
        $documents = $request->file('documents', []);
        $titles = $request->input('document_titles', []);

        foreach ($documents as $index => $doc) {
            if (!$doc) {
                continue;
            }
            $path = $doc->store('subscribers', 'public');
            SubscriberDocument::create([
                'subscriber_id' => $subscriberId,
                'title' => $titles[$index] ?? $doc->getClientOriginalName(),
                'file_path' => $path,
                'uploaded_by' => Auth::id(),
            ]);
        }
    }
}

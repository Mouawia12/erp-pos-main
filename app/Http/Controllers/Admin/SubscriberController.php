<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscriber;
use App\Models\SubscriberDocument;
use App\Models\SubscriberRenewal;
use App\Models\SystemSettings;
use App\Models\User;
use App\Services\SubscriberProvisioner;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class SubscriberController extends Controller
{
    public function index()
    {
        $subscribers = Subscriber::with(['renewals' => function ($q) {
            $q->latest();
        }, 'documents', 'users'])
            ->orderByDesc('id')
            ->get()
            ->map(function (Subscriber $subscriber) {
                $subscriber->refreshLifecycleStatus();
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
        $data['is_trial'] = $request->boolean('is_trial');
        $data = $this->applyTrialDefaults($data);
        $data['created_by'] = Auth::id();

        [$loginEmail, $loginPassword, $loginPlain, $passwordChanged] = $this->prepareCredentials($request);
        $data['login_email'] = $loginEmail;
        $data['login_password'] = $loginPassword;
        $data['login_password_plain'] = $loginPlain;

        $subscriber = Subscriber::create($data);
        $this->syncUserAccount($subscriber, $loginEmail, $loginPassword);
        $this->syncSystemSettings($subscriber);
        $subscriber->refreshLifecycleStatus();

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
        $data = $this->validateRequest($request, $subscriber);
        $data['is_trial'] = $request->boolean('is_trial');
        $data = $this->applyTrialDefaults($data, $subscriber);
        [$loginEmail, $loginPassword, $loginPlain, $passwordChanged] = $this->prepareCredentials($request, $subscriber);
        $data['login_email'] = $loginEmail;
        if ($passwordChanged) {
            $data['login_password'] = $loginPassword;
            $data['login_password_plain'] = $loginPlain;
        }

        $subscriber->update($data);
        $this->syncUserAccount($subscriber, $loginEmail, $loginPassword);
        $this->syncSystemSettings($subscriber);
        $subscriber->refreshLifecycleStatus();

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

    public function archiveDocument(SubscriberDocument $document)
    {
        if (!Auth::user() || !Auth::user()->hasRole('system_owner')) {
            abort(403);
        }
        $document->update(['archived_at' => now()]);
        return back()->with('success', __('main.archived') ?? 'تم الأرشفة');
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

        $subscriber->refreshLifecycleStatus();

        return redirect()->route('owner.subscribers.index')->with('success', __('main.saved_successfully') ?? 'تم التجديد');
    }

    public function permissions(Subscriber $subscriber)
    {
        $settings = SystemSettings::firstOrCreate(
            ['subscriber_id' => $subscriber->id],
            [
                'company_name' => $subscriber->company_name,
                'email' => $subscriber->contact_email,
                'client_group_id' => 0,
                'nom_of_days_to_edit_bill' => 0,
                'branch_id' => 0,
                'cashier_id' => 0,
            ]
        );

        return view('admin.subscribers.permissions', compact('subscriber', 'settings'));
    }

    public function updatePermissions(Request $request, Subscriber $subscriber)
    {
        $data = $request->validate([
            'max_branches' => 'nullable|integer|min:0',
        ]);

        $settings = SystemSettings::firstOrCreate(
            ['subscriber_id' => $subscriber->id],
            [
                'company_name' => $subscriber->company_name,
                'email' => $subscriber->contact_email,
                'client_group_id' => 0,
                'nom_of_days_to_edit_bill' => 0,
                'branch_id' => 0,
                'cashier_id' => 0,
            ]
        );

        $settings->update($data);

        return redirect()
            ->route('owner.subscribers.index')
            ->with('success', 'تم تحديث صلاحيات المشترك بنجاح');
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

    private function validateRequest(Request $request, ?Subscriber $subscriber = null): array
    {
        $subscriberId = $subscriber?->id;
        $subscriberUserId = $subscriber?->user_id;

        $loginEmailRules = [
            'required',
            'email',
            'max:255',
            Rule::unique('subscribers', 'login_email')->ignore($subscriberId),
        ];

        $loginEmailRules[] = $subscriberUserId
            ? Rule::unique('users', 'email')->ignore($subscriberUserId)
            : Rule::unique('users', 'email');

        return $request->validate([
            'company_name' => 'required|string|max:255',
            'cr_number' => 'nullable|string|max:255',
            'tax_number' => 'nullable|string|max:255',
            'responsible_person' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:255',
            'login_email' => $loginEmailRules,
            'login_password' => 'nullable|string|min:6|max:255',
            'address' => 'nullable|string|max:255',
            'system_url' => 'nullable|string|max:255',
            'users_limit' => 'nullable|integer|min:1',
            'subscription_start' => 'nullable|date',
            'subscription_end' => 'nullable|date|after_or_equal:subscription_start',
            'is_trial' => 'nullable|boolean',
            'trial_starts_at' => 'nullable|date',
            'trial_ends_at' => 'nullable|date|after_or_equal:trial_starts_at',
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
            return [$loginEmail, $existing->login_password, $existing->login_password_plain, false];
        }

        $plain = $passwordInput ?: 'password123';
        $hashed = Hash::make($plain);

        return [$loginEmail, $hashed, $plain, true];
    }

    private function syncUserAccount(Subscriber $subscriber, ?string $loginEmail, ?string $hashedPassword): void
    {
        if (!$loginEmail || !$hashedPassword) {
            return;
        }

        $role = Role::firstOrCreate(['name' => 'مدير النظام', 'guard_name' => 'admin-web']);
        [$branch, $warehouse] = SubscriberProvisioner::ensureDefaults($subscriber);

        $user = User::updateOrCreate(
            ['email' => $loginEmail],
            [
                'name' => $subscriber->responsible_person ?: $subscriber->company_name,
                'password' => $hashedPassword,
                'branch_id' => $branch->id,
                'subscriber_id' => $subscriber->id,
                'role_name' => $role->name,
                'status' => 1,
                'phone_number' => $subscriber->contact_phone ?? '0000000000',
                'profile_pic' => '',
            ]
        );

        $user->syncRoles([$role->name]);

        if (!$subscriber->user_id || $subscriber->user_id !== $user->id) {
            $subscriber->update(['user_id' => $user->id]);
        }

        if ($warehouse && (! $warehouse->user_id || $warehouse->user_id !== $user->id)) {
            $warehouse->update(['user_id' => $user->id]);
        }
    }

    private function applyTrialDefaults(array $data, ?Subscriber $existing = null): array
    {
        if (! ($data['is_trial'] ?? false)) {
            $data['trial_starts_at'] = null;
            $data['trial_ends_at'] = null;
            return $data;
        }

        $startInput = $data['trial_starts_at']
            ?? $data['subscription_start']
            ?? ($existing?->trial_starts_at ? $existing->trial_starts_at->format('Y-m-d') : now()->format('Y-m-d'));
        $start = Carbon::parse($startInput)->startOfDay();
        $endInput = $data['trial_ends_at']
            ?? ($existing?->trial_ends_at ? $existing->trial_ends_at->format('Y-m-d') : null);
        $end = $endInput ? Carbon::parse($endInput)->startOfDay() : $start->copy()->addDays(30);

        $data['trial_starts_at'] = $start->format('Y-m-d');
        $data['trial_ends_at'] = $end->format('Y-m-d');
        $data['subscription_start'] = $data['subscription_start'] ?? $start->format('Y-m-d');
        $data['subscription_end'] = $end->format('Y-m-d');

        return $data;
    }

    private function syncSystemSettings(Subscriber $subscriber): void
    {
        $settings = SystemSettings::firstOrCreate(
            ['subscriber_id' => $subscriber->id],
            [
                'company_name' => $subscriber->company_name,
                'email' => $subscriber->contact_email,
                'client_group_id' => 0,
                'nom_of_days_to_edit_bill' => 0,
                'branch_id' => 0,
                'cashier_id' => 0,
            ]
        );

        $settings->fill([
            'company_name' => $subscriber->company_name,
            'email' => $subscriber->contact_email,
            'tax_number' => $subscriber->tax_number,
        ])->save();
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

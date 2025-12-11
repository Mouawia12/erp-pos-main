<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Sales;
use App\Models\ZatcaDocument;
use App\Services\ZatcaIntegration\ZatcaDocumentService;
use App\Services\ZatcaIntegration\ZatcaInvoiceService;
use App\Services\ZatcaIntegration\ZatcaOnboardingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ZatcaController extends Controller
{
    public function onboard(Request $request, ZatcaOnboardingService $service): RedirectResponse
    {
        $data = $request->validate([
            'otp' => ['required', 'string', 'max:191'],
            'env' => ['nullable', 'in:developer-portal,simulation,core'],
            'invoice_type' => ['nullable', 'string', 'max:191'],
            'egs_serial' => ['nullable', 'string', 'max:191'],
            'business_category' => ['nullable', 'string', 'max:191'],
            'simulate' => ['nullable', 'boolean'],
            'branch_id' => ['required', 'integer', 'exists:branches,id'],
        ]);

        if (! config('zatca.enabled')) {
            return back()->with('error', __('main.feature_disabled') ?? 'تم تعطيل ربط هيئة الزكاة في الإعدادات.');
        }

        $overrides = array_filter([
            'env' => $data['env'] ?? null,
            'invoice_type' => $data['invoice_type'] ?? null,
            'egs_serial' => $data['egs_serial'] ?? null,
            'business_category' => $data['business_category'] ?? null,
        ]);

        $subscriberId = Auth::guard('admin-web')->user()?->subscriber_id;
        $branch = Branch::query()
            ->when($subscriberId, fn ($query) => $query->where('subscriber_id', $subscriberId))
            ->find($data['branch_id']);

        if (! $branch) {
            return back()->with('error', __('main.zatca_branch_not_found') ?? 'تعذر العثور على الفرع المحدد.');
        }

        $simulate = (bool) ($data['simulate'] ?? false);

        try {
            $service->authorize($branch, $data['otp'], $overrides, $simulate);
        } catch (\Throwable $exception) {
            return back()->withInput()->with('error', $exception->getMessage());
        }

        return back()
            ->with('success', __('main.zatca_onboarding_success', ['branch' => $branch->branch_name ?? '#'.$branch->id]) ?? 'تم إرسال طلب هيئة الزكاة بنجاح');
    }

    public function sendInvoice(Sales $sale, ZatcaInvoiceService $invoiceService, ZatcaDocumentService $documentService): RedirectResponse
    {
        if (! config('zatca.enabled')) {
            return back()->with('error', __('main.feature_disabled') ?? 'تم تعطيل ربط هيئة الزكاة في الإعدادات.');
        }

        try {
            $document = $documentService->initDocumentForSale($sale);
            $invoiceService->sendForSale($sale, $document);
        } catch (\Throwable $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return back()->with('success', __('main.sent_successfully') ?? 'تم إرسال الفاتورة إلى هيئة الزكاة.');
    }

    public function resendDocument(ZatcaDocument $document, ZatcaInvoiceService $invoiceService, ZatcaDocumentService $documentService): RedirectResponse
    {
        if (! config('zatca.enabled')) {
            return back()->with('error', __('main.feature_disabled') ?? 'تم تعطيل ربط هيئة الزكاة في الإعدادات.');
        }

        $sale = $document->sale;
        if (! $sale) {
            return back()->with('error', __('main.could_not_find_record') ?? 'لا يوجد فاتورة مرتبطة بهذا المستند.');
        }

        try {
            $invoiceService->sendForSale($sale, $document);
        } catch (\Throwable $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return back()->with('success', __('main.sent_successfully') ?? 'تمت إعادة إرسال المستند بنجاح.');
    }

    public function sendByReference(Request $request, ZatcaInvoiceService $invoiceService, ZatcaDocumentService $documentService): RedirectResponse
    {
        if (! config('zatca.enabled')) {
            return back()->with('error', __('main.feature_disabled') ?? 'تم تعطيل ربط هيئة الزكاة في الإعدادات.');
        }

        $data = $request->validate([
            'sale_id' => ['nullable', 'integer'],
            'invoice_no' => ['nullable', 'string', 'max:191'],
        ]);

        if (empty($data['sale_id']) && empty($data['invoice_no'])) {
            return back()->with('error', __('main.validation_error') ?? 'يرجى إدخال رقم الفاتورة أو رقم المستند.');
        }

        $sale = null;
        if (! empty($data['sale_id'])) {
            $sale = Sales::with(['customer', 'branch', 'details'])->find($data['sale_id']);
        }
        if (! $sale && ! empty($data['invoice_no'])) {
            $sale = Sales::with(['customer', 'branch', 'details'])->where('invoice_no', $data['invoice_no'])->first();
        }

        if (! $sale) {
            return back()->with('error', __('main.could_not_find_record') ?? 'لا يوجد فاتورة مطابقة للبيانات المدخلة.');
        }

        try {
            $document = $documentService->initDocumentForSale($sale);
            $invoiceService->sendForSale($sale, $document);
        } catch (\Throwable $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return back()->with('success', __('main.sent_successfully') ?? 'تم إرسال الفاتورة إلى هيئة الزكاة.');
    }
}

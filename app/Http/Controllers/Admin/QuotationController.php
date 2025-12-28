<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Quotation;
use App\Models\QuotationDetail;
use App\Models\Sales;
use App\Models\SaleDetails;
use App\Models\SystemSettings;
use App\Models\Warehouse;
use App\Models\Branch;
use App\Models\Representative;
use App\Models\CostCenter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class QuotationController extends Controller
{
    public function index()
    {
        $quotations = Quotation::with('customer')->latest()->get();
        return view('admin.quotations.index', compact('quotations'));
    }

    public function create()
    {
        $customers = Company::where('group_id',3)->get();
        $warehouses = Warehouse::all();
        $branches = Branch::where('status',1)->get();
        $products = Product::with('variants')->get();
        $settings = SystemSettings::first();
        $defaultInvoiceType = $this->resolveDefaultInvoiceType();
        $walkInCustomer = Company::ensureWalkInCustomer(Auth::user()->subscriber_id ?? null);
        $nextQuotationNo = $this->generateQuotationNumber(optional(Auth::user())->branch_id);
        $representatives = Representative::all();
        $subscriberId = Auth::user()->subscriber_id ?? null;
        $costCenters = CostCenter::query()
            ->when($subscriberId, fn($q) => $q->where('subscriber_id', $subscriberId))
            ->where('is_active', 1)
            ->orderBy('name')
            ->get();

        return view('admin.quotations.create', compact(
            'customers',
            'warehouses',
            'products',
            'branches',
            'settings',
            'defaultInvoiceType',
            'walkInCustomer',
            'nextQuotationNo',
            'representatives',
            'costCenters'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|integer|exists:companies,id',
            'warehouse_id' => 'required|integer|exists:warehouses,id',
            'branch_id' => 'nullable|integer|exists:branches,id',
            'invoice_type' => ['nullable', Rule::in(['tax_invoice','simplified_tax_invoice','non_tax_invoice'])],
            'payment_method' => ['nullable', Rule::in(['cash','credit'])],
            'cost_center_id' => ['nullable', 'exists:cost_centers,id'],
            'product_id' => 'required|array|min:1',
            'product_id.*' => 'integer|exists:products,id',
            'qnt' => 'required|array|min:1',
            'price' => 'required|array|min:1',
        ]);

        $quotationNo = $this->generateQuotationNumber($request->branch_id);

        $total = 0; $tax = 0; $net = 0;
        $lines = [];
        foreach($request->product_id as $idx => $pid){
            $qty = (float)($request->qnt[$idx] ?? 0);
            $price = (float)($request->price[$idx] ?? 0);
            $lineTax = (float)($request->tax[$idx] ?? 0);
            $lineTotal = $qty * $price;
            $total += $lineTotal;
            $tax += $lineTax;
            $net += $lineTotal + $lineTax;
            $lines[] = [
                'product_id' => $pid,
                'variant_id' => $request->variant_id[$idx] ?? null,
                'variant_color' => $request->variant_color[$idx] ?? null,
                'variant_size' => $request->variant_size[$idx] ?? null,
                'variant_barcode' => $request->variant_barcode[$idx] ?? null,
                'quantity' => $qty,
                'price_unit' => $price,
                'tax' => $lineTax,
                'total' => $lineTotal,
            ];
        }

        $subscriberId = Auth::user()->subscriber_id ?? null;
        $costCenterId = $request->cost_center_id ?: null;
        $costCenterName = $request->cost_center;
        if ($costCenterId && empty($costCenterName)) {
            $costCenterName = CostCenter::query()
                ->when($subscriberId, fn($q) => $q->where('subscriber_id', $subscriberId))
                ->where('is_active', 1)
                ->find($costCenterId)?->name;
        }

        $quotation = Quotation::create([
            'date' => now(),
            'quotation_no' => $quotationNo,
            'invoice_type' => $request->invoice_type ?? 'simplified_tax_invoice',
            'customer_id' => $request->customer_id,
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'customer_address' => $request->customer_address,
            'customer_tax_number' => $request->customer_tax_number,
            'warehouse_id' => $request->warehouse_id,
            'representative_id' => $request->representative_id,
            'cost_center' => $costCenterName,
            'cost_center_id' => $costCenterId,
            'payment_method' => $request->payment_method ?? 'cash',
            'note' => $request->note,
            'total' => $total,
            'discount' => 0,
            'tax' => $tax,
            'net' => $net,
            'status' => 'draft',
            'branch_id' => $request->branch_id ?? optional(Auth::user()->branch)->id,
            'user_id' => Auth::id(),
        ]);

        foreach($lines as $line){
            $line['quotation_id'] = $quotation->id;
            QuotationDetail::create($line);
        }

        return redirect()->route('quotations.index')->with('success', __('main.created'));
    }

    public function show(Quotation $quotation)
    {
        $quotation->load('details.product','customer');
        return view('admin.quotations.show', compact('quotation'));
    }

    public function edit(Quotation $quotation)
    {
        $quotation->load('details');
        $customers = Company::where('group_id',3)->get();
        $warehouses = Warehouse::all();
        $branches = Branch::where('status',1)->get();
        $products = Product::with('variants')->get();
        $defaultInvoiceType = $this->resolveDefaultInvoiceType();
        $walkInCustomer = Company::ensureWalkInCustomer(Auth::user()->subscriber_id ?? null);
        $representatives = Representative::all();
        $subscriberId = Auth::user()->subscriber_id ?? null;
        $costCenters = CostCenter::query()
            ->when($subscriberId, fn($q) => $q->where('subscriber_id', $subscriberId))
            ->where('is_active', 1)
            ->orderBy('name')
            ->get();

        return view('admin.quotations.edit', compact('quotation','customers','warehouses','products','branches','defaultInvoiceType','walkInCustomer','representatives','costCenters'));
    }

    public function update(Request $request, Quotation $quotation)
    {
        $request->validate([
            'customer_id' => 'required|integer|exists:companies,id',
            'warehouse_id' => 'required|integer|exists:warehouses,id',
            'branch_id' => 'nullable|integer|exists:branches,id',
            'invoice_type' => ['nullable', Rule::in(['tax_invoice','simplified_tax_invoice','non_tax_invoice'])],
            'payment_method' => ['nullable', Rule::in(['cash','credit'])],
            'cost_center_id' => ['nullable', 'exists:cost_centers,id'],
            'product_id' => 'required|array|min:1',
            'product_id.*' => 'integer|exists:products,id',
            'qnt' => 'required|array|min:1',
            'price' => 'required|array|min:1',
        ]);

        $total = 0; $tax = 0; $net = 0;
        $lines = [];
        foreach($request->product_id as $idx => $pid){
            $qty = (float)($request->qnt[$idx] ?? 0);
            $price = (float)($request->price[$idx] ?? 0);
            $lineTax = (float)($request->tax[$idx] ?? 0);
            $lineTotal = $qty * $price;
            $total += $lineTotal;
            $tax += $lineTax;
            $net += $lineTotal + $lineTax;
            $lines[] = [
                'product_id' => $pid,
                'variant_id' => $request->variant_id[$idx] ?? null,
                'variant_color' => $request->variant_color[$idx] ?? null,
                'variant_size' => $request->variant_size[$idx] ?? null,
                'variant_barcode' => $request->variant_barcode[$idx] ?? null,
                'quantity' => $qty,
                'price_unit' => $price,
                'tax' => $lineTax,
                'total' => $lineTotal,
            ];
        }

        $subscriberId = Auth::user()->subscriber_id ?? null;
        $costCenterId = $request->cost_center_id ?: null;
        $costCenterName = $request->cost_center;
        if ($costCenterId && empty($costCenterName)) {
            $costCenterName = CostCenter::query()
                ->when($subscriberId, fn($q) => $q->where('subscriber_id', $subscriberId))
                ->where('is_active', 1)
                ->find($costCenterId)?->name;
        }

        $quotation->update([
            'customer_id' => $request->customer_id,
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'customer_address' => $request->customer_address,
            'customer_tax_number' => $request->customer_tax_number,
            'warehouse_id' => $request->warehouse_id,
            'representative_id' => $request->representative_id,
            'cost_center' => $costCenterName,
            'cost_center_id' => $costCenterId,
            'invoice_type' => $request->invoice_type ?? $quotation->invoice_type,
            'payment_method' => $request->payment_method ?? $quotation->payment_method,
            'note' => $request->note,
            'total' => $total,
            'discount' => 0,
            'tax' => $tax,
            'net' => $net,
            'branch_id' => $request->branch_id ?? $quotation->branch_id,
        ]);

        $quotation->details()->delete();
        foreach($lines as $line){
            $line['quotation_id'] = $quotation->id;
            QuotationDetail::create($line);
        }

        return redirect()->route('quotations.index')->with('success', __('main.updated'));
    }

    public function destroy(Quotation $quotation)
    {
        $quotation->details()->delete();
        $quotation->delete();
        return back()->with('success', __('main.deleted'));
    }

    public function convertToInvoice(Quotation $quotation)
    {
        $quotation->load('details');
        $settings = SystemSettings::first();
        $prefix = $settings->sales_prefix ?? 'INV-';
        $invoiceNo = $prefix . str_pad((Sales::max('id') + 1), 6, '0', STR_PAD_LEFT);

        DB::transaction(function() use ($quotation, $invoiceNo){
            $sale = Sales::create([
                'date' => now(),
                'invoice_no' => $invoiceNo,
                'customer_id' => $quotation->customer_id,
                'biller_id' => Auth::id(),
                'warehouse_id' => $quotation->warehouse_id ?? 1,
                'total' => $quotation->total,
                'discount' => $quotation->discount,
                'tax' => $quotation->tax,
                'tax_excise' => 0,
                'net' => $quotation->net,
                'paid' => 0,
                'sale_status' => 'completed',
                'payment_status' => 'not_paid',
                'created_by' => Auth::id(),
                'pos' => 0,
                'lista' => 0,
                'profit'=> 0,
                'additional_service' => 0,
                'note' => $quotation->note,
                'branch_id'=> $quotation->branch_id ?? 1,
                'user_id'=> Auth::id(),
                'status'=> 1,
            ]);

            foreach($quotation->details as $detail){
                SaleDetails::create([
                    'sale_id' => $sale->id,
                    'product_code' => optional($detail->product)->code ?? '',
                    'product_id' => $detail->product_id,
                    'variant_id' => $detail->variant_id,
                    'variant_color' => $detail->variant_color,
                    'variant_size' => $detail->variant_size,
                    'variant_barcode' => $detail->variant_barcode,
                    'quantity' => $detail->quantity,
                    'price_unit' => $detail->price_unit,
                    'discount' => 0,
                    'price_with_tax' => $detail->price_unit + $detail->tax,
                    'warehouse_id' => $sale->warehouse_id,
                    'unit_id' => optional($detail->product)->unit ?? 1,
                    'unit_factor' => 1,
                    'tax' => $detail->tax,
                    'tax_excise' => 0,
                    'total' => $detail->total,
                    'lista' => 0,
                    'profit'=> 0,
                ]);
            }

            $quotation->update(['status' => 'converted']);
        });

        return redirect()->route('quotations.index')->with('success', __('main.done'));
    }

    private function resolveDefaultInvoiceType(): string
    {
        $user = Auth::user();

        if ($user && !empty($user->default_invoice_type)) {
            return $user->default_invoice_type;
        }

        if ($user && $user->branch && !empty($user->branch->default_invoice_type)) {
            return $user->branch->default_invoice_type;
        }

        $systemDefault = optional(SystemSettings::first())->default_invoice_type;

        return $systemDefault ?: 'simplified_tax_invoice';
    }

    private function generateQuotationNumber(?int $branchId = null): string
    {
        $settings = SystemSettings::first();
        $prefix = $settings->quotation_prefix ?? 'QTN-';
        if ($branchId) {
            $prefix .= $branchId.'-';
        }

        $nextSequence = (Quotation::max('id') ?? 0) + 1;

        return $prefix . str_pad($nextSequence, 5, '0', STR_PAD_LEFT);
    }
}

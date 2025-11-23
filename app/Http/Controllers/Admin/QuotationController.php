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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        $products = Product::with('variants')->get();
        return view('admin.quotations.create', compact('customers','warehouses','products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|integer',
            'warehouse_id' => 'required|integer',
            'product_id' => 'required|array',
            'product_id.*' => 'integer',
            'qnt' => 'required|array',
            'price' => 'required|array',
        ]);

        $settings = SystemSettings::first();
        $prefix = $settings->quotation_prefix ?? 'QTN-';
        $quotationNo = $prefix . str_pad((Quotation::max('id') + 1), 5, '0', STR_PAD_LEFT);

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

        $quotation = Quotation::create([
            'date' => now(),
            'quotation_no' => $quotationNo,
            'customer_id' => $request->customer_id,
            'warehouse_id' => $request->warehouse_id,
            'note' => $request->note,
            'total' => $total,
            'discount' => 0,
            'tax' => $tax,
            'net' => $net,
            'status' => 'draft',
            'branch_id' => $request->branch_id ?? null,
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
        $products = Product::with('variants')->get();
        return view('admin.quotations.edit', compact('quotation','customers','warehouses','products'));
    }

    public function update(Request $request, Quotation $quotation)
    {
        $request->validate([
            'customer_id' => 'required|integer',
            'warehouse_id' => 'required|integer',
            'product_id' => 'required|array',
            'product_id.*' => 'integer',
            'qnt' => 'required|array',
            'price' => 'required|array',
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

        $quotation->update([
            'customer_id' => $request->customer_id,
            'warehouse_id' => $request->warehouse_id,
            'note' => $request->note,
            'total' => $total,
            'discount' => 0,
            'tax' => $tax,
            'net' => $net,
            'branch_id' => $request->branch_id ?? null,
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
}

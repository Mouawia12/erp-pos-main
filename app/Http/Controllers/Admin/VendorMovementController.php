<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller; 
use App\Models\Payment;
use App\Models\Purchase;
use App\Models\Sales;
use App\Models\VendorMovement;
use App\Models\WarehouseMovement;
use App\Http\Requests\StoreVendorMovementRequest;
use App\Http\Requests\UpdateVendorMovementRequest;
use Carbon\Carbon; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class VendorMovementController extends Controller
{
    public function addSaleMovement($id){
        $sale = Sales::find($id);
        VendorMovement::create([
            'vendor_id' => $sale->customer_id,
            'paid' => 0,
            'debit' => $sale->net,
            'credit' => 0, 
            'date' => Carbon::parse($sale->date)->format('Y-m-d'),
            'invoice_type' => 'Sales',
            'invoice_id' => $id,
            'invoice_no' => $sale->invoice_no,
            'paid_by' => '',
            'branch_id'=> $sale->branch_id
        ]);
    }

    public function addSalePaymentMovement($id){
        $payment = Payment::find($id);
        $sale = Sales::find($payment->sale_id);

        VendorMovement::create([
            'vendor_id' => $sale->customer_id,
            'paid' => 0,
            'debit' => 0,
            'credit' => $payment->amount, 
            'date' => Carbon::parse($payment->date)->format('Y-m-d'),
            'invoice_type' => 'Sale_Payment',
            'invoice_id' => $id,
            'invoice_no' => $sale->invoice_no,
            'paid_by' => $payment->paid_by,
            'branch_id'=> $sale->branch_id
        ]);
    }

    public function removeSalePaymentMovement($id){
        $vendorMovementId = VendorMovement::query()->where('invoice_id',$id)->get()->first();
        VendorMovement::destroy($vendorMovementId);
    }

    public function addPurchaseMovement($id){
        $purchase = Purchase::find($id);
        VendorMovement::create([
            'vendor_id' => $purchase->customer_id,
            'paid' => 0,
            'debit' => 0,
            'credit' => $purchase->net,
            'date' => Carbon::parse($purchase->date)->format('Y-m-d'),
            'invoice_type' => 'Purchases',
            'invoice_id' => $id,
            'invoice_no' => $purchase->invoice_no,
            'paid_by' => '',
            'branch_id'=> $purchase->branch_id
        ]);
    }

    public function removePurchaseMovement($id){
        $purchase = Purchase::find($id);
        $vendorMovementId = VendorMovement::query()->where('invoice_id',$id)->get()->first();
        VendorMovement::destroy($vendorMovementId);
    }

    public function addPurchasePaymentMovement($id){
        $payment = Payment::find($id);
        $purchase = Purchase::find($payment->purchase_id);

        VendorMovement::create([
            'vendor_id' => $purchase->customer_id,
            'paid' => 0,
            'debit' => $payment->amount,
            'credit' => 0,
            'date' => Carbon::parse($payment->date)->format('Y-m-d'),
            'invoice_type' => 'Purchase_Payment',
            'invoice_id' => $id,
            'invoice_no' => $purchase->invoice_no,
            'paid_by' => $payment->paid_by,
            'branch_id'=> $purchase->branch_id
        ]);
    }

    public function removePurchasePaymentMovement($id){
        $vendorMovementId = VendorMovement::query()->where('invoice_id',$id)->get()->first();
        VendorMovement::destroy($vendorMovementId);
    }

    public function syncWarehouseMovement($items,$type,$bill_id,$bill_no,$isMinus = true){ 
        $multy = $isMinus ? -1:1;
        if($items){
            foreach ($items as $item){
                $payload = [
                    'warehouse_id' => $item->warehouse_id,
                    'product_id' => $item->product_id,
                    'debit' => $type > 0 ? $item->quantity * $multy : 0,
                    'credit' => $type < 0 ? $item->quantity * $multy : 0 , 
                    'invoice_type' => $type > 0 ? 'Purchase' : 'Sales',
                    'invoice_id' => $bill_id,
                    'invoice_no' => $bill_no,
                    'user_id' => Auth::user() -> id,
                ];

                $this->storeWarehouseMovement($payload, $bill_no);
            }
        }
    }

    private function storeWarehouseMovement(array $payload, string $billNo): void
    {
        try {
            WarehouseMovement::create($payload);
        } catch (QueryException $e) {
            if ($this->isInvoiceNoIntegerError($e)) {
                $fallbackValue = $this->sanitizeInvoiceNumber($billNo);
                $payload['invoice_no'] = $fallbackValue;
                WarehouseMovement::create($payload);
                Log::warning('warehouse_movements.invoice_no forced to numeric fallback', [
                    'original' => $billNo,
                    'fallback' => $fallbackValue,
                ]);
            } else {
                throw $e;
            }
        }
    }

    private function isInvoiceNoIntegerError(QueryException $e): bool
    {
        return str_contains($e->getMessage(), 'warehouse_movements') &&
            str_contains($e->getMessage(), 'invoice_no') &&
            str_contains($e->getMessage(), 'Incorrect integer value');
    }

    private function sanitizeInvoiceNumber(string $invoiceNo): int
    {
        $numbersOnly = preg_replace('/\\D+/', '', $invoiceNo);
        return (int) ($numbersOnly !== '' ? $numbersOnly : 0);
    }

}

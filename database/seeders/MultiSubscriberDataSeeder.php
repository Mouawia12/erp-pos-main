<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MultiSubscriberDataSeeder extends Seeder
{
    public function run(): void
    {
        $subscribers = DB::table('subscribers')->get();
        $products = DB::table('products')->get();
        $today = Carbon::today();

        foreach ($subscribers as $sub) {
            $userId = DB::table('users')->where('subscriber_id',$sub->id)->value('id');
            $branchId = DB::table('branches')->where('subscriber_id',$sub->id)->value('id') ?? 1;
            $warehouseId = DB::table('warehouses')->where('subscriber_id',$sub->id)->value('id');
            $customerId = DB::table('companies')->where('subscriber_id',$sub->id)->where('group_id',3)->value('id');
            $supplierId = DB::table('companies')->where('subscriber_id',$sub->id)->where('group_id',4)->value('id');
            if(!$warehouseId || !$customerId || !$supplierId || !$products->count()){
                continue;
            }

            // فاتورة مبيعات لكل مشترك
            $saleInv = 'SWSI-'.$sub->id.'-'.str_pad(1,6,'0',STR_PAD_LEFT);
            $saleId = DB::table('sales')->updateOrInsert(
                ['invoice_no' => $saleInv],
                [
                    'date' => $today,
                    'invoice_no' => $saleInv,
                    'invoice_type' => 'tax_invoice',
                    'sale_id' => 0,
                    'customer_id' => $customerId,
                    'biller_id' => $userId,
                    'warehouse_id' => $warehouseId,
                    'note' => 'عينة مبيعات '.$sub->company_name,
                    'total' => 400,
                    'discount' => 0,
                    'tax' => 0,
                    'tax_excise' => 0,
                    'net' => 400,
                    'paid' => 0,
                    'sale_status' => 'completed',
                    'payment_status' => 'not_paid',
                    'locked_at' => $today,
                    'created_by' => $userId,
                    'pos' => 0,
                    'lista' => 0,
                    'profit' => 0,
                    'additional_service' => 0,
                    'branch_id' => $branchId,
                    'user_id' => $userId,
                    'status' => 1,
                    'subscriber_id' => $sub->id,
                ]
            );
            $saleId = DB::table('sales')->where('invoice_no',$saleInv)->value('id');
            $product = $products->first();
            DB::table('sale_details')->updateOrInsert(
                ['sale_id' => $saleId, 'product_id' => $product->id],
                [
                    'sale_id' => $saleId,
                    'product_code' => $product->code,
                    'product_id' => $product->id,
                    'quantity' => 2,
                    'price_unit' => 200,
                    'discount' => 0,
                    'price_with_tax' => 200,
                    'warehouse_id' => $warehouseId,
                    'unit_id' => $product->unit,
                    'tax' => 0,
                    'tax_excise' => 0,
                    'total' => 400,
                    'lista' => 0,
                    'profit' => 0,
                    'subscriber_id' => $sub->id,
                ]
            );

            // فاتورة مشتريات لكل مشترك
            $purchaseInv = 'PCH-'.$sub->id.'-'.str_pad(1,6,'0',STR_PAD_LEFT);
            $purchaseId = DB::table('purchases')->updateOrInsert(
                ['invoice_no' => $purchaseInv],
                [
                    'date' => $today,
                    'invoice_no' => $purchaseInv,
                    'customer_id' => $supplierId,
                    'biller_id' => $userId,
                    'warehouse_id' => $warehouseId,
                    'note' => 'عينة مشتريات '.$sub->company_name,
                    'total' => 250,
                    'discount' => 0,
                    'tax' => 0,
                    'net' => 250,
                    'paid' => 0,
                    'purchase_status' => 'completed',
                    'payment_status' => 'not_paid',
                    'created_by' => $userId,
                    'returned_bill_id' => 0,
                    'branch_id' => $branchId,
                    'user_id' => $userId,
                    'status' => 1,
                    'supplier_invoice_no' => null,
                    'supplier_invoice_copy' => null,
                    'cost_center' => null,
                    'tax_mode' => 'inclusive',
                    'subscriber_id' => $sub->id,
                ]
            );
            $purchaseId = DB::table('purchases')->where('invoice_no',$purchaseInv)->value('id');
            DB::table('purchase_details')->updateOrInsert(
                ['purchase_id' => $purchaseId, 'product_id' => $product->id],
                [
                    'purchase_id' => $purchaseId,
                    'product_code' => $product->code,
                    'product_id' => $product->id,
                    'quantity' => 3,
                    'cost_without_tax' => 80,
                    'cost_with_tax' => 80,
                    'warehouse_id' => $warehouseId,
                    'unit_id' => $product->unit,
                    'tax' => 0,
                    'total' => 240,
                    'net' => 240,
                    'returned_qnt' => 0,
                    'subscriber_id' => $sub->id,
                ]
            );
        }
    }
}

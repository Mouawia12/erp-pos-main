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
        $today = Carbon::today();

        foreach ($subscribers as $sub) {
            $userId = DB::table('users')->where('subscriber_id',$sub->id)->value('id');
            $branchId = DB::table('branches')->where('subscriber_id',$sub->id)->value('id') ?? 1;
            $warehouseId = DB::table('warehouses')->where('subscriber_id',$sub->id)->value('id');
            $products = DB::table('products')->where('subscriber_id',$sub->id)->get();

            // تأكيد وجود عملاء وموردين لكل مشترك (لأجل مخطط التوزيع)
            $clients = DB::table('companies')->where('subscriber_id',$sub->id)->where('group_id',3);
            $suppliers = DB::table('companies')->where('subscriber_id',$sub->id)->where('group_id',4);
            if($clients->count() < 2){
                for($i=$clients->count(); $i<2; $i++){
                    DB::table('companies')->updateOrInsert(
                        ['email' => "client-{$sub->id}-{$i}@example.com"],
                        [
                            'group_id' => 3,
                            'group_name' => 'عميل',
                            'customer_group_id' => 1,
                            'customer_group_name' => 'افتراضي',
                            'name' => 'عميل '.$sub->company_name.' '.($i+1),
                            'company' => $sub->company_name,
                            'vat_no' => $sub->tax_number,
                            'address' => $sub->address,
                            'city' => 'مدينة',
                            'state' => 'منطقة',
                            'postal_code' => '00000',
                            'country' => 'السعودية',
                            'phone' => '05123'.$sub->id.$i,
                            'status' => 1,
                            'cr_number' => $sub->cr_number,
                            'tax_number' => $sub->tax_number,
                            'subscriber_id' => $sub->id,
                        ]
                    );
                }
            }
            if($suppliers->count() < 2){
                for($i=$suppliers->count(); $i<2; $i++){
                    DB::table('companies')->updateOrInsert(
                        ['email' => "supplier-{$sub->id}-{$i}@example.com"],
                        [
                            'group_id' => 4,
                            'group_name' => 'مورد',
                            'customer_group_id' => 1,
                            'customer_group_name' => 'افتراضي',
                            'name' => 'مورد '.$sub->company_name.' '.($i+1),
                            'company' => $sub->company_name,
                            'vat_no' => $sub->tax_number,
                            'address' => $sub->address,
                            'city' => 'مدينة',
                            'state' => 'منطقة',
                            'postal_code' => '00000',
                            'country' => 'السعودية',
                            'phone' => '05987'.$sub->id.$i,
                            'status' => 1,
                            'cr_number' => $sub->cr_number,
                            'tax_number' => $sub->tax_number,
                            'subscriber_id' => $sub->id,
                        ]
                    );
                }
            }

            $customerId = DB::table('companies')->where('subscriber_id',$sub->id)->where('group_id',3)->value('id');
            $supplierId = DB::table('companies')->where('subscriber_id',$sub->id)->where('group_id',4)->value('id');

            if(!$warehouseId || !$customerId || !$supplierId || !$products->count()){
                continue;
            }

            // نماذج مبيعات ومشتريات خلال آخر 7 أيام لكل مشترك
            $product = $products->first();
            $dateRange = collect(range(0,6))->map(fn($i)=>$today->copy()->subDays($i));

            $baseSale = 300 + ($sub->id * 25);
            $basePurchase = 180 + ($sub->id * 20);

            foreach ($dateRange as $idx => $date) {
                $saleNet = $baseSale + ($idx * 30);
                $purchaseNet = $basePurchase + ($idx * 20);
                $saleInv = 'SWSI-'.$sub->id.'-'.str_pad($idx+1,6,'0',STR_PAD_LEFT);
                DB::table('sales')->updateOrInsert(
                    ['invoice_no' => $saleInv],
                    [
                        'date' => $date,
                        'invoice_no' => $saleInv,
                        'invoice_type' => 'tax_invoice',
                        'sale_id' => 0,
                        'customer_id' => $customerId,
                        'biller_id' => $userId,
                        'warehouse_id' => $warehouseId,
                        'note' => 'عينة مبيعات '.$sub->company_name.' يوم '.$date->format('Y-m-d'),
                        'total' => $saleNet,
                        'discount' => 0,
                        'tax' => 0,
                        'tax_excise' => 0,
                        'net' => $saleNet,
                        'paid' => 0,
                        'sale_status' => 'completed',
                        'payment_status' => 'not_paid',
                        'locked_at' => $date,
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
                DB::table('sale_details')->updateOrInsert(
                    ['sale_id' => $saleId, 'product_id' => $product->id],
                    [
                        'sale_id' => $saleId,
                        'product_code' => $product->code,
                        'product_id' => $product->id,
                        'quantity' => 1,
                        'price_unit' => $saleNet,
                        'discount' => 0,
                        'price_with_tax' => $saleNet,
                        'warehouse_id' => $warehouseId,
                        'unit_id' => $product->unit,
                        'tax' => 0,
                        'tax_excise' => 0,
                        'total' => $saleNet,
                        'lista' => 0,
                        'profit' => 0,
                        'subscriber_id' => $sub->id,
                    ]
                );

                $purchaseInv = 'PCH-'.$sub->id.'-'.str_pad($idx+1,6,'0',STR_PAD_LEFT);
                DB::table('purchases')->updateOrInsert(
                    ['invoice_no' => $purchaseInv],
                    [
                        'date' => $date,
                        'invoice_no' => $purchaseInv,
                        'customer_id' => $supplierId,
                        'biller_id' => $userId,
                        'warehouse_id' => $warehouseId,
                        'note' => 'عينة مشتريات '.$sub->company_name.' يوم '.$date->format('Y-m-d'),
                        'total' => $purchaseNet,
                        'discount' => 0,
                        'tax' => 0,
                        'net' => $purchaseNet,
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
                        'quantity' => 1,
                        'cost_without_tax' => $purchaseNet,
                        'cost_with_tax' => $purchaseNet,
                        'warehouse_id' => $warehouseId,
                        'unit_id' => $product->unit,
                        'tax' => 0,
                        'total' => $purchaseNet,
                        'net' => $purchaseNet,
                        'returned_qnt' => 0,
                        'subscriber_id' => $sub->id,
                    ]
                );
            }
        }
    }
}

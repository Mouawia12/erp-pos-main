<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller; 
use App\Models\Cashier;
use App\Models\Company;
use App\Models\Product;
use App\Models\Purchase;
use App\Http\Requests\StorePurchaseRequest;
use App\Http\Requests\UpdatePurchaseRequest;
use App\Models\PurchaseDetails;
use App\Models\SystemSettings;
use App\Models\ProductUnit;
use App\Models\Unit;
use App\Models\Warehouse; 
use App\Models\Branch;
use App\Models\Representative;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Purchase::join('warehouses','purchases.warehouse_id','=','warehouses.id')
            ->join('companies','purchases.customer_id','=','companies.id')
            ->select('purchases.*','warehouses.name as warehouse_name','companies.name as customer_name')
            ->where('returned_bill_id' , 0 )
            ->orderBy('id', 'desc')
            ->when(Auth::user()->subscriber_id ?? null,function($q,$sub){
                $q->where('purchases.subscriber_id',$sub);
            })
            ->get();

        if(!empty(Auth::user()->branch_id)) {
            $data = $data->where('branch_id', Auth::user()->branch_id); 
        }  

        return view('admin.purchases.index',compact('data'));
    }

    public function get_purchases_no($type,$branch_id){ 

        $bills = Purchase::where('branch_id',$branch_id)
            ->where('returned_bill_id',0)
            ->count(); 
       
        if($bills > 0){
            $id = $bills ;
        } else{
            $id = 0 ;
        }
       
        $settings = SystemSettings::all();
        $prefix = "";
        if(count($settings) > 0 && $settings[0]->purchase_prefix){
            $prefix = $settings[0]->purchase_prefix;
        }
        $prefixParts = array_filter([$prefix, $branch_id]);
        $prefix = ($prefixParts ? implode('-', $prefixParts) . '-' : '');
 
        if($type == 1){
            $no = $prefix . str_pad($id + 1, 6 , '0' , STR_PAD_LEFT);
            return $no ; 
        }else{
            $no = json_encode($prefix . str_pad($id + 1, 6 , '0' , STR_PAD_LEFT)) ;
            echo $no ;
            exit;
        }

    }

    public function get_return_purchases_no($type,$branch_id){ 

        $bills = Purchase::where('branch_id',$branch_id)
            ->where('returned_bill_id','>',0)
            ->count(); 
       
        if($bills > 0){
            $id = $bills ;
        } else{
            $id = 0 ;
        }
       
        $settings = SystemSettings::all();
        $prefix = "";
        if(count($settings) > 0 && $settings[0]->purchase_return_prefix){
            $prefix = $settings[0]->purchase_return_prefix;
        }
        $prefixParts = array_filter([$prefix, $branch_id]);
        $prefix = ($prefixParts ? implode('-', $prefixParts) . '-' : '');
 
        if($type == 1){
            $no = $prefix . str_pad($id + 1, 6 , '0' , STR_PAD_LEFT);
            return $no ; 
        }else{
            $no = json_encode($prefix . str_pad($id + 1, 6 , '0' , STR_PAD_LEFT)) ;
            echo $no ;
            exit;
        }

    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $siteContrller = new SystemController();
        $warehouses = $siteContrller->getAllWarehouses();
        $customers = $siteContrller->getAllVendors();
        $representatives = Representative::all();
        $setting = SystemSettings::all() -> first();
        $branches = Branch::where('status',1)->get();
        $defaultInvoiceType = $this->resolveDefaultInvoiceType();

        return view('admin.purchases.create',compact('warehouses','customers','representatives','setting','branches','defaultInvoiceType'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StorePurchaseRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){

        $request['invoice_no'] = $this->get_purchases_no( 1 , $request -> branch_id); 

        $request->validate([
            'invoice_no' => 'required|unique:purchases',
            'customer_id' => 'required|exists:companies,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'invoice_type' => ['nullable', Rule::in(['tax_invoice','non_tax_invoice'])],
            'payment_method' => ['nullable', Rule::in(['cash','credit'])],
        ]);

        $siteController = new SystemController();
        $total = 0;
        $tax = 0;
        $tax_excise = 0;
        $net = 0;

        $products = [];
        $qntProducts = [];
        foreach ($request->product_id as $index=>$id){
            $productDetails = Product::with('productTaxes')->find($id);
            $unitId = $request->unit_id[$index] ?? $productDetails->unit;
            $unitFactor = $request->unit_factor[$index] ?? 1;

            $qty = $request->qnt[$index] ?? 0;
            $costWithoutTax = $request->price_without_tax[$index];
            $costWithTaxInput = $request->price_with_tax[$index];
            $taxRate = $productDetails->totalTaxRate();
            $exciseRate = (float)($productDetails->tax_excise ?? 0);

            // If price_with_tax not provided, compute it
            $lineBase = $costWithoutTax * $qty;
            $lineTaxExcise = $lineBase * ($exciseRate / 100);
            $lineTax = $lineBase * ($taxRate / 100);
            $linePriceWithTax = $lineBase + $lineTax;
            $lineTotal = $lineBase;
            $lineNet = $linePriceWithTax;

            // Override with provided inclusive price if exists
            if (!empty($costWithTaxInput)) {
                $linePriceWithTax = $costWithTaxInput * $qty;
                $lineTax = $linePriceWithTax - $lineBase;
                $lineNet = $linePriceWithTax;
            }

            $product = [
                'purchase_id' => 0,
                'product_code' => $productDetails->code,
                'product_id' => $id,
                'variant_id' => $request->variant_id[$index] ?? null,
                'variant_color' => $request->variant_color[$index] ?? null,
                'variant_size' => $request->variant_size[$index] ?? null,
                'variant_barcode' => $request->variant_barcode[$index] ?? null,
                'quantity' => $qty,
                'cost_without_tax' => $costWithoutTax,
                'cost_with_tax' => $linePriceWithTax / max($qty,1),
                'warehouse_id' => $request->warehouse_id,
                'unit_id' => $unitId,
                'batch_no' => $request->batch_no[$index] ?? null,
                'production_date' => $request->production_date[$index] ?? null,
                'expiry_date' => $request->expiry_date[$index] ?? null,
                'unit_factor' => $unitFactor,
                'tax' => $lineTax,
                'total' => $lineTotal,
                'net' => $lineNet,
                'note' => !empty($request->item_note[$index]) ? trim($request->item_note[$index]) : null,
            ];

            $item = new Product();
            $item -> product_id = $id;
            $item -> quantity = $qty * $unitFactor;
            $item -> warehouse_id = $request->warehouse_id; 
            $qntProducts[] = $item ;

            $products[] = $product;
            $total +=$lineTotal;
            $tax +=$lineTax;
            $tax_excise += $lineTaxExcise;
            $net +=$lineNet;
        }

        $taxForInvoice = $tax;
        $netCalc = $total;
        if(($request->tax_mode ?? 'inclusive') === 'exclusive'){
            $netCalc += $taxForInvoice;
        }
 
        $purchase = Purchase::create([
            'date' => $request->bill_date,
            'invoice_no' => $request-> invoice_no,
            'supplier_invoice_no' => $request->supplier_invoice_no,
            'representative_id' => $request->representative_id,
            'supplier_name' => $request->supplier_name,
            'supplier_phone' => $request->supplier_phone,
            'cost_center' => $request->cost_center,
            'tax_mode' => $request->tax_mode ?? 'inclusive',
            'invoice_type' => $request->invoice_type ?? 'tax_invoice',
            'payment_method' => $request->payment_method ?? 'credit',
            'customer_id' => $request->customer_id,
            'biller_id' => Auth::id(),
            'warehouse_id' => $request->warehouse_id,
            'note' => $request->notes ?? '' ,
            'total' => $total,
            'discount' => 0,
            'tax' => $taxForInvoice,
            'net' => $netCalc,
            'paid' => 0,
            'purchase_status' => 'completed',
            'payment_status' => 'not_paid',
            'created_by' => Auth::id(),
            'branch_id'=> $request->branch_id ?? $siteController->getWarehouseById($request->warehouse_id)->branch_id,
            'status'=> 1,
            'user_id'=> Auth::user()->id
        ]);

        foreach ($products as $product){
            $product['purchase_id'] = $purchase->id;
            PurchaseDetails::create($product);
            $this->refreshProductPricing($product);
            $this->syncVariantStock($product, true);
        }

        if($request->hasFile('supplier_invoice_copy')){
            $path = $request->file('supplier_invoice_copy')->store('uploads/purchase_invoices','public');
            $purchase->update(['supplier_invoice_copy' => $path]);
        }

        $siteController->syncQnt($qntProducts,null , false);
        $clientController = new ClientMoneyController();
        $clientController->syncMoney($request->customer_id,0,$net);

        $vendorMovementController = new VendorMovementController();
        $vendorMovementController->addPurchaseMovement($purchase->id);
        $vendorMovementController->syncWarehouseMovement($qntProducts,1,$purchase->id,$purchase->invoice_no, false);

        $siteController->purchaseJournals($purchase->id);

        return redirect()->route('purchases');
    }

    private function refreshProductPricing(array $purchaseRow): void
    {
        $product = Product::with('productTaxes')->find($purchaseRow['product_id'] ?? null);
        if (!$product) {
            return;
        }

        $baseCost = $purchaseRow['cost_without_tax'] ?? $product->cost;
        $taxRateValue = $product->totalTaxRate();

        $margin = $product->profit_margin;

        $newPrice = $product->price;
        if ($margin !== null) {
            $newPrice = $baseCost * (1 + ($margin / 100));
            if ($product->price_includes_tax) {
                $newPrice = $newPrice * (1 + ($taxRateValue / 100));
            }
        }

        $priceLevels = [];
        for ($i = 1; $i <= 6; $i++) {
            $key = 'price_level_'.$i;
            if ($i === 1) {
                $priceLevels[$key] = $newPrice;
            } else {
                $priceLevels[$key] = $product->{$key} ?? $newPrice;
            }
        }

        $product->update(array_merge([
            'cost' => $baseCost,
            'price' => $newPrice,
        ], $priceLevels));
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

        return $systemDefault ?: 'tax_invoice';
    }

    private function syncVariantStock(array $purchaseRow, bool $isIncrease = true): void
    {
        if (empty($purchaseRow['variant_id'])) {
            return;
        }
        $variant = \App\Models\ProductVariant::find($purchaseRow['variant_id']);
        if (!$variant) {
            return;
        }
        $qty = $purchaseRow['quantity'] ?? 0;
        $variant->update([
            'quantity' => $variant->quantity + ($isIncrease ? $qty : -$qty),
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Purchase  $purchase
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $datas = DB::table('purchases')
            ->join('warehouses','purchases.warehouse_id','=','warehouses.id')
            ->join('companies','purchases.customer_id','=','companies.id')
            ->select('purchases.*','warehouses.name as warehouse_name','companies.name as customer_name' )
            ->where('purchases.id' , '=' , $id)
            ->when(Auth::user()->subscriber_id ?? null, function($q,$sub){
                $q->where('purchases.subscriber_id',$sub);
            })
            ->get();
            
        if(count($datas)){
            $data = $datas[0];
            $details = DB::table('purchase_details')
                -> join('products' , 'purchase_details.product_id' , '=' , 'products.id')
                -> select('purchase_details.*' , 'products.code' , 'products.name')
                ->where('purchase_details.purchase_id' , '=' , $id)-> get();
            // return  $details ;

            $vendor = Company::find($data->customer_id);
            $cashier = Cashier::get()->first();

            return view('admin.purchases.view',compact('data' , 'details','vendor','cashier'))->render();
        }


    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Purchase  $purchase
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $purchase = Purchase::find($id);
        if($purchase->net < 0){
            return redirect()->back();
        }

        $siteContrller = new SystemController();
        $warehouses = $siteContrller->getAllWarehouses();
        $customers = $siteContrller->getAllVendors();

        $purchaseItems = DB::table('purchase_details')
            ->join('products','products.id','=','purchase_details.product_id')
            ->select('purchase_details.*','products.name as product_name')
            ->where('purchase_id',$id)
            ->get();

        foreach ($purchaseItems as $purchaseItem){
            $unitsOptions = ProductUnit::join('units','units.id','=','product_units.unit_id')
                ->where('product_units.product_id',$purchaseItem->product_id)
                ->select('product_units.*','units.name as unit_name')
                ->get();

            if($unitsOptions->isEmpty()){
                $unitsOptions = collect([[
                    'unit_id' => $purchaseItem->unit_id,
                    'unit_name' => Unit::find($purchaseItem->unit_id)->name ?? '',
                    'price' => $purchaseItem->cost_without_tax,
                    'conversion_factor' => 1,
                    'barcode' => null
                ]]);
            }

            $purchaseItem->units_options = $unitsOptions->map(function($u){
                return [
                    'unit_id' => $u->unit_id,
                    'unit_name' => $u->unit_name,
                    'price' => $u->price,
                    'conversion_factor' => $u->conversion_factor ?? 1,
                    'barcode' => $u->barcode
                ];
            });

            $purchaseItem->selected_unit_id = $purchaseItem->unit_id;
            $selected = $purchaseItem->units_options->firstWhere('unit_id',$purchaseItem->unit_id);
            $purchaseItem->unit_factor = $selected['conversion_factor'] ?? 1;
        }


        $zeroItems = 0;
        foreach ($purchaseItems as $purchaseItem){
            $returnedQnt = $this->getAllProductReturnForSameInvoice($id,$purchaseItem->product_id);
            $purchaseItem->quantity = $purchaseItem->quantity + $returnedQnt;

            if($purchaseItem->quantity <= 0){
                $zeroItems +=1;
            }
        }

        if($zeroItems >= count($purchaseItems)){
            return redirect()->back();
        }
        //$purchaseItems = $purchaseItems->toJson();
       // return  $purchaseItems ;
        return view('admin.PurchasesReturn.create',compact('warehouses','customers','purchaseItems','id','purchase'));
    }


    public function purchase_return(){

        $data = Purchase::where('returned_bill_id' , '>' , 0 ) ->get();
        
        if(!empty(Auth::user()->branch_id)) {
            $data = $data->where('branch_id', Auth::user()->branch_id); 
        }  

        return view('admin.PurchasesReturn.index',compact('data'));
    }

    private function getAllProductReturnForSameInvoice($invoiceId,$productId){
        $totalQnt = 0;

        $allOtherPurchaseItems = DB::table('purchase_details')
            ->join('purchases','purchases.id','=','purchase_details.purchase_id')
            ->select('purchase_details.*')
            ->where('purchases.returned_bill_id',$invoiceId)
            ->where('purchase_details.product_id',$productId)
            ->get();

        foreach ($allOtherPurchaseItems as $item){
            $totalQnt += $item->quantity;
        }

        return $totalQnt;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatePurchaseRequest  $request
     * @param  \App\Models\Purchase  $purchase
     * @return \Illuminate\Http\Response
     */
    public function update(Request  $request, $billid)
    {
        $request['invoice_no'] = $this->get_return_purchases_no( 1 ,$request -> branch_id); 

        $validated = $request->validate([
            'invoice_no' => 'required|unique:purchases', 
            'customer_id' => 'required',
            'warehouse_id' => 'required'
        ]);
        
        $siteController = new SystemController();
        $total = 0;
        $tax = 0;
        $discount = 0;
        $net = 0;
        $lista = 0;
        $profit = 0;

        $products = array();
        $qntProducts = array();
        foreach ($request->product_id as $index=>$id){
            $productDetails = $siteController->getProductById($id);
            $unitId = $request->unit_id[$index] ?? $productDetails->unit;
            $unitFactor = $request->unit_factor[$index] ?? 1;
            $product = [
                'purchase_id' => 0,
                'product_code' => $productDetails->code,
                'product_id' => $id,
                'quantity' => $request->qnt[$index] * -1,
                'cost_without_tax' => $request->price_without_tax[$index] * -1,
                'cost_with_tax' => $request->price_with_tax[$index] * -1,
                'warehouse_id' => $request->warehouse_id,
                'unit_id' => $unitId,
                'batch_no' => $request->batch_no[$index] ?? null,
                'production_date' => $request->production_date[$index] ?? null,
                'expiry_date' => $request->expiry_date[$index] ?? null,
                'unit_factor' => $unitFactor,
                'tax' => $request->tax[$index] * -1,
                'total' => $request->total[$index] * -1,
                'net' => $request->net[$index] * -1,
                'note' => !empty($request->item_note[$index]) ? trim($request->item_note[$index]) : null,
            ];

            $item = new Product();
            $item -> product_id = $id;
            $item -> quantity = $request->qnt[$index]  * -1 * $unitFactor;
            $item -> warehouse_id = $request->warehouse_id ;
            $qntProducts[] = $item ;

            $products[] = $product;
            $total +=$request->total[$index];
            $tax +=$request->tax[$index];
            $net +=$request->net[$index];
        }

        $taxForInvoice = $tax;
        $netCalc = $total;
        if(($request->tax_mode ?? 'inclusive') === 'exclusive'){
            $netCalc += $taxForInvoice;
        }
        $subscriberId = Auth::user()->subscriber_id;

        $return = Purchase::create([
            'returned_bill_id' => $billid,
            'date' => $request->bill_date,
            'invoice_no' => $request-> invoice_no,
            'customer_id' => $request->customer_id,
            'representative_id' => $request->representative_id,
            'supplier_name' => $request->supplier_name,
            'supplier_phone' => $request->supplier_phone,
            'biller_id' => Auth::id(),
            'warehouse_id' => $request->warehouse_id,
            'note' => $request->notes ? $request->notes :'',
            'total' => $total * -1,
            'discount' => 0,
            'tax' => $taxForInvoice * -1,
            'net' => $netCalc * -1,
            'paid' => 0,
            'purchase_status' => 'completed',
            'payment_status' => 'not_paid',
            'branch_id'=> $request->branch_id ?? $siteController->getWarehouseById($request->warehouse_id)->branch_id,
            'user_id'=> Auth::user()->id,
            'created_by'=> Auth::user()->id,
            'subscriber_id'=> $subscriberId,
            'status' => 1,
            'tax_mode' => $request->tax_mode ?? 'inclusive',
            'invoice_type' => $request->invoice_type ?? 'tax_invoice',
            'payment_method' => $request->payment_method ?? 'credit',
        ]);

        foreach ($products as $product){
            $product['purchase_id'] = $return->id;
            PurchaseDetails::create($product);
        }

        $siteController->syncQnt($qntProducts,null , false);
        $clientController = new ClientMoneyController();
        $clientController->syncMoney($request->customer_id,0,$net*-1);

        $vendorMovementController = new VendorMovementController();
        $vendorMovementController->addPurchaseMovement($return->id);

        $siteController->purchaseJournals($return->id);

        return redirect()->route('purchase.return');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Purchase  $purchase
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $purchase = Purchase::find($id);
        $item = new Product();
        $qntProducts = array();
        $siteController = new SystemController();
        $net = 0 ;

        if($purchase){

            $details = PurchaseDetails::where('purchase_id' , '=' , $id) -> get();

            foreach ($details as $detail){
                $item = new Product();
                $item -> product_id = $detail -> product_id;
                $item -> quantity = $detail-> quantity  * -1;
                $item -> warehouse_id = $detail->warehouse_id ;
                $qntProducts[] = $item ;
                $net +=$detail->net;
                $detail -> delete();
            }
            
            $returns = Purchase::where('returned_bill_id' , '=' , $id) -> get();

            foreach ($returns as $return){
                $details = PurchaseDetails::where('purchase_id' , '=' , $return -> id) -> get();
                foreach ($details as $detail){
                    $item = new Product();
                    $item -> product_id = $detail -> product_id;
                    $item -> quantity = $detail-> quantity  * -1;
                    $item -> warehouse_id = $detail->warehouse_id ;
                    $qntProducts[] = $item ;
                    $net +=$detail->net;
                    $detail -> delete();
                }
                $return -> delete();
            }

            $siteController->syncQnt($qntProducts,null , false);
            $clientController = new ClientMoneyController();
            $clientController->syncMoney($purchase->customer_id,0,$net*-1);

            $vendorMovementController = new VendorMovementController();
            $vendorMovementController->removePurchaseMovement($purchase->id);
 
            $paymentController = new PaymentController();
            $paymentController->deleteAllPurchasePayments($purchase->id);

            $purchase -> delete();

            return redirect()->route('purchases')->with('success' ,  __('main.deleted'));

        }
    }

    public function getReturnNo($branch_id){ 

        $bills = Purchase::where('branch_id',$branch_id)
            ->where('returned_bill_id','>',0)
            ->count();
                    
        if($bills > 0){
            $id = $bills ;
        } else{
            $id = 0 ;
        }

        $settings = SystemSettings::all();
        $prefix = "";
        if(count($settings) > 0){
            if($settings[0] -> purchase_return_prefix)
                $prefix = $settings[0] -> purchase_return_prefix ;
            else
                $prefix = "" ;
        } else {
            $prefix = "";
        }

        $prefix = $prefix.'-'.$branch_id.'-';
  
        $no = json_encode($prefix . str_pad($id + 1, 6 , '0' , STR_PAD_LEFT)) ;
        echo $no ;
        exit;
    }

}

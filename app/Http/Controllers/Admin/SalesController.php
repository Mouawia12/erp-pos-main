<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller; 
use App\Models\Cashier;
use App\Models\Company;
use App\Models\Payment;
use App\Models\Product;
use App\Models\SaleDetails;
use App\Models\Sales;
use App\Models\CompanyInfo;
use App\Http\Requests\StoreSalesRequest;
use App\Http\Requests\UpdateSalesRequest;
use App\Models\SystemSettings;
use App\Models\PosSettings;
use App\Models\Warehouse;
use App\Models\Branch;
use App\Models\ProductUnit;
use App\Models\Unit;
use App\Models\Representative;
use App\Models\Promotion;
use App\Models\PromotionItem;
use App\Services\DocumentNumberService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use DataTables;

class SalesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = DB::table('sales as s')
            ->join('warehouses','s.warehouse_id','=','warehouses.id')
            ->join('branches','s.branch_id','=','branches.id')
            ->join('companies','s.customer_id','=','companies.id')
            ->leftJoin('representatives','representatives.id','=','s.representative_id')
            ->select('s.*','warehouses.name as warehouse_name','companies.name as customer_name','branches.branch_name','representatives.user_name as representative_name')
            ->selectRaw('(s.net - s.paid) as remain') 
            ->where('s.sale_id',0)
            ->when(Auth::user()->subscriber_id ?? null, function($q, $sub){
                $q->where('s.subscriber_id',$sub);
            })
            ->when($request->invoice_no, fn($q,$v)=>$q->where('s.invoice_no','like','%'.$v.'%'))
            ->when($request->customer_id, fn($q,$v)=>$q->where('s.customer_id',$v))
            ->when($request->representative_id, fn($q,$v)=>$q->where('s.representative_id',$v))
            ->when($request->branch_id, fn($q,$v)=>$q->where('s.branch_id',$v))
            ->when($request->date_from, fn($q,$v)=>$q->whereDate('s.date','>=',$v))
            ->when($request->date_to, fn($q,$v)=>$q->whereDate('s.date','<=',$v))
            ->when($request->item_search, function($q,$v){
                $q->whereExists(function($sub) use($v){
                    $sub->select(DB::raw(1))
                        ->from('sale_details as sd')
                        ->join('products as p','p.id','=','sd.product_id')
                        ->whereColumn('sd.sale_id','s.id')
                        ->where(function($inner) use($v){
                            $inner->where('p.code','like','%'.$v.'%')
                                  ->orWhere('p.name','like','%'.$v.'%');
                        });
                });
            })
            ->orderBy('s.id', 'desc');

        if(!empty(Auth::user()->branch_id)) {
            $query->where('s.branch_id', Auth::user()->branch_id); 
        }  

        $data = $query->get();

        if ($request->ajax()) { 
            return Datatables::of($data)->addIndexColumn()
                ->addColumn('action', function($row){   
                    if($row->sale_id == 0){
                        if(auth()->user()->can('عرض سند صرف')){  
                            $btn ='<a href="javascript:;" onclick="showPayments('.$row->id.')" type="button" class="btn btn-success">
                                        عرض المدفوعات
                                    </a>';
                        }

                        if(auth()->user()->can('اضافة سند صرف')){  
                            if(abs($row->net) - abs($row->paid) > 0) {
                                $btn = $btn.'<a href="javascript:;" onclick="addPayments('.$row->id.')" type="button" class="btn btn-primary">
                                                اضافة مدفوع
                                             </a>';
                            }
                        }
    
                        $btn = $btn.'<a type="button" class="btn btn-info"
                                       href='.route('print.sales',$row->id).'> 
                                        طباعة فاتورة 
                                     </a>';

                        if(auth()->user()->can('اضافة مردود مبيعات')){  
                            $btn = $btn.'<a href='.route('add_return',$row->id).' type="button" class="btn btn-warning">
                                            عمل مرتجع
                                        </a>';
                        } 
                    }else{
                        $btn = '<a type="button" class="btn btn-warning"
                                   href="javascript:;" onclick="view_sales('.$row->id.')"> 
                                    طباعة فاتورة 
                                </a>'; 
                    }  

                    return $btn; 
                }) 
                ->rawColumns(['action']) 
                ->make(true);
        } 
 
        $customers = Company::where('group_id',3)->get();
        $representatives = Representative::all();
        $branches = Branch::where('status',1)->get();

        return view('admin.sales.index',compact('customers','representatives','branches'));
    
    }

    private function resolvePromotionDiscount($productId, $variantId, $branchId, $qty, $basePrice): array
    {
        $today = now()->toDateString();
        $promoItem = PromotionItem::query()
            ->join('promotions','promotions.id','=','promotion_items.promotion_id')
            ->where('promotions.status','active')
            ->where(function($q) use ($today){
                $q->whereNull('promotions.start_date')->orWhere('promotions.start_date','<=',$today);
            })
            ->where(function($q) use ($today){
                $q->whereNull('promotions.end_date')->orWhere('promotions.end_date','>=',$today);
            })
            ->where(function($q) use ($branchId){
                if($branchId){
                    $q->where(function($qq) use ($branchId){
                        $qq->whereNull('promotions.branch_id')->orWhere('promotions.branch_id',$branchId);
                    });
                }
            })
            ->where('promotion_items.product_id',$productId)
            ->when($variantId,function($q) use ($variantId){
                $q->where(function($qq) use ($variantId){
                    $qq->whereNull('promotion_items.variant_id')->orWhere('promotion_items.variant_id',$variantId);
                });
            })
            ->first(['promotion_items.*']);

        if(!$promoItem){
            return ['discount_unit'=>0];
        }

        if($qty < $promoItem->min_qty){
            return ['discount_unit'=>0];
        }
        if($promoItem->max_qty && $qty > $promoItem->max_qty){
            return ['discount_unit'=>0];
        }

        if($promoItem->discount_type === 'amount'){
            return ['discount_unit'=>(float)$promoItem->discount_value];
        }

        // percent
        return ['discount_unit'=> $basePrice * ($promoItem->discount_value/100)];
    }
    private function syncVariantStock(array $products, bool $isReturn = false): void
    {
        foreach ($products as $row) {
            if (empty($row['variant_id'])) {
                continue;
            }
            $variant = \App\Models\ProductVariant::find($row['variant_id']);
            if (!$variant) {
                continue;
            }
            $delta = $row['quantity'] * ($isReturn ? -1 : 1);
            $variant->update(['quantity' => $variant->quantity - $delta]);
        }
    }


    public function get_sales_pos_no($type,$id){ 
        $warehouse = Warehouse::find($id);
        $service = app(DocumentNumberService::class);
        $settings = SystemSettings::first();
        $next = $service->next('sales', $warehouse->branch_id, $settings?->sales_prefix);

        if($type == 1){
            return $next;
        }

        echo json_encode($next);
        exit;
    }


    public function get_return_sales_pos_no($type,$id){ 

        if($type == 1){
            $warehouse = Warehouse::find($id);
            $bills = Sales::where('sale_id','>',0) 
                ->where('branch_id',$warehouse->branch_id)
                ->count();
            $branch_id = $warehouse->branch_id;
        }else{
            $bills = Sales::where('sale_id','>',0) 
                ->where('branch_id',$id)
                ->count();
            $branch_id = $id;
        }


        if($bills > 0){
            $id = $bills ;
        } else{
            $id = 0 ;
        }
       
        $settings = SystemSettings::all();
        $prefix = "";
        if(count($settings) > 0){
            if($settings[0] -> sales_return_prefix)
                $prefix = $settings[0] -> sales_return_prefix ;
            else
                $prefix = "" ;
        } else {
            $prefix = "";
        }

        $prefix = $prefix .'-'.$branch_id.'-';
 
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
        $customers = $siteContrller->getAllClients();
        $representatives = Representative::all();
        $settings = SystemSettings::with('currency') -> first();
        $branches = Branch::where('status',1)->get();
        $defaultInvoiceType = $this->resolveDefaultInvoiceType();

        return view('admin.sales.create',compact('warehouses','customers','representatives','settings','branches','defaultInvoiceType'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreSalesRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSalesRequest $request)
    {
        $request['invoice_no'] = $this->get_sales_pos_no( 1 , $request -> warehouse_id); 

        $validated = $request->validate([
            'invoice_no' => 'required|unique:sales', 
            'customer_id' => 'required',
            'warehouse_id' => 'required'
        ]);

        $billDate = now()->format('Y-m-d H:i:s');

        $siteController = new SystemController();
        $customer = Company::find($request->customer_id);
        $customerPriceLevel = $customer->price_level_id ?? null;
        $total = 0;
        $tax = 0;
        $tax_excise = 0;
        $discount = 0;
        $net = 0;
        $lista = 0;
        $profit = 0;

        $products = array();
        $qntProducts = array();

        foreach ($request->product_id as $index=>$id){ 
            
            $productDetails = Product::with('productTaxes')->find($id);
            $unitId = $request->unit_id[$index] ?? $productDetails->unit;
            $unitFactor = $request->unit_factor[$index] ?? 1;
            $basePrice = $request->price_unit[$index];
            if($customerPriceLevel){
                $col = 'price_level_'.$customerPriceLevel;
                if(!empty($productDetails->$col)){
                    $basePrice = $productDetails->$col;
                }
            }
            $promoDiscount = $this->resolvePromotionDiscount(
                $id,
                $request->variant_id[$index] ?? null,
                $request->branch_id ?? $siteController->getWarehouseById($request->warehouse_id)->branch_id,
                $request->qnt[$index],
                $basePrice
            );
            $discountPerUnit = $promoDiscount['discount_unit'];
            $basePrice = max($basePrice - $discountPerUnit, 0);
            $qty = $request->qnt[$index];
            $taxRate = $productDetails->totalTaxRate();
            $exciseRate = (float)($productDetails->tax_excise ?? 0);

            $lineBase = $basePrice * $qty;
            $lineTaxExcise = $lineBase * ($exciseRate / 100);
            if($productDetails->price_includes_tax){
                $lineTax = $lineBase - ($lineBase / (1 + ($taxRate/100)));
                $lineTotal = $lineBase - $lineTax;
                $linePriceWithTax = $lineBase;
            } else {
                $lineTax = $lineBase * ($taxRate/100);
                $lineTotal = $lineBase;
                $linePriceWithTax = $lineBase + $lineTax;
            }
            $lineNet = $linePriceWithTax;
            $product = [
                'sale_id' => 0,
                'product_code' => $productDetails->code,
                'product_id' => $id,
                'variant_id' => $request->variant_id[$index] ?? null,
                'variant_color' => $request->variant_color[$index] ?? null,
                'variant_size' => $request->variant_size[$index] ?? null,
                'variant_barcode' => $request->variant_barcode[$index] ?? null,
                'quantity' => $qty,
                'price_unit' => $basePrice,
                'discount' => ($discountPerUnit * $qty),
                'discount_unit' => $discountPerUnit,
                'price_with_tax' => $linePriceWithTax,
                'warehouse_id' => $request->warehouse_id,
                'unit_id' => $unitId,
                'unit_factor' => $unitFactor,
                'tax' => $lineTaxExcise > 0 ? $lineTax - $lineTaxExcise:$lineTax,
                'tax_excise' => $lineTaxExcise, 
                'total' => $lineTotal,
                'lista' => 0,
                'profit'=> ($basePrice - ($productDetails->cost * $unitFactor)) * $qty
            ];

            $item = new Product();
            $item -> product_id = $id;
            $item -> quantity = $request->qnt[$index] * $unitFactor;
            $item -> warehouse_id = $request->warehouse_id ;
            $qntProducts[] = $item ;

            $products[] = $product;
            $total += $lineTotal;
            $tax += $lineTax;
            $tax_excise += $lineTaxExcise;
            $net += $lineNet;
            $profit +=($basePrice - ($productDetails->cost * $unitFactor)) * $qty;
        }

        $taxForInvoice = $tax_excise > 0 ? ($tax - $tax_excise) : $tax;
        $net += $request -> additional_service ?? 0 ;
        $net -= $request->discount;
 
        $sale = Sales::create([
            'date' => $billDate,
            'invoice_no' => $request-> invoice_no,
            'invoice_type' => $request->invoice_type ?? 'tax_invoice',
            'cost_center' => $request->cost_center,
            'representative_id' => $request->representative_id,
            'tax_mode' => $request->tax_mode ?? 'inclusive',
            'customer_id' => $request->customer_id,
            'customer_name' => $request->customer_name,//pos
            'customer_phone' => $request->customer_phone,//pos
            'biller_id' => Auth::user()->id,
            'warehouse_id' => $request->warehouse_id,
            'note' => $request->notes ? $request->notes:'',
            'total' => $total,
            'discount' => $request->discount,
            'tax' => $taxForInvoice,
            'tax_excise' => $tax_excise,
            'net' => $net,
            'paid' => 0,
            'sale_status' => 'completed',
            'payment_status' => 'not_paid', 
            'pos' => $request -> has('POS') ? $request ->POS:0,
            'lista' => $lista,
            'profit'=> $profit,
            'additional_service' => $request -> additional_service ?? 0,
            'branch_id'=> $request->branch_id ?? $siteController->getWarehouseById($request->warehouse_id)->branch_id,
            'status'=> 1,
            'user_id'=> Auth::user()->id
        ]);

        foreach ($products as $product){
            $product['sale_id'] = $sale->id;
            SaleDetails::create($product);
        }

        $siteController->syncQnt($qntProducts,null);
        $this->syncVariantStock($products, false);
        $clientController = new ClientMoneyController();
        $clientController->syncMoney($request->customer_id,0,$net*-1);

        $vendorMovementController = new VendorMovementController();
        $vendorMovementController->addSaleMovement($sale->id);
        $vendorMovementController->syncWarehouseMovement($qntProducts,-1,$sale->id,$sale->invoice_no);

        $siteController->saleJournals($sale->id);

        $salePaymentController = new PaymentController();
        $salePaymentController->MakeSalePayment($request,$sale->id);
        
        if(!$request ->POS){
            //return redirect()->route('sales');
            return redirect()->route('print.sales',$sale->id);
        } else {
            return redirect()->route('pos');
            //return $this->print($sale->id);
        } 
        
    }

    public function sales_return_create()
    {
        $siteContrller = new SystemController(); 
        return view('admin.SalesReturn.show');
    }

    public function sales_return()
    {
        $data = Sales::where('sale_id','>',0)->get();
        if(!empty(Auth::user()->branch_id)) {
            $data = $data->where('branch_id', Auth::user()->branch_id); 
        }  
        return view('admin.SalesReturn.index',compact('data'));
    }

    public function returnSale($id)
    {
        $sale = Sales::find($id);
        if($sale->net < 0){
            return redirect()->back();
        }

        $siteContrller = new SystemController();
        $warehouses = $siteContrller->getAllWarehouses();
        $customers = $siteContrller->getAllClients();

        $saleItems = DB::table('sale_details')
            ->join('products','products.id','=','sale_details.product_id')
            ->join('sales','sales.id','=','sale_details.sale_id') 
            ->select('sale_details.*','products.name as product_name','sales.pos')
            ->where('sale_details.sale_id',$id)
            ->get();

        foreach ($saleItems as $saleItem){
            $unitsOptions = ProductUnit::join('units','units.id','=','product_units.unit_id')
                ->where('product_units.product_id',$saleItem->product_id)
                ->select('product_units.*','units.name as unit_name')
                ->get();

            if($unitsOptions->isEmpty()){
                $unitsOptions = collect([[
                    'unit_id' => $saleItem->unit_id,
                    'unit_name' => Unit::find($saleItem->unit_id)->name ?? '',
                    'price' => $saleItem->price_unit,
                    'conversion_factor' => 1,
                    'barcode' => null
                ]]);
            }

            $saleItem->units_options = $unitsOptions->map(function($u){
                return [
                    'unit_id' => $u->unit_id,
                    'unit_name' => $u->unit_name,
                    'price' => $u->price,
                    'conversion_factor' => $u->conversion_factor ?? 1,
                    'barcode' => $u->barcode
                ];
            });
            $saleItem->selected_unit_id = $saleItem->unit_id;
            $selected = $saleItem->units_options->firstWhere('unit_id',$saleItem->unit_id);
            $saleItem->unit_factor = $selected['conversion_factor'] ?? 1;
            $saleItem->price_withoute_tax = $saleItem->price_unit;
        }

        $zeroItems = 0;
        foreach ($saleItems as $saleItem){
            $returnedQnt = $this->getAllProductReturnForSameInvoice($id,$saleItem->product_id);
            $saleItem->quantity = $saleItem->quantity + $returnedQnt;

            if($saleItem->quantity <= 0){
                $zeroItems +=1;
            }
        }

        if($zeroItems >= count($saleItems)){
            return redirect()->back();
        }

        $saleItems = $saleItems->toJson();

        return view('admin.salesReturn.create',compact('warehouses','customers','saleItems','id','sale'));
    }


    private function getAllProductReturnForSameInvoice($invoiceId,$productId){
        $totalQnt = 0;

        $allOtherSaleItems = DB::table('sale_details')
            ->join('sales','sales.id','=','sale_details.sale_id')
            ->select('sale_details.*')
            ->where('sales.sale_id',$invoiceId)
            ->where('sale_details.product_id',$productId)
            ->when(Auth::user()->subscriber_id ?? null,function($q,$sub){
                $q->where('sale_details.subscriber_id',$sub);
            })
            ->get();

        foreach ($allOtherSaleItems as $item){

            $totalQnt += $item->quantity;
        }

        return $totalQnt;
    }

    public function show($id)
    {
        $datas = DB::table('sales')
            ->join('warehouses','sales.warehouse_id','=','warehouses.id')
            ->join('companies','sales.customer_id','=','companies.id')
            ->join('branches','sales.branch_id','=','branches.id')
            ->select('sales.*','warehouses.name as warehouse_name','companies.name as customer_name'
                     ,'branches.branch_name','branches.branch_phone','branches.branch_address','branches.cr_number','branches.tax_number as branch_tax_number','branches.manager_name as branch_manager','branches.contact_email as branch_email' )
            ->where('sales.id' , '=' , $id)
            ->when(Auth::user()->subscriber_id ?? null,function($q,$sub){
                $q->where('sales.subscriber_id',$sub);
            })
            ->get();

        if(count($datas)){
            $data = $datas[0];

            $details = DB::table('sale_details')
                ->join('products' , 'sale_details.product_id' , '=' , 'products.id')
                ->select('sale_details.*' , 'products.code' , 'products.name')
                ->where('sale_details.sale_id' , '=' , $id)
                ->when(Auth::user()->subscriber_id ?? null,function($q,$sub){
                    $q->where('sale_details.subscriber_id',$sub);
                })
                ->get();

            $payments = Payment::with('user') -> where('sale_id',$id)
                            ->where('sale_id','<>',null)
                            ->get();


            $vendor = Company::find($data->customer_id);
            $cashier = Cashier::first();
            $company = CompanyInfo::first();
            $settings = SystemSettings::first();

            if($datas[0]->pos == 1){   
                return view('admin.sales.printPos',compact('data' , 'details','vendor','cashier' , 'payments','company','settings' ))->render();
            } else {
                return view('admin.sales.print',compact('data' , 'details','vendor','cashier' , 'payments','company','settings' ))->render();
            }

            //return view('admin.sales.view',compact('data' , 'details','vendor','cashier' , 'payments','company' ))->render();
        }

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreSalesRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function storeReturn(StoreSalesRequest $request,$id)
    {
        $request['invoice_no'] = $this->get_return_sales_pos_no( 1 ,$request -> warehouse_id); 

        $validated = $request->validate([
            'invoice_no' => 'required|unique:sales', 
            'customer_id' => 'required',
            'warehouse_id' => 'required'
        ]);

        $siteController = new SystemController();
        $billDate = now()->format('Y-m-d');
        $total = 0;
        $tax = 0;
        $tax_excise = 0;
        $discount = 0;
        $net = 0;
        $lista = 0;
        $profit = 0;

        $products = array();
        $qntProducts = array();
        foreach ($request->product_id as $index=>$id1){
            $productDetails = Product::with('productTaxes')->find($id1);
            $unitId = $request->unit_id[$index] ?? $productDetails->unit;
            $unitFactor = $request->unit_factor[$index] ?? 1;
            $qty = $request->qnt[$index];
            $basePrice = $request->price_unit[$index];
            $taxRate = $productDetails->totalTaxRate();
            $exciseRate = (float)($productDetails->tax_excise ?? 0);

            $lineBase = $basePrice * $qty;
            $lineTaxExcise = $lineBase * ($exciseRate / 100);
            if($productDetails->price_includes_tax){
                $lineTax = $lineBase - ($lineBase / (1 + ($taxRate/100)));
                $linePriceWithTax = $lineBase;
                $lineTotal = $lineBase - $lineTax;
            } else {
                $lineTax = $lineBase * ($taxRate/100);
                $lineTotal = $lineBase;
                $linePriceWithTax = $lineBase + $lineTax;
            }
            $product = [
                'sale_id' => 0,
                'product_code' => $productDetails->code,
                'product_id' => $id1,
                'variant_id' => $request->variant_id[$index] ?? null,
                'variant_color' => $request->variant_color[$index] ?? null,
                'variant_size' => $request->variant_size[$index] ?? null,
                'variant_barcode' => $request->variant_barcode[$index] ?? null,
                'quantity' => $qty * -1,
                'price_unit' => $basePrice * -1,
                'discount_unit' => 0,
                'price_with_tax' => $linePriceWithTax * -1,
                'warehouse_id' => $request->warehouse_id,
                'unit_id' => $unitId,
                'unit_factor' => $unitFactor,
                'tax' => $lineTax * -1,
                'tax_excise' => $lineTaxExcise * -1, 
                'total' => $lineTotal * -1,
                'lista' => 0,
                'profit'=> (($basePrice - ($productDetails->cost * $unitFactor)) * $qty) * -1
            ];

            $item = new Product();
            $item -> product_id = $id1;
            $item -> quantity = $qty  * -1 * $unitFactor;
            $item -> warehouse_id = $request->warehouse_id ;
            $qntProducts[] = $item ;

            $products[] = $product;
            $total +=$lineTotal;
            $tax +=$lineTax;
            $tax_excise +=$lineTaxExcise;
            $profit +=($basePrice - ($productDetails->cost * $unitFactor)) * $qty;
        }

        $taxForInvoice = $tax_excise > 0 ? ($tax - $tax_excise) : $tax;
        $net = $total + $taxForInvoice + $tax_excise;

        $sale = Sales::create([
            'sale_id' => $id,
            'date' => $billDate,
            'invoice_no' => $request-> invoice_no,
            'customer_id' => $request->customer_id,
            'representative_id' => $request->representative_id,
            'biller_id' => Auth::id(),
            'warehouse_id' => $request->warehouse_id,
            'note' => $request->notes ? $request->notes :'',
            'total' => $total * -1,
            'discount' => $request->discount * -1, 
            'tax' => $taxForInvoice * -1,
            'tax_excise' => $tax_excise * -1 , 
            'net' => ($net - $request->discount)* -1 ,
            'paid' => 0,
            'sale_status' => 'completed',
            'payment_status' => 'not_paid', 
            'pos' => 0,
            'lista' => $lista * -1,
            'profit'=> $profit * -1,
            'branch_id'=> $request->branch_id ?? $siteController->getWarehouseById($request->warehouse_id)->branch_id,
            'user_id'=> Auth::user()->id
        ]);

        foreach ($products as $product){
            $product['sale_id'] = $sale->id;
            SaleDetails::create($product);
        }

        $siteController->syncQnt($qntProducts,null);
        $this->syncVariantStock($products, true);
        $clientController = new ClientMoneyController();
        $clientController->syncMoney($request->customer_id,0,$net);

        $vendorMovementController = new VendorMovementController();
        $vendorMovementController->addSaleMovement($sale->id);

        $siteController->saleJournals($sale->id);

        return redirect()->route('sales.return');
    }

    
    public function get_sale_no($branch_id){ 
        $bills = Sales::where('sale_id' , 0 )
                    ->where('branch_id',$branch_id) 
                    ->count();

        if($bills > 0){
            $id = $bills ;
        } else{
            $id = 0 ;
        }
            
        $settings = SystemSettings::all();
        $prefix = "";

        if(count($settings) > 0){
            if($settings[0] -> sales_prefix)
                $prefix = $settings[0] -> sales_prefix ;
            else
                $prefix = "" ;
        } else {
            $prefix = "";
        }

        $prefix = $prefix .'-'.$branch_id.'-';
   
        $no = json_encode($prefix . str_pad($id + 1, 6 , '0' , STR_PAD_LEFT)) ;
         echo $no ;
         exit;
    }
 
    public function pos(){
        $siteContrller = new SystemController();
        $vendors = Company::where('group_id' , '=' , 3) -> get();
        $warehouses = $siteContrller->getAllWarehouses();
        $settings = SystemSettings::with('currency') ->first();
        $representatives = Representative::all();
        $defaultInvoiceType = $this->resolveDefaultInvoiceType();
        $posSettings = PosSettings::first();
        $posMode = optional($posSettings)->pos_mode ?? 'classic';
     
       return view('admin.sales.pos' , compact('vendors' , 'warehouses' , 'settings','representatives','defaultInvoiceType','posSettings','posMode'));
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

    public function getLastSalesBill(){
        $bills = Sales::orderBy('id', 'desc')
                    ->where('pos',1)
                    ->limit(1);
        if($bills){
            echo json_encode ($bills ->first()); 
        } 
        exit;
    }

    public function print_last_pos(){
        $bills = Sales::orderBy('id', 'desc') -> where('pos' , '<>' , 0)->get();
       if(count($bills) > 0){
           $bill = $bills -> first();
           return $this -> show($bill -> id);
       }
    }

    public function print(Request $request, $id)
    {
        $datas = DB::table('sales')
            ->join('warehouses','sales.warehouse_id','=','warehouses.id')
            ->join('companies','sales.customer_id','=','companies.id')
            ->join('branches','sales.branch_id','=','branches.id')
            ->select('sales.*','warehouses.name as warehouse_name','companies.name as customer_name'
                     ,'branches.branch_name','branches.branch_phone','branches.branch_address' )
            ->where('sales.id' , '=' , $id)
            ->get();

        if(count($datas)){
            $data = $datas[0];

            $details = DB::table('sale_details')
                ->join('products','sale_details.product_id','=','products.id')
                ->select('sale_details.*', 'products.code','products.name','products.tax as taxRate','products.tax_excise as taxExciseRate')
                ->where('sale_details.sale_id','=', $id)
                ->get();

            $payments = Payment::with('user') -> where('sale_id',$id)
                ->where('sale_id','<>',null)
                ->get();

            $vendor = Company::find($data->customer_id);
            $cashier = Cashier::first();
            $company = CompanyInfo::first();
            $settings = SystemSettings::first();

            if($datas[0]->pos == 1){ 
                return view('admin.sales.printPos',compact('data','details','vendor','cashier','payments','company','settings'))->render();
            } elseif($request->get('format') === 'a5'){
                return view('admin.sales.printA5',compact('data','details','vendor','cashier','payments','company','settings'))->render();
            } else {
                return view('admin.sales.print',compact('data','details','vendor','cashier','payments','company','settings'))->render();
            }
           
        }

    }

    public function get_sales($code)
    {
        $single = $this->getSingleSales($code);

        if($single){ 
            echo json_encode([$single]);
            exit;
        }else{
            if(!empty(Auth::user()->branch_id)) {
                $sale = Sales::where('invoice_no' , 'like' , '%'.$code.'%') 
                ->where('sale_id' , 0) 
                ->where('branch_id', Auth::user()->branch_id)
                ->limit(5)
                ->get();
            }else{
                $sale = Sales::where('invoice_no' , 'like' , '%'.$code.'%') 
                ->where('sale_id' , 0) 
                ->limit(5)
                ->get();
            }

            echo json_encode ($sale);
            exit;
        }

    }

    private function getSingleSales($code){
        if(!empty(Auth::user()->branch_id)) {
            return Sales::where('invoice_no', '=' , $code)
            ->where('sale_id' , 0) 
            ->where('branch_id', Auth::user()->branch_id)
            ->first();
        }else{
            return Sales::where('invoice_no', '=' , $code)
            ->where('sale_id' , 0) 
            ->first(); 
        }
 
    }   
}

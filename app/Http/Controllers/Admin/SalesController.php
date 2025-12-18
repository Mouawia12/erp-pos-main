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
use App\Models\Subscriber;
use App\Models\WarehouseProducts as WarehouseProductModel;
use App\Services\DocumentNumberService;
use App\Services\ZatcaIntegration\ZatcaDocumentService;
use App\Jobs\SendZatcaInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Carbon;
use DataTables;
use Salla\ZATCA\GenerateQrCode;
use Salla\ZATCA\Tags\InvoiceDate;
use Salla\ZATCA\Tags\InvoiceTaxAmount;
use Salla\ZATCA\Tags\InvoiceTotalAmount;
use Salla\ZATCA\Tags\Seller;
use Salla\ZATCA\Tags\TaxNumber;

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
            ->when($request->vehicle_plate, fn($q,$v)=>$q->where('s.vehicle_plate','like','%'.$v.'%'))
            ->when($request->vehicle_name, fn($q,$v)=>$q->where('s.vehicle_name','like','%'.$v.'%'))
            ->when($request->vehicle_color, fn($q,$v)=>$q->where('s.vehicle_color','like','%'.$v.'%'))
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

        $data = $query->get()->transform(function($row){
            $numericFields = ['net','discount','tax','total','paid','remain'];
            foreach ($numericFields as $field){
                if(isset($row->{$field})){
                    $row->{$field} = round((float)$row->{$field}, 3);
                } else {
                    $row->{$field} = 0;
                }
            }
            $row->remain = round((float)$row->net - (float)$row->paid, 3);
            return $row;
        });

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
 
        $subscriberId = Auth::user()?->subscriber_id;
        $settings = SystemSettings::query()
            ->when($subscriberId, fn ($q) => $q->where('subscriber_id', $subscriberId))
            ->first();
        $enableVehicleFeatures = (bool) optional($settings)->enable_vehicle_features;

        $customers = Company::where('group_id',3)->get();
        $branches = Branch::where('status',1)->get();
        $representatives = Representative::all();

        return view('admin.sales.index',compact('customers','representatives','branches','enableVehicleFeatures'));
    
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
        $allowNegativeStock = $siteContrller->allowSellingWithoutStock();
        $walkInCustomer = Company::ensureWalkInCustomer(Auth::user()->subscriber_id ?? null);
        $enableVehicleFeatures = (bool) optional($settings)->enable_vehicle_features;
        if ($walkInCustomer) {
            $customers = $customers->sortByDesc(function ($customer) use ($walkInCustomer) {
                return $customer->id === $walkInCustomer->id ? 1 : 0;
            })->values();
        }

        return view('admin.sales.create',compact('warehouses','customers','representatives','settings','branches','defaultInvoiceType','allowNegativeStock','walkInCustomer','enableVehicleFeatures'));
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

        $settings = SystemSettings::query()
            ->when(Auth::user()?->subscriber_id, fn($q,$sub)=>$q->where('subscriber_id',$sub))
            ->first();
        $enableVehicleFeatures = (bool) optional($settings)->enable_vehicle_features;

        $baseRules = [
            'invoice_no' => 'required|unique:sales', 
            'customer_id' => 'required',
            'warehouse_id' => 'required',
        ];
        if($enableVehicleFeatures){
            $baseRules['vehicle_plate'] = 'nullable|string|max:191';
            $baseRules['vehicle_name'] = 'nullable|string|max:191';
            $baseRules['vehicle_color'] = 'nullable|string|max:191';
            $baseRules['vehicle_odometer'] = 'nullable|numeric|min:0';
        } else {
            $request->merge([
                'vehicle_plate' => null,
                'vehicle_name' => null,
                'vehicle_color' => null,
                'vehicle_odometer' => null,
            ]);
        }

        $validated = $request->validate($baseRules);

        $billDate = now()->format('Y-m-d H:i:s');

        // لا يوجد فاتورة أب لنستنسخ بياناتها في إنشاء فاتورة جديدة، استخدم القيم القادمة من الطلب فقط
        $serviceDefaults = [
            'service_mode' => $request->service_mode ?? null,
            'session_location' => $request->session_location ?? null,
            'session_type' => $request->session_type ?? null,
        ];

        $subscriberId = Auth::user()->subscriber_id ?? null;
        $siteController = new SystemController();
        $allowNegativeStock = $siteController->allowSellingWithoutStock();
        $customer = Company::find($request->customer_id);
        if (! $customer) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'العميل المحدد غير موجود');
        }
        $walkInCustomer = Company::ensureWalkInCustomer($subscriberId);
        $isWalkInCustomer = $walkInCustomer && (int) $customer->id === (int) $walkInCustomer->id;
        if ($isWalkInCustomer) {
            $request->validate([
                'customer_name' => 'required|string|max:191',
                'customer_phone' => 'nullable|string|max:191',
            ]);
        }
        $customerPriceLevel = $customer->price_level_id ?? null;
        $warehouseMeta = $siteController->getWarehouseById($request->warehouse_id);
        if (! $warehouseMeta) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'المستودع المحدد غير متاح');
        }
        if (empty($request->product_id) || !is_array($request->product_id)) {
            return redirect()->back()
                ->withInput()
                ->with('error', __('main.invoice_details_required') ?? 'يجب إضافة صنف واحد على الأقل للفاتورة');
        }
        if (count($request->product_id) !== count($request->qnt ?? [])) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'عدد الأصناف لا يطابق عدد الكميات المدخلة');
        }

        Log::info('sale_store_attempt', [
            'user_id' => Auth::id(),
            'customer_id' => $request->customer_id,
            'warehouse_id' => $request->warehouse_id,
            'branch_selected' => $request->branch_id,
            'products_count' => count($request->product_id),
        ]);

        [$resolvedBranchId, $subscriberId] = $this->resolveBranchAndSubscriber($request, $warehouseMeta, $customer);
        if (! $resolvedBranchId || ! $subscriberId) {
            return redirect()->back()
                ->withInput()
                ->with('error', __('main.branch_required') ?? 'يجب اختيار فرع صالح للحفظ');
        }
        $total = 0;
        $tax = 0;
        $tax_excise = 0;
        $discount = 0;
        $net = 0;
        $lista = 0;
        $profit = 0;

        $products = array();
        $qntProducts = array();

        $shouldDispatchZatcaInvoice = false;

        DB::beginTransaction();
        $zatcaWarning = null;

        try {
        foreach ($request->product_id as $index=>$id){ 
            
            $productDetails = Product::with('productTaxes')->find($id);
            if (! $productDetails) {
                throw ValidationException::withMessages([
                    "product_id.$index" => __('main.product_not_found') ?? 'تم اختيار صنف غير موجود'
                ]);
            }
            $unitId = $request->unit_id[$index] ?? $productDetails->unit;
            $unitFactor = $request->unit_factor[$index] ?? 1;
            $basePrice = (float) ($request->original_price[$index] ?? $request->price_unit[$index]);
            if($customerPriceLevel){
                $col = 'price_level_'.$customerPriceLevel;
                if(!empty($productDetails->$col)){
                    $basePrice = (float) $productDetails->$col;
                }
            }
            $manualDiscount = isset($request->discount_unit[$index]) ? (float)$request->discount_unit[$index] : 0;
            if($manualDiscount < 0){
                $manualDiscount = 0;
            }
            if($manualDiscount > $basePrice){
                $manualDiscount = $basePrice;
            }
            $basePrice -= $manualDiscount;
            if (! $allowNegativeStock) {
                $warehouseStock = $this->resolveWarehouseProductStock(
                    $request->warehouse_id,
                    $id,
                    $productDetails,
                    $subscriberId
                );
                $availableQty = $warehouseStock?->quantity ?? 0;
                $requiredQty = $request->qnt[$index] * $unitFactor;
                if ($availableQty < $requiredQty) {
                    throw ValidationException::withMessages([
                        "product_id.$index" => __('main.insufficient_stock', ['item' => $productDetails->name])
                    ]);
                }
            }

            $promoDiscount = $this->resolvePromotionDiscount(
                $id,
                $request->variant_id[$index] ?? null,
                $request->branch_id ?? optional($warehouseMeta)->branch_id,
                $request->qnt[$index],
                $basePrice
            );
            $discountPerUnit = $manualDiscount + $promoDiscount['discount_unit'];
            $basePrice = max($basePrice - $promoDiscount['discount_unit'], 0);
            $qty = $request->qnt[$index];
            $taxRate = $productDetails->price_includes_tax ? 0 : $productDetails->totalTaxRate();
            $exciseRate = $productDetails->price_includes_tax ? 0 : (float)($productDetails->tax_excise ?? 0);

            $lineBase = $basePrice * $qty;
            $lineTaxExcise = $lineBase * ($exciseRate / 100);
            if($productDetails->price_includes_tax){
                $lineTax = 0;
                $lineTotal = $lineBase;
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
                'profit'=> ($basePrice - ($productDetails->cost * $unitFactor)) * $qty,
                'note' => !empty($request->item_note[$index]) ? trim($request->item_note[$index]) : null,
                'subscriber_id' => $subscriberId,
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
 
        $saleData = [
            'date' => $billDate,
            'invoice_no' => $request-> invoice_no,
            'invoice_type' => $request->invoice_type ?? 'tax_invoice',
            'cost_center' => $request->cost_center,
            'representative_id' => $request->representative_id,
            'tax_mode' => $request->tax_mode ?? 'inclusive',
            'customer_id' => $request->customer_id,
            'customer_name' => $isWalkInCustomer ? $request->customer_name : $customer->name,
            'customer_phone' => $isWalkInCustomer ? $request->customer_phone : $customer->phone,
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
            'branch_id'=> $resolvedBranchId,
            'status'=> 1,
            'user_id'=> Auth::user()->id,
            'created_by'=> Auth::user()->id,
            'sale_id' => 0,
            'subscriber_id' => $subscriberId,
            'vehicle_plate' => $request->vehicle_plate ? trim($request->vehicle_plate) : null,
            'vehicle_name' => $request->vehicle_name ? trim($request->vehicle_name) : null,
            'vehicle_color' => $request->vehicle_color ? trim($request->vehicle_color) : null,
            'vehicle_odometer' => $request->vehicle_odometer,
        ];

        $sale = Sales::create(array_merge($saleData, $this->resolveServiceMeta($request, $serviceDefaults)));

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

        if (config('zatca.enabled')) {
            app(ZatcaDocumentService::class)->initDocumentForSale($sale);
            $sale->loadMissing('branch.zatcaSetting');
            $branchSetting = $sale->branch?->zatcaSetting;

            if ($branchSetting && $branchSetting->getCertificateBundle()) {
                $shouldDispatchZatcaInvoice = true;
            } else {
                $zatcaWarning = __('main.zatca_branch_missing_credentials') ?? 'ZATCA integration is enabled but this branch does not have onboarding credentials yet.';
            }
        }

        DB::commit();
        if ($shouldDispatchZatcaInvoice) {
            try {
                SendZatcaInvoice::dispatch($sale->id);
            } catch (\Throwable $dispatchException) {
                Log::error('zatca_dispatch_failed', [
                    'sale_id' => $sale->id,
                    'message' => $dispatchException->getMessage(),
                ]);
                $zatcaWarning = __('main.zatca_send_failed') ?? 'Invoice saved but sending to ZATCA failed. Please resend later.';
            }
        }

        $redirect = !$request->POS
            ? redirect()->route('sales')
            : redirect()->route('pos');

        $redirect = $redirect->with('success', __('main.created') ?? 'تم حفظ الفاتورة بنجاح');

        if ($zatcaWarning) {
            $redirect = $redirect->with('warning', $zatcaWarning);
        }

        return $redirect;
        } catch (ValidationException $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Failed to store sale invoice', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', $this->resolveStoreErrorMessage($e));
        }
        
    }

    protected function resolveStoreErrorMessage(\Throwable $e): string
    {
        if ($this->isZatcaIcvDuplicate($e)) {
            return __('main.zatca_icv_duplicate') ?? 'Unable to create a new ZATCA document because the previous sequence is still pending.';
        }

        return __('main.error_occured') ?? $e->getMessage();
    }

    protected function isZatcaIcvDuplicate(\Throwable $e): bool
    {
        if ($e instanceof QueryException && $e->getCode() === '23000' && str_contains($e->getMessage(), 'zatca_documents_scope_icv_unique')) {
            return true;
        }

        $previous = $e->getPrevious();
        if ($previous) {
            return $this->isZatcaIcvDuplicate($previous);
        }

        return false;
    }

    private function resolveBranchAndSubscriber(Request $request, ?Warehouse $warehouse = null, ?Company $customer = null): array
    {
        $user = Auth::user();
        $branchId = $user->branch_id
            ?? $request->branch_id
            ?? $warehouse?->branch_id
            ?? $customer->branch_id
            ?? null;

        $branch = $branchId ? Branch::find($branchId) : null;

        $subscriberId = $user->subscriber_id
            ?? $branch?->subscriber_id
            ?? $customer->subscriber_id
            ?? $warehouse?->subscriber_id
            ?? null;

        return [$branchId, $subscriberId];
    }

    private function resolveWarehouseProductStock(int $warehouseId, int $productId, Product $product, ?int $subscriberId)
    {
        $record = WarehouseProductModel::query()
            ->where('warehouse_id', $warehouseId)
            ->where('product_id', $productId)
            ->first();

        if ($record) {
            return $record;
        }

        $initialQty = $product->quantity ?? 0;
        $newRecord = WarehouseProductModel::create([
            'warehouse_id' => $warehouseId,
            'product_id' => $productId,
            'quantity' => $initialQty,
            'cost' => $product->cost,
        ]);

        Log::warning('warehouse_product_autocreated', [
            'user_id' => Auth::id(),
            'subscriber_id' => $subscriberId,
            'warehouse_id' => $warehouseId,
            'product_id' => $productId,
            'initial_quantity' => $initialQty,
        ]);

        return $newRecord;
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
        $payload = $this->getPrintPayload((int) $id);

        if (! $payload) {
            abort(404);
        }

        $view = $payload['data']->pos == 1 ? 'admin.sales.printPos' : 'admin.sales.print';

        return view($view, $payload)->render();
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
            $taxRate = $productDetails->price_includes_tax ? 0 : $productDetails->totalTaxRate();
            $exciseRate = $productDetails->price_includes_tax ? 0 : (float)($productDetails->tax_excise ?? 0);

            $lineBase = $basePrice * $qty;
            $lineTaxExcise = $lineBase * ($exciseRate / 100);
            if($productDetails->price_includes_tax){
                $lineTax = 0;
                $linePriceWithTax = $lineBase;
                $lineTotal = $lineBase;
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

        $saleData = [
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
            'created_by' => Auth::id(),
            'status' => 1,
            'branch_id'=> $request->branch_id ?? $siteController->getWarehouseById($request->warehouse_id)->branch_id,
            'user_id'=> Auth::user()->id
        ];

        $sale = Sales::create(array_merge($saleData, $this->resolveServiceMeta($request)));

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

        return redirect()->route('sales.return')
            ->with('success', 'تم حفظ مرتجع المبيعات بنجاح');
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

    public function customerVehicles(Company $customer)
    {
        if (! Auth::user()->can('عرض مبيعات')) {
            abort(403);
        }

        $vehicles = Sales::query()
            ->select('vehicle_plate', 'vehicle_odometer','vehicle_name','vehicle_color')
            ->where('customer_id', $customer->id)
            ->whereNotNull('vehicle_plate')
            ->orderByDesc('id')
            ->limit(50)
            ->get()
            ->filter(fn ($row) => filled($row->vehicle_plate))
            ->unique('vehicle_plate')
            ->values();

        return response()->json($vehicles);
    }

    public function pos(){
        $siteContrller = new SystemController();
        $vendors = Company::where('group_id' , '=' , 3) -> get();
        $warehouses = $siteContrller->getAllWarehouses();
        $settings = SystemSettings::with('currency') ->first();
        $defaultInvoiceType = $this->resolveDefaultInvoiceType();
        $posSettings = PosSettings::first();
        $posMode = optional($posSettings)->pos_mode ?? 'classic';
        $defaultWarehouseId = optional($warehouses->first())->id;
        if($settings && $settings->branch_id){
            $preferred = $warehouses->firstWhere('id', $settings->branch_id);
            if($preferred){
                $defaultWarehouseId = $preferred->id;
            }
        }
        $allowNegativeStock = $siteContrller->allowSellingWithoutStock();
     
       return view('admin.sales.pos' , compact('vendors' , 'warehouses' , 'settings','defaultInvoiceType','posSettings','posMode','defaultWarehouseId','allowNegativeStock'));
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
        $payload = $this->getPrintPayload((int) $id);

        if (! $payload) {
            abort(404);
        }

        $format = $request->get('format');

        if ($format === 'a5') {
            return view('admin.sales.printA5', $payload)->render();
        }

        if ($payload['data']->pos == 1) {
            return view('admin.sales.printPos', $payload)->render();
        }

        return view('admin.sales.print', $payload)->render();
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

    private function getPrintPayload(int $id): ?array
    {
        $data = DB::table('sales')
            ->join('warehouses','sales.warehouse_id','=','warehouses.id')
            ->join('companies','sales.customer_id','=','companies.id')
            ->join('branches','sales.branch_id','=','branches.id')
            ->select(
                'sales.*',
                'warehouses.name as warehouse_name',
                'companies.name as customer_name',
                'branches.branch_name',
                'branches.branch_phone',
                'branches.branch_address',
                'branches.cr_number',
                'branches.tax_number as branch_tax_number',
                'branches.manager_name as branch_manager',
                'branches.contact_email as branch_email'
            )
            ->where('sales.id' , '=' , $id)
            ->when(Auth::user()->subscriber_id ?? null,function($q,$sub){
                $q->where('sales.subscriber_id',$sub);
            })
            ->first();

        if (! $data) {
            return null;
        }

        $details = DB::table('sale_details')
            ->join('products','sale_details.product_id','=','products.id')
            ->select('sale_details.*', 'products.code','products.name','products.tax as taxRate','products.tax_excise as taxExciseRate')
            ->where('sale_details.sale_id','=', $id)
            ->when(Auth::user()->subscriber_id ?? null,function($q,$sub){
                $q->where('sale_details.subscriber_id',$sub);
            })
            ->get();

        $payments = Payment::with('user')
            ->where('sale_id',$id)
            ->where('sale_id','<>',null)
            ->get();

        $vendor = Company::find($data->customer_id);
        $cashier = Cashier::first();
        $company = CompanyInfo::first();
        $settings = SystemSettings::where('subscriber_id', $data->subscriber_id)->first() ?? SystemSettings::first();
        $subscriber = $data->subscriber_id ? Subscriber::find($data->subscriber_id) : null;
        $trialMode = $subscriber?->isTrialActive() ?? false;
        $resolvedTaxNumber = $this->resolveTaxNumber($data, $subscriber, $company, $settings);
        $qrCodeImage = $this->buildInvoiceQr($data, $company, $resolvedTaxNumber, $trialMode);

        return compact('data','details','vendor','cashier','payments','company','settings','subscriber','trialMode','resolvedTaxNumber','qrCodeImage');
    }

    private function resolveTaxNumber($sale, ?Subscriber $subscriber, ?CompanyInfo $company, ?SystemSettings $settings): ?string
    {
        return $sale->branch_tax_number
            ?? optional($subscriber)->tax_number
            ?? optional($settings)->tax_number
            ?? optional($company)->taxNumber;
    }

    private function buildInvoiceQr($sale, ?CompanyInfo $company, ?string $taxNumber, bool $trialMode): ?string
    {
        try {
            if ($trialMode) {
                return GenerateQrCode::fromArray([
                    new Seller('TRIAL VERSION'),
                    new TaxNumber('000000000000000'),
                    new InvoiceDate(now()->toIso8601String()),
                    new InvoiceTotalAmount(0),
                    new InvoiceTaxAmount(0),
                ])->render();
            }

            $sellerName = $sale->branch_name
                ?? optional($company)->name_ar
                ?? optional($company)->name_en
                ?? 'Company';

            $taxValue = $taxNumber ?: '000000000000000';

            return GenerateQrCode::fromArray([
                new Seller($sellerName),
                new TaxNumber($taxValue),
                new InvoiceDate($sale->date),
                new InvoiceTotalAmount($sale->net),
                new InvoiceTaxAmount($sale->tax),
            ])->render();
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function resolveServiceMeta(Request $request, array $defaults = []): array
    {
        $mode = $request->input('service_mode', $defaults['service_mode'] ?? 'dine_in');
        $allowedModes = ['dine_in', 'takeaway', 'delivery'];
        if (! in_array($mode, $allowedModes, true)) {
            $mode = 'dine_in';
        }

        $sessionLocation = null;
        $sessionType = null;
        $reservationTime = null;
        $reservationGuests = null;
        $reservationEnabled = (bool) ($request->boolean('reservation_enabled') ?? ($defaults['reservation_enabled'] ?? false));

        if ($mode === 'dine_in') {
            $sessionLocation = trim((string) ($request->input('session_location') ?? $defaults['session_location'] ?? '')) ?: null;
            $sessionType = trim((string) ($request->input('session_type') ?? $defaults['session_type'] ?? '')) ?: null;
            if ($reservationEnabled) {
                $reservationGuests = (int) ($request->input('reservation_guests') ?? $defaults['reservation_guests'] ?? 0) ?: null;
                $reservationInput = $request->input('reservation_time') ?? ($defaults['reservation_time'] ?? null);
                if ($reservationInput) {
                    try {
                        $reservationTime = Carbon::parse($reservationInput);
                    } catch (\Throwable $th) {
                        $reservationTime = null;
                    }
                }
            }
        }

        return [
            'service_mode' => $mode,
            'session_location' => $sessionLocation,
            'session_type' => $sessionType,
            'reservation_time' => $reservationTime,
            'reservation_guests' => $reservationGuests,
        ];
    }
}

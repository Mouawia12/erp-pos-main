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
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use DataTables;

class SalesReturnController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = DB::table('sales')
                    ->join('warehouses','sales.warehouse_id','=','warehouses.id')
                    ->join('companies','sales.customer_id','=','companies.id')
                    ->select('sales.*','warehouses.name as warehouse_name','companies.name as customer_name')
                    ->orderBy('id', 'desc')
                    ->get(); 

        if ($request->ajax()) { 
            return Datatables::of($data)->addIndexColumn()
                ->addColumn('action', function($row){  
                    if($row->sale_id == 0){
                        $btn ='<a href="javascript:;" onclick="showPayments('.$row->id.')" type="button" class="btn btn-success">
                                    عرض المدفوعات
                                </a>';
                        if(abs($row->net) - abs($row->paid) > 0) {
                            $btn = $btn.'<a href="javascript:;" onclick="addPayments('.$row->id.')" type="button" class="btn btn-primary">
                                            اضافة مدفوع
                                         </a>';
                        }
                        $btn = $btn.'<a type="button" class="btn btn-info"
                                       href='.route('print.sales',$row->id).'> 
                                        طباعة فاتورة 
                                     </a>';
                   
                        $btn = $btn.'<a href='.route('add_return',$row->id).' type="button" class="btn btn-warning">
                                         عمل مرتجع
                                     </a>';
                       
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
 
        return view('admin.sales.index');
    
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
        $settings = SystemSettings::with('currency') -> get() -> first();

        return view('admin.sales.create',compact('warehouses','customers' , 'settings'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreSalesRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSalesRequest $request)
    {
        $validated = $request->validate([
            'invoice_no' => 'required|unique:sales', 
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
                'sale_id' => 0,
                'product_code' => $productDetails->code,
                'product_id' => $id,
                'quantity' => $request->qnt[$index],
                'price_unit' => $request->price_unit[$index],
                'price_with_tax' => $request->price_with_tax[$index],
                'warehouse_id' => $request->warehouse_id,
                'unit_id' => $unitId,
                'tax' => $request->tax[$index],
                'total' => $request->total[$index],
                'lista' => 0,
                'profit'=> ($request->price_unit[$index] - ($productDetails->cost * $unitFactor)) * $request->qnt[$index]
            ];

            $item = new Product();
            $item -> product_id = $id;
            $item -> quantity = $request->qnt[$index] * $unitFactor;
            $item -> warehouse_id = $request->warehouse_id ;
            $qntProducts[] = $item ;

            $products[] = $product;
            $total +=$request->total[$index];
            $tax +=$request->tax[$index];
            $net +=$request->net[$index];
            $profit +=($request->price_unit[$index] - $productDetails->cost) * $request->qnt[$index];
        }

        $net += $request -> additional_service ?? 0 ;
 
        $sale = Sales::create([
            'date' => $request->bill_date,
            'invoice_no' => $request-> invoice_no,
            'invoice_type' => $request->invoice_type ?? 'tax_invoice',
            'customer_id' => $request->customer_id,
            'biller_id' => Auth::user()->id,
            'warehouse_id' => $request->warehouse_id,
            'note' => $request->notes ? $request->notes:'',
            'total' => $total,
            'discount' => 0,
            'tax' => $tax,
            'net' => $net ,
            'paid' => 0,
            'sale_status' => 'completed',
            'payment_status' => 'not_paid', 
            'pos' => $request -> has('POS') ? $request ->POS:0,
            'lista' => $lista,
            'profit'=> $profit,
            'additional_service' => $request -> additional_service ?? 0,
            'branch_id'=> $request->branch_id ?? $siteController->getWarehouseById($request->warehouse_id)->branch_id,
            'user_id'=> Auth::user()->id
        ]);

        foreach ($products as $product){
            $product['sale_id'] = $sale->id;
            SaleDetails::create($product);
        }

        $siteController->syncQnt($qntProducts,null);
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

    public function sales_return()
    {
        $returns = Sales::where('sale_id','>',0)->get();
        return view('admin.sales.index');
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
            ->select('sale_details.*','products.name as product_name')
            ->where('sale_id',$id)
            ->get();

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

        return view('admin.sales.return',compact('warehouses','customers','saleItems','id','sale'));
    }


    private function getAllProductReturnForSameInvoice($invoiceId,$productId){
        $totalQnt = 0;

        $allOtherSaleItems = DB::table('sale_details')
            ->join('sales','sales.id','=','sale_details.sale_id')
            ->select('sale_details.*')
            ->where('sales.sale_id',$invoiceId)
            ->where('sale_details.product_id',$productId)->get();

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
                             ,'branches.branch_name','branches.branch_phone','branches.branch_address' )
                    ->where('sales.id' , '=' , $id)
                    ->get();

        if(count($datas)){
            $data = $datas[0];

            $details = DB::table('sale_details')
                        ->join('products' , 'sale_details.product_id' , '=' , 'products.id')
                        ->select('sale_details.*' , 'products.code' , 'products.name')
                        ->where('sale_details.sale_id' , '=' , $id)
                        ->get();

            $payments = Payment::with('user') -> where('sale_id',$id)
                            ->where('sale_id','<>',null)
                            ->get();


            $vendor = Company::find($data->customer_id);
            $cashier = Cashier::first();
            $company = CompanyInfo::first();

            if($datas[0]->pos == 1){   
                return view('admin.sales.printPos',compact('data' , 'details','vendor','cashier' , 'payments','company' ))->render();
            } else {
                return view('admin.sales.print',compact('data' , 'details','vendor','cashier' , 'payments','company' ))->render();
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

        $siteController = new SystemController();
        $total = 0;
        $tax = 0;
        $discount = 0;
        $net = 0;
        $lista = 0;
        $profit = 0;

        $products = array();
        $qntProducts = array();
        foreach ($request->product_id as $index=>$id1){
            $productDetails = $siteController->getProductById($id1);
            $unitId = $request->unit_id[$index] ?? $productDetails->unit;
            $unitFactor = $request->unit_factor[$index] ?? 1;
            $product = [
                'sale_id' => 0,
                'product_code' => $productDetails->code,
                'product_id' => $id1,
                'quantity' => $request->qnt[$index] * -1,
                'price_unit' => $request->price_unit[$index] * -1,
                'price_with_tax' => $request->price_with_tax[$index] * -1,
                'warehouse_id' => $request->warehouse_id,
                'unit_id' => $unitId,
                'tax' => $request->tax[$index] * -1,
                'total' => $request->total[$index] * -1,
                'lista' => 0,
                'profit'=> (($request->price_unit[$index] - ($productDetails->cost * $unitFactor)) * $request->qnt[$index]) * -1
            ];

            $item = new Product();
            $item -> product_id = $id1;
            $item -> quantity = $request->qnt[$index]  * -1 * $unitFactor;
            $item -> warehouse_id = $request->warehouse_id ;
            $qntProducts[] = $item ;

            $products[] = $product;
            $total +=$request->total[$index];
            $tax +=$request->tax[$index];
            $net +=$request->net[$index];
            $profit +=($request->price_unit[$index] - $productDetails->cost) * $request->qnt[$index];
        }

        $sale = Sales::create([
            'sale_id' => $id,
            'date' => $request->bill_date,
            'invoice_no' => $request-> bill_number,
            'customer_id' => $request->customer_id,
            'biller_id' => Auth::id(),
            'warehouse_id' => $request->warehouse_id,
            'note' => $request->notes ? $request->notes :'',
            'total' => $total * -1,
            'discount' => 0,
            'tax' => $tax * -1,
            'net' => $net * -1,
            'paid' => 0,
            'sale_status' => 'completed',
            'payment_status' => 'not_paid',
            'created_by' => Auth::id(),
            'pos' => 0,
            'lista' => $lista * -1,
            'profit'=> $profit * -1,
            'branch_id'=> $request->branch_id ?? 1,
            'user_id'=> Auth::user()->id
        ]);

        foreach ($products as $product){
            $product['sale_id'] = $sale->id;
            SaleDetails::create($product);
        }

        $siteController->syncQnt($qntProducts,null);
        $clientController = new ClientMoneyController();
        $clientController->syncMoney($request->customer_id,0,$net);

        $vendorMovementController = new VendorMovementController();
        $vendorMovementController->addSaleMovement($sale->id);

        $siteController->saleJournals($sale->id);

        return redirect()->route('sales');
    }


    public function getNo($id){
        $warehouse = Warehouse::find($id);
        $bills = Sales::where('branch_id',$warehouse->branch_id)->count();
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

        if($warehouse -> serial_prefix){
            $prefix = $prefix .'-'.$warehouse ->branch_id.'-'.$warehouse -> serial_prefix;
        }else{
            $prefix = $prefix .'-'.$warehouse ->branch_id;
        }

        $no = json_encode($prefix . str_pad($id + 1, 6 , '0' , STR_PAD_LEFT)) ;
         echo $no ;
         exit;
    }

    public function getReturnNo($branch_id){ 

        $bills = Sales::where('branch_id',$branch_id)
                    ->where('sale_id','>',0)
                    ->count();
        
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

        $prefix = $prefix.'-'.$branch_id.'-';

        $no = json_encode($prefix . str_pad($id + 1, 6 , '0' , STR_PAD_LEFT)) ;
        echo $no ;
        exit;
    }

    public function pos(){
        $vendors = Company::where('group_id' , '=' , 3) -> get();
        $warehouses = Warehouse::all();
        $settings = SystemSettings::with('currency') -> get() -> first();
     
       return view('admin.sales.pos' , compact('vendors' , 'warehouses' , 'settings'));
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

    public function print($id)
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
                        ->join('products' , 'sale_details.product_id' , '=' , 'products.id')
                        ->select('sale_details.*' , 'products.code' , 'products.name')
                        ->where('sale_details.sale_id' , '=' , $id)
                        ->get();

            $payments = Payment::with('user') -> where('sale_id',$id)
                            ->where('sale_id','<>',null)
                            ->get();


            $vendor = Company::find($data->customer_id);
            $cashier = Cashier::first();
            $company = CompanyInfo::first();

            if($datas[0]->pos == 1){ 
                return view('admin.sales.printPos',compact('data' , 'details','vendor','cashier' , 'payments','company' ))->render();
            } else {
                return view('admin.sales.print',compact('data' , 'details','vendor','cashier' , 'payments','company' ))->render();
            }
           
        }

    }
}

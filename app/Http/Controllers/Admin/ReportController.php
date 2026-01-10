<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller; 
use App\Models\Brand;
use App\Models\Category;
use App\Models\Company;
use App\Models\Product;
use App\Models\UpdateQuntity;
use App\Models\VendorMovement;
use App\Models\Warehouse;
use App\Models\CompanyInfo;
use App\Models\Payment;
use App\Models\Sales;
use App\Models\Purchase;
use App\Models\Branch;
use App\Models\CostCenter;
use App\Models\WarehouseProducts;
use App\Models\SalonDepartment;
use App\Models\Representative;
use App\Models\Inventory;
use App\Models\InventoryDetails;
use App\Models\PosShift;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class ReportController extends Controller
{
    private function applySubscriberFilter($query, $table)
    {
        $sub = Auth::user()->subscriber_id ?? null;
        if ($sub && Schema::hasColumn($table, 'subscriber_id')) {
            $query->where($table . '.subscriber_id', $sub);
        }
        return $query;
    }
    public function daily_sales_report(){
        $siteContrller = new SystemController();
        $warehouses = $siteContrller->getAllWarehouses();
        $branches = $siteContrller->getBranches(); 
        $customers = Company::where('group_id',3)->get();
        $subscriberId = Auth::user()->subscriber_id ?? null;
        $costCenters = CostCenter::query()
            ->when($subscriberId, fn($q) => $q->where('subscriber_id', $subscriberId))
            ->where('is_active', 1)
            ->orderBy('name')
            ->get();
        return view('admin.Report.daily_sales_report' , compact('warehouses','branches','customers','costCenters'));
    }

    public function daily_sales_report_search($date , $warehouse, $branch_id, $customer_id = 0, $vehicle_plate = 'empty', $cost_center_id = 0){
        $query = Sales::with(['branch','warehouse','customer'])
                    ->where('sale_id',0)
                    ->when(Auth::user()->subscriber_id ?? null, function($q,$sub){
                        $q->where('subscriber_id',$sub);
                    })
                    ->whereDate('date',$date);

        if( $warehouse >0 ) $query->where('warehouse_id',$warehouse);  
        if( $branch_id >0 ) $query->where('branch_id',$branch_id);
        if( $customer_id >0 ) $query->where('customer_id',$customer_id);
        if( $cost_center_id >0 ) $query->where('cost_center_id',$cost_center_id);
        if(!empty($vehicle_plate) && $vehicle_plate !== 'empty'){
            $query->where('vehicle_plate','like','%'.$vehicle_plate.'%');
        }

        $data = $query->get();               
     
        $period_ar = 'الفترة :';
        if($date){
            $startDate = $date; 
            $period_ar .= $startDate ;
        } else { 
            $period_ar .= 'من البداية' ;
        }
 
        $html = view('admin.Report.daily_sales_modal',compact('data' , 'date' , 'warehouse','period_ar'))->render();
        return $html ;

    }

    public function sales_item_report(){
        $siteContrller = new SystemController();
        $warehouses = $siteContrller->getAllWarehouses();
        $branches = $siteContrller->getBranches(); 
        $vendors = Company::where('group_id' , '=' , 3) -> get(); 
        $subscriberId = Auth::user()->subscriber_id ?? null;
        $costCenters = CostCenter::query()
            ->when($subscriberId, fn($q) => $q->where('subscriber_id', $subscriberId))
            ->where('is_active', 1)
            ->orderBy('name')
            ->get();

        return view('admin.Report.sales_item_report' , compact('warehouses','vendors','branches','costCenters'));
    }

    public function sales_item_report_search($fdate,$tdate,$warehouse,$branch_id,$item_id,$supplier,$vehicle_plate = 'empty', $cost_center_id = 0){

        $dataQuery = DB::table('sale_details')
                    ->join('sales' , 'sales.id' , 'sale_details.sale_id')
                    ->join('products' , 'products.id' , 'sale_details.product_id')
                    ->join('companies' , 'sales.customer_id' , '=' , 'companies.id')
                    ->join('warehouses' , 'warehouses.id' , 'sales.warehouse_id')
                    ->join('branches' , 'branches.id' , 'sales.branch_id')
                    ->select('sale_details.*', 'sales.date as bill_date', 'sales.invoice_no as invoice_no',
                            'sales.branch_id','products.code as product_code', 'products.name as product_name',
                            'warehouses.name as warehouse_name', 'branches.branch_name','sales.warehouse_id'
                            ,'sales.customer_id','sales.date','sales.vehicle_plate','sales.vehicle_odometer','companies.name as customer_name')
                    ->where('sales.sale_id' , '=' ,  0)  
                    ->when(Auth::user()->subscriber_id ?? null,function($q,$sub){
                        $q->where('sale_details.subscriber_id',$sub);
                    });
 
        if($fdate){
            $dataQuery->whereDate('sales.date','>=',\Carbon\Carbon::parse($fdate)->format('Y-m-d'));
        }
        if($tdate){
            $dataQuery->whereDate('sales.date','<=',\Carbon\Carbon::parse($tdate)->format('Y-m-d'));
        }
        if( $warehouse > 0 ) $dataQuery->where('sales.warehouse_id',$warehouse);
        if( $branch_id > 0 ) $dataQuery->where('sales.branch_id',$branch_id);
        if( $cost_center_id > 0 ) $dataQuery->where('sales.cost_center_id',$cost_center_id);
        if($item_id>0) $dataQuery->where('sale_details.product_id',$item_id);
        if($supplier > 0) $dataQuery->where('sales.customer_id',$supplier);  
        if(!empty($vehicle_plate) && $vehicle_plate !== 'empty'){
            $dataQuery->where('sales.vehicle_plate','like','%'.$vehicle_plate.'%');
        }
        
        $data = $dataQuery->get();
        
           
        $period_ar = 'الفترة :';
        if($fdate){
            $startDate = $fdate; 
            $period_ar .= $startDate ;
        } else { 
            $period_ar .= 'من البداية' ;
        }

        if($tdate){
            $endDate =  Carbon::parse($tdate)  ; 
            $period_ar .= ' -- '  . $endDate -> format('Y-m-d');
        } else { 
            $period_ar .= ' -- '  . 'حتى اليوم' ;
        }

     
        $html = view('admin.Report.item_sales_modal',compact('data' , 'fdate' , 'tdate' , 'warehouse','period_ar','item_id', 'supplier'))->render();
        return $html ;
    }

    public function sales_return_report(){
        $siteContrller = new SystemController();
        $warehouses = $siteContrller->getAllWarehouses();
        $branches = $siteContrller->getBranches(); 
        $vendors = Company::where('group_id', 3)->get();
        $subscriberId = Auth::user()->subscriber_id ?? null;
        $costCenters = CostCenter::query()
            ->when($subscriberId, fn($q) => $q->where('subscriber_id', $subscriberId))
            ->where('is_active', 1)
            ->orderBy('name')
            ->get();
        return view('admin.Report.sales_return_report' , compact('warehouses','vendors','branches','costCenters'));
    }

    public function sales_return_report_search($fdate, $tdate, $warehouse,$bill_no,$vendor,$branch_id, $cost_center_id = 0){

        $data = Sales::where('sale_id' , '<>' , 0)
                    ->get();
                    
        if($fdate) $data = $data->where('date','>=',\Carbon\Carbon::parse($fdate)->format('Y-m-d'));
        if($tdate) $data = $data->where('date','<=',\Carbon\Carbon::parse($tdate)->format('Y-m-d'));
        if($warehouse > 0) $data = $data->where('warehouse_id',$warehouse);
        if(isset($bill_no) and $bill_no<>'empty') $data = $data->where('invoice_no',$bill_no);
        if($vendor > 0) $data = $data->where('customer_id',$vendor);
        if($branch_id > 0) $data = $data->where('branch_id',$branch_id);
        if($cost_center_id > 0) $data = $data->where('cost_center_id',$cost_center_id);

        $period_ar = 'الفترة :';
        if($fdate){
            $startDate = $fdate; 
            $period_ar .= $startDate;
        } else { 
            $period_ar .= 'من البداية' ;
        }

        if($tdate){
            $endDate =  Carbon::parse($tdate); 
            $period_ar .= ' -- '  . $endDate -> format('Y-m-d');
        } else { 
            $period_ar .= ' -- '  . 'حتى اليوم' ;
        }

        $html = view('admin.Report.sales_return_modal'
                    ,compact('data', 'fdate', 'tdate', 'warehouse','bill_no', 'vendor'
                    ,'period_ar'))->render();
        return $html ;
    }

    public function purchase_report(){

        $siteContrller = new SystemController();
        $warehouses = $siteContrller->getAllWarehouses();
        $branches = $siteContrller->getBranches(); 
        $vendors = Company::where('group_id' , '=' , 4) -> get();
        $subscriberId = Auth::user()->subscriber_id ?? null;
        $costCenters = CostCenter::query()
            ->when($subscriberId, fn($q) => $q->where('subscriber_id', $subscriberId))
            ->where('is_active', 1)
            ->orderBy('name')
            ->get();

        return view('admin.Report.purchase_report' , compact('warehouses', 'vendors','branches','costCenters'));
    }

    public function posEndOfDayReport(Request $request)
    {
        $siteContrller = new SystemController();
        $warehouses = $siteContrller->getAllWarehouses();
        $branches = $siteContrller->getBranches();
        $subscriberId = Auth::user()->subscriber_id ?? null;
        $shifts = PosShift::query()
            ->when($subscriberId, fn($q) => $q->where('subscriber_id', $subscriberId))
            ->orderByDesc('id')
            ->limit(200)
            ->get();
        $cashiers = User::query()
            ->when($subscriberId, fn($q) => $q->where('subscriber_id', $subscriberId))
            ->orderBy('name')
            ->get();

        $filters = [
            'from' => $request->input('from'),
            'to' => $request->input('to'),
            'branch_id' => (int) $request->input('branch_id', 0),
            'warehouse_id' => (int) $request->input('warehouse_id', 0),
            'user_id' => (int) $request->input('user_id', 0),
            'shift_id' => (int) $request->input('shift_id', 0),
        ];

        $salesQuery = Sales::query()
            ->where('pos', 1)
            ->when($subscriberId, fn($q) => $q->where('subscriber_id', $subscriberId));

        if (! empty($filters['from'])) {
            $salesQuery->whereDate('date', '>=', $filters['from']);
        }
        if (! empty($filters['to'])) {
            $salesQuery->whereDate('date', '<=', $filters['to']);
        }
        if ($filters['branch_id'] > 0) {
            $salesQuery->where('branch_id', $filters['branch_id']);
        }
        if ($filters['warehouse_id'] > 0) {
            $salesQuery->where('warehouse_id', $filters['warehouse_id']);
        }
        if ($filters['user_id'] > 0) {
            $salesQuery->where('user_id', $filters['user_id']);
        }
        if ($filters['shift_id'] > 0) {
            $salesQuery->where('pos_shift_id', $filters['shift_id']);
        }

        $salesTotals = (clone $salesQuery)->where('sale_id', 0)->selectRaw(
            'SUM(total) as total, SUM(tax) as tax, SUM(tax_excise) as tax_excise, SUM(net) as net, SUM(profit) as profit'
        )->first();

        $returnTotals = (clone $salesQuery)->where('sale_id', '>', 0)->selectRaw(
            'SUM(total) as total, SUM(tax) as tax, SUM(tax_excise) as tax_excise, SUM(net) as net, SUM(profit) as profit'
        )->first();

        $saleIds = (clone $salesQuery)->pluck('id');
        $quantityTotal = 0;
        if ($saleIds->isNotEmpty()) {
            $quantityTotal = DB::table('sale_details')
                ->when($subscriberId, fn($q) => $q->where('sale_details.subscriber_id', $subscriberId))
                ->whereIn('sale_id', $saleIds)
                ->selectRaw('SUM(quantity * COALESCE(unit_factor, 1)) as qty')
                ->value('qty');
        }

        $payments = Payment::query()
            ->whereIn('sale_id', $saleIds)
            ->selectRaw("SUM(CASE WHEN paid_by = 'cash' THEN amount ELSE 0 END) as cash_total")
            ->selectRaw("SUM(CASE WHEN paid_by = 'bank' THEN amount ELSE 0 END) as bank_total")
            ->selectRaw("SUM(CASE WHEN paid_by LIKE 'card:%' THEN amount ELSE 0 END) as card_total")
            ->first();

        $summary = [
            'sales' => [
                'total' => (float) ($salesTotals->total ?? 0),
                'tax' => (float) ($salesTotals->tax ?? 0),
                'tax_excise' => (float) ($salesTotals->tax_excise ?? 0),
                'net' => (float) ($salesTotals->net ?? 0),
                'profit' => (float) ($salesTotals->profit ?? 0),
            ],
            'returns' => [
                'total' => (float) ($returnTotals->total ?? 0),
                'tax' => (float) ($returnTotals->tax ?? 0),
                'tax_excise' => (float) ($returnTotals->tax_excise ?? 0),
                'net' => (float) ($returnTotals->net ?? 0),
                'profit' => (float) ($returnTotals->profit ?? 0),
            ],
            'quantity' => (float) ($quantityTotal ?? 0),
            'payments' => [
                'cash' => (float) ($payments->cash_total ?? 0),
                'bank' => (float) ($payments->bank_total ?? 0),
                'card' => (float) ($payments->card_total ?? 0),
            ],
        ];

        return view('admin.Report.pos_end_of_day', compact('warehouses', 'branches', 'cashiers', 'shifts', 'filters', 'summary'));
    }

    public function purchase_report_search($fdate,$tdate, $warehouse,$bill_no,$vendor,$branch_id, $cost_center_id = 0){

        $data = Purchase::where('returned_bill_id', 0)
                    ->get();
        
        if($fdate) $data = $data->where('date','>=',\Carbon\Carbon::parse($fdate)->format('Y-m-d'));
        if($tdate) $data = $data->where('date','<=',\Carbon\Carbon::parse($tdate)->format('Y-m-d'));
        if($warehouse > 0) $data = $data->where('warehouse_id',$warehouse); 
        if(isset($bill_no) and $bill_no<>'empty') $data = $data->where('invoice_no',$bill_no);
        if($vendor > 0) $data = $data->where('customer_id',$vendor);
        if($branch_id > 0) $data = $data->where('branch_id',$branch_id);
        if($cost_center_id > 0) $data = $data->where('cost_center_id',$cost_center_id);
       
        $period_ar = 'الفترة :';
        if($fdate){
            $startDate = $fdate; 
            $period_ar .= $startDate;
        } else { 
            $period_ar .= 'من البداية' ;
        }

        if($tdate){
            $endDate =  Carbon::parse($tdate); 
            $period_ar .= ' -- '  . $endDate -> format('Y-m-d');
        } else { 
            $period_ar .= ' -- '  . 'حتى اليوم' ;
        }
                                
        $html = view('admin.Report.purchase_modal'
                    ,compact('data', 'fdate', 'tdate', 'warehouse','bill_no','vendor','period_ar'))->render();
        return $html ;
    }

    public function purchases_return_report(){
        $siteContrller = new SystemController();
        $warehouses = $siteContrller->getAllWarehouses();
        $branches = $siteContrller->getBranches(); 
        $vendors = Company::where('group_id', 4)->get();
        $subscriberId = Auth::user()->subscriber_id ?? null;
        $costCenters = CostCenter::query()
            ->when($subscriberId, fn($q) => $q->where('subscriber_id', $subscriberId))
            ->where('is_active', 1)
            ->orderBy('name')
            ->get();
        return view('admin.Report.purchase_return_report' , compact('warehouses','vendors','branches','costCenters'));
    }

    public function purchases_return_report_search($fdate, $tdate, $warehouse,$bill_no,$vendor,$branch_id, $cost_center_id = 0){

        $data = Purchase::where('returned_bill_id' , '<>' , 0)
                    ->get();
                    
        if($fdate) $data = $data->where('date','>=',\Carbon\Carbon::parse($fdate)->format('Y-m-d'));
        if($tdate) $data = $data->where('date','<=',\Carbon\Carbon::parse($tdate)->format('Y-m-d'));
        if($warehouse > 0) $data = $data->where('warehouse_id',$warehouse);
        if(isset($bill_no) and $bill_no<>'empty') $data = $data->where('invoice_no',$bill_no);
        if($vendor > 0) $data = $data->where('customer_id',$vendor);
        if($branch_id > 0) $data = $data->where('branch_id',$branch_id);
        if($cost_center_id > 0) $data = $data->where('cost_center_id',$cost_center_id);

        $period_ar = 'الفترة :';
        if($fdate){
            $startDate = $fdate; 
            $period_ar .= $startDate;
        } else { 
            $period_ar .= 'من البداية' ;
        }

        if($tdate){
            $endDate =  Carbon::parse($tdate); 
            $period_ar .= ' -- '  . $endDate -> format('Y-m-d');
        } else { 
            $period_ar .= ' -- '  . 'حتى اليوم' ;
        }

        $html = view('admin.Report.purchase_return_modal'
                    ,compact('data', 'fdate', 'tdate', 'warehouse','bill_no', 'vendor'
                    ,'period_ar'))->render();
        return $html ;
    }

    public function items_report(){
        $siteContrller = new SystemController();
        $warehouses = $siteContrller->getAllWarehouses();
        $branches = $siteContrller->getBranches(); 
        $brands = Brand::all();
        $categories = Category::all(); 
        $type = 0 ;

        return view('admin.Report.items_report', compact('warehouses','brands','categories','branches','type'));
    }

    public function items_report_search( $category, $brand, $warehouse, $branch_id){

        if($branch_id> 0){
            $data = Product::with('units')
            ->join('warehouse_products' ,'warehouse_products.product_id' , '=' , 'products.id')
            ->join('warehouses' ,'warehouses.id' , '=' , 'warehouse_products.warehouse_id')
            ->join('branches' ,'branches.id' , '=' , 'warehouses.branch_id')
            ->join('categories' ,'categories.id' , '=' , 'products.category_id')
            ->select('products.*' , 'warehouse_products.quantity as qty'
                     ,'warehouse_products.warehouse_id as warehouseId'
                     ,'warehouses.branch_id as branchId','warehouses.name as warehouse_name'
                     ,'branches.branch_name','categories.name as categories_name')
            ->get(); 

            
            if($warehouse > 0) $data = $data->where('warehouseId',$warehouse); 
            $data = $data->where('branchId',$branch_id);
            $isbranches = 1;

        }else{
            $data = Product::with('units')
            ->join('warehouse_products' ,'warehouse_products.product_id' , '=' , 'products.id')
            ->join('warehouses' ,'warehouses.id' , '=' , 'warehouse_products.warehouse_id')
            ->join('branches' ,'branches.id' , '=' , 'warehouses.branch_id')
            ->select('products.*' , DB::raw("SUM(warehouse_products.quantity) qty"))
            ->groupBy('warehouse_products.product_id')
            ->get(); 

            $isbranches = 0;
        }

        if($brand > 0) $data = $data->where('brand',$brand);   
        if($category > 0) $data = $data->where('category_id',$category); 
        $type = 0 ;

        $html = view('admin.Report.items_modal',compact('data','category','brand','type','isbranches'))->render();
        return $html ;
    }

    public function items_limit_report(){

        $siteContrller = new SystemController();
        $warehouses = $siteContrller->getAllWarehouses();
        $branches = $siteContrller->getBranches(); 
        $brands = Brand::all();
        $categories = Category::all();
        $type = 1 ;

        return view('admin.Report.items_report', compact('warehouses','brands','categories','type','branches'));
    }

    public function items_limit_report_search( $category, $brand, $warehouse, $branch_id){
        
        if($branch_id> 0){
            $data = Product::with('units')
            ->join('warehouse_products' ,'warehouse_products.product_id' , '=' , 'products.id')
            ->join('warehouses' ,'warehouses.id' , '=' , 'warehouse_products.warehouse_id')
            ->join('branches' ,'branches.id' , '=' , 'warehouses.branch_id')
            ->join('categories' ,'categories.id' , '=' , 'products.category_id')
            ->select('products.*' , 'warehouse_products.quantity as qty'
                     ,'warehouse_products.warehouse_id as warehouseId'
                     ,'warehouses.branch_id as branchId','warehouses.name as warehouse_name'
                     ,'branches.branch_name','categories.name as categories_name')
            ->whereRaw('products.alert_quantity > warehouse_products.quantity')
            ->get(); 

            if($warehouse > 0) $data = $data->where('warehouseId',$warehouse); 
            $data = $data->where('branchId',$branch_id);
            $isbranches = 1;

        }else{
            $data = Product::with('units')
            ->join('warehouse_products' ,'warehouse_products.product_id' , '=' , 'products.id')
            ->join('warehouses' ,'warehouses.id' , '=' , 'warehouse_products.warehouse_id')
            ->join('branches' ,'branches.id' , '=' , 'warehouses.branch_id')
            ->select('products.*' , DB::raw("SUM(warehouse_products.quantity) qty"))
            ->groupBy('warehouse_products.product_id')
            ->whereRaw('products.alert_quantity > warehouse_products.quantity')
            ->get(); 

            $isbranches = 0;
        }

        if($brand > 0) $data = $data->where('brand',$brand);   
        if($category > 0) $data = $data->where('category_id',$category); 
        $type = 1 ;

        $html = view('admin.Report.items_modal',compact('data','category','brand','type','isbranches'))->render();
        return $html ;
    }

    public function items_no_balance_report(){

        $siteContrller = new SystemController();
        $warehouses = $siteContrller->getAllWarehouses();
        $branches = $siteContrller->getBranches(); 
        $brands = Brand::all();
        $categories = Category::all();
        $type = 2 ;

       return view('admin.Report.items_report', compact('warehouses','brands','categories','type','branches'));
    }

    public function items_no_balance_report_search( $category , $brand, $warehouse, $branch_id){
        if($branch_id> 0){
            $data = Product::with('units')
            ->join('warehouse_products' ,'warehouse_products.product_id' , '=' , 'products.id')
            ->join('warehouses' ,'warehouses.id' , '=' , 'warehouse_products.warehouse_id')
            ->join('branches' ,'branches.id' , '=' , 'warehouses.branch_id')
            ->join('categories' ,'categories.id' , '=' , 'products.category_id')
            ->select('products.*' , 'warehouse_products.quantity as qty'
                     ,'warehouse_products.warehouse_id as warehouseId'
                     ,'warehouses.branch_id as branchId','warehouses.name as warehouse_name'
                     ,'branches.branch_name','categories.name as categories_name')
            ->where('products.quantity', '<=' , 0)
            ->get(); 

            if($warehouse > 0) $data = $data->where('warehouseId',$warehouse); 
            $data = $data->where('branchId',$branch_id);
            $isbranches = 1;

        }else{
            $data = Product::with('units')
            ->join('warehouse_products' ,'warehouse_products.product_id' , '=' , 'products.id')
            ->join('warehouses' ,'warehouses.id' , '=' , 'warehouse_products.warehouse_id')
            ->join('branches' ,'branches.id' , '=' , 'warehouses.branch_id')
            ->select('products.*' , DB::raw("SUM(warehouse_products.quantity) qty"))
            ->groupBy('warehouse_products.product_id') 
            ->where('products.quantity', '<=' , 0)
            ->get(); 

            $isbranches = 0;
        }

        if($brand > 0) $data = $data->where('brand',$brand);   
        if($category > 0) $data = $data->where('category_id',$category); 
        $type = 2 ;
        
        $html = view('admin.Report.items_modal',compact('data','category','brand','type','isbranches'))->render();
        return $html ;
    }

    public function items_stock_report(){

        $siteContrller = new SystemController();
        $warehouses = $siteContrller->getAllWarehouses();
        $branches = $siteContrller->getBranches(); 

        return view('admin.Report.items_stock_report' , compact('warehouses','branches'));
    }

    public function items_stock_report_search( $fdate, $tdate, $warehouse, $branch_id, $item_id ){

        $data = array(); 

        $purchases = Purchase::join('purchase_details' , 'purchases.id' , '=' , 'purchase_details.purchase_id')
            -> join('products' , 'purchase_details.product_id' , '=', 'products.id')
            -> select('purchases.warehouse_id as warehouse','purchases.date as date'
                 , 'purchase_details.product_id as item_id' , 'purchase_details.quantity as qnt'
                 ,'purchases.branch_id','products.code as product_code' , 'products.name as product_name')
            -> where('purchases.returned_bill_id' , '=' , 0)
            -> when(Auth::user()->subscriber_id ?? null,function($q,$sub){
                $q->where('purchases.subscriber_id',$sub);
            })
            ->get();

        if( $warehouse > 0 ) $purchases = $purchases->where('warehouse',$warehouse);
        if( $branch_id > 0 ) $purchases = $purchases->where('branch_id',$branch_id); 

        foreach ($purchases as $purchase){
            $obj = [
                'date' => $purchase -> date,
                'item_id' => $purchase -> item_id,
                'product_code' => $purchase -> product_code,
                'product_name' => $purchase -> product_name,
                'qnt' => $purchase -> qnt,
                'warehouse' => $purchase -> warehouse,
                'type' => 1
            ] ;

            array_push($data , $obj) ;
        }

        $returnPurchase = Purchase::join('purchase_details' , 'purchases.id' , '=' , 'purchase_details.purchase_id')
            -> join('products' , 'purchase_details.product_id' , '=', 'products.id')
            -> select('purchases.warehouse_id as warehouse','purchases.date as date'
                    ,'purchase_details.product_id as item_id' , 'purchase_details.quantity as qnt'
                    ,'purchases.branch_id','products.code as product_code' , 'products.name as product_name')
            -> where('purchases.returned_bill_id' , '<>' , 0)
            -> when(Auth::user()->subscriber_id ?? null,function($q,$sub){
                $q->where('purchases.subscriber_id',$sub);
            })
            ->get();
  
        if( $warehouse > 0 ) $returnPurchase = $returnPurchase->where('warehouse',$warehouse);
        if( $branch_id > 0 ) $returnPurchase = $returnPurchase->where('branch_id',$branch_id); 

        foreach ($returnPurchase as $item){
            $obj = [
                'date' => $item -> date,
                'item_id' => $item -> item_id,
                'product_code' => $item -> product_code,
                'product_name' => $item -> product_name,
                'qnt' => $item -> qnt,
                'warehouse' => $item -> warehouse,
                'type' => 2
            ] ;
            array_push($data , $obj) ;
        }

        $sales = Sales::join('sale_details' , 'sales.id' , '=' , 'sale_details.sale_id')
            -> join('products' , 'sale_details.product_id' , '=', 'products.id')
            -> select('sales.warehouse_id as warehouse','sales.date as date'
                    ,'sale_details.product_id as item_id' , 'sale_details.quantity as qnt'
                    ,'sales.branch_id','products.code as product_code'
                    ,'products.name as product_name')
            -> where('sales.sale_id' , '=' , 0)
            -> when(Auth::user()->subscriber_id ?? null,function($q,$sub){
                $q->where('sales.subscriber_id',$sub);
            })
            ->get();

        if( $warehouse > 0 ) $sales = $sales->where('warehouse',$warehouse);
        if( $branch_id > 0 ) $sales = $sales->where('branch_id',$branch_id);

        foreach ($sales as $item){
            $obj = [
                'date' => $item -> date,
                'item_id' => $item -> item_id,
                'product_code' => $item -> product_code,
                'product_name' => $item -> product_name,
                'qnt' => $item -> qnt,
                'warehouse' => $item -> warehouse,
                'type' => 3
            ] ;
            array_push($data , $obj) ;
        }

        $salesReturn = Sales::join('sale_details' , 'sales.id' , '=' , 'sale_details.sale_id')
            -> join('products' , 'sale_details.product_id' , '=', 'products.id')
            -> select('sales.warehouse_id as warehouse','sales.date as date'
                    ,'sale_details.product_id as item_id' , 'sale_details.quantity as qnt'
                    ,'sales.branch_id','products.code as product_code'
                    ,'products.name as product_name')
            -> where('sales.sale_id' , '<>' , 0)
            -> when(Auth::user()->subscriber_id ?? null,function($q,$sub){
                $q->where('sales.subscriber_id',$sub);
            })
            -> when(Auth::user()->subscriber_id ?? null,function($q,$sub){
                $q->where('sale_details.subscriber_id',$sub);
            })
            ->get();

        if( $warehouse > 0 ) $salesReturn = $salesReturn->where('warehouse',$warehouse);
        if( $branch_id > 0 ) $salesReturn = $salesReturn->where('branch_id',$branch_id);

        foreach ($salesReturn as $item){
            $obj = [
                'date' => $item -> date,
                'item_id' => $item -> item_id,
                'product_code' => $item -> product_code,
                'product_name' => $item -> product_name,
                'qnt' => $item -> qnt,
                'warehouse' => $item -> warehouse,
                'type' => 4
            ] ;
            array_push($data , $obj) ;
        }

        $result = [] ; 
        $group = [];

        foreach ($data as $element) {
            $group[$element['item_id']][] = $element;
        }

        foreach ($group as $element) {
               $qnt_update = 0 ;
               $qnt_purchase = 0 ;
               $qnt_purchase_return = 0;
               $qnt_sales = 0 ;
               $qnt_sales_return = 0 ;
               foreach ($element as $subElement){
                   if($subElement['type'] == 0){
                       $qnt_update += $subElement['qnt'] ;
                   } else if($subElement['type'] == 1){
                       $qnt_purchase += $subElement['qnt'] ;
                   }
                   else if($subElement['type'] == 2){
                       $qnt_purchase_return += $subElement['qnt'] ;
                   } else if($subElement['type'] == 3){
                       $qnt_sales += $subElement['qnt'] ;
                   }  else if($subElement['type'] == 4){
                       $qnt_sales_return += $subElement['qnt'] ;
                   }

               }
               $obj = [
                   'date' => $subElement['date'],
                   'item_id' => $subElement['item_id'],
                   'product_code' => $subElement['product_code'],
                   'product_name' => $subElement ['product_name'],
                   'qnt_update' => $qnt_update,
                   'qnt_purchase' => $qnt_purchase ,
                   'qnt_purchase_return' => $qnt_purchase_return ,
                   'qnt_sales' => $qnt_sales ,
                   'qnt_sales_return' => $qnt_sales_return ,
                   'warehouse' => $subElement['warehouse'],
               ] ;
               array_push($result , $obj) ;
        }

        $period_ar = 'الفترة :';
        if($fdate){
            $startDate = $fdate; 
            $period_ar .= $startDate;
        } else { 
            $period_ar .= 'من البداية' ;
        }

        if($tdate){
            $endDate =  Carbon::parse($tdate); 
            $period_ar .= ' -- '  . $endDate -> format('Y-m-d');
        } else { 
            $period_ar .= ' -- '  . 'حتى اليوم' ;
        }

        if($warehouse == 0){
            $warehouse_name = 'الكل' ;
        }else{
            $warehouse_name = Warehouse::find($warehouse)->name;
        }

        if($branch_id == 0){
            $branch_name = 'الكل' ;
        }else{
            $branch_name = Branch::find($branch_id)->branch_name;
        }

        $html = view('admin.Report.items_stock_modal'
                    ,compact('result','fdate', 'tdate', 'warehouse','warehouse_name', 'item_id','period_ar','branch_name'))->render();
        return $html ;
 
    }

    public function items_purchased_report(){

        $siteContrller = new SystemController();
        $warehouses = $siteContrller->getAllWarehouses();
        $branches = $siteContrller->getBranches(); 
        $vendors = Company::where('group_id' , '=' , 4) -> get();

        return view('admin.Report.items_purchased_report' , compact('warehouses','vendors','branches'));
    }

    public function items_purchased_report_search($fdate,$tdate,$warehouse,$branch_id,$item_id,$supplier ){

        $data = DB::table('purchase_details')
                    ->join('purchases' , 'purchase_details.purchase_id' , '=' , 'purchases.id')
                    ->join('products' , 'purchase_details.product_id' , '=' , 'products.id')
                    ->join('companies' , 'purchases.customer_id' , '=' , 'companies.id')
                    ->join('warehouses' , 'warehouses.id' , 'purchases.warehouse_id')
                    ->join('branches' , 'branches.id' , 'purchases.branch_id')
                    ->select('purchases.*' ,'products.code as product_code','products.name as product_name' 
                            ,'purchase_details.quantity', 'purchase_details.cost_with_tax'
                            , 'purchase_details.product_id', 'companies.name as supplier_name'
                            , 'warehouses.name as warehouse_name','branches.branch_name')
                    ->get(); 
 
        if($fdate) $data = $data->where('date','>=',\Carbon\Carbon::parse($fdate)->format('Y-m-d'));
        if($tdate) $data = $data->where('date','<=',\Carbon\Carbon::parse($tdate)->format('Y-m-d'));
        if( $warehouse > 0 ) $data = $data->where('warehouse_id',$warehouse);
        if( $branch_id > 0 ) $data = $data->where('branch_id',$branch_id);
        if($item_id>0) $data = $data->where('product_id',$item_id);
        if($supplier > 0) $data = $data->where('customer_id',$supplier);
    
        $period_ar = 'الفترة :';
        if($fdate){
            $startDate = $fdate; 
            $period_ar .= $startDate;
        } else { 
            $period_ar .= 'من البداية' ;
        }
    
        if($tdate){
            $endDate =  Carbon::parse($tdate); 
            $period_ar .= ' -- '  . $endDate -> format('Y-m-d');
        } else { 
            $period_ar .= ' -- '  . 'حتى اليوم' ;
        }
 
        $html = view('admin.Report.items_purchased_modal'
                    ,compact('data','fdate', 'tdate', 'warehouse','item_id', 'supplier','period_ar'))->render();
        return $html ;
 

    }

    public function quotationsReport(Request $request)
    {
        $siteContrller = new SystemController();
        $warehouses = $siteContrller->getAllWarehouses();
        $branches = $siteContrller->getBranches();
        $customers = Company::where('group_id', 3)->get();
        $representatives = Representative::all();
        $subscriberId = Auth::user()->subscriber_id ?? null;
        $costCenters = CostCenter::query()
            ->when($subscriberId, fn($q) => $q->where('subscriber_id', $subscriberId))
            ->where('is_active', 1)
            ->orderBy('name')
            ->get();

        $hasRepresentative = Schema::hasColumn('quotations', 'representative_id');
        $hasCostCenter = Schema::hasColumn('quotations', 'cost_center_id');

        $query = DB::table('quotations as q')
            ->leftJoin('companies as c', 'c.id', '=', 'q.customer_id')
            ->leftJoin('warehouses as w', 'w.id', '=', 'q.warehouse_id')
            ->leftJoin('branches as b', 'b.id', '=', 'q.branch_id');

        if ($hasRepresentative) {
            $query->leftJoin('representatives as r', 'r.id', '=', 'q.representative_id');
        }
        if ($hasCostCenter) {
            $query->leftJoin('cost_centers as cc', 'cc.id', '=', 'q.cost_center_id');
        }

        $selectColumns = [
            'q.*',
            'c.name as customer_name_display',
            'w.name as warehouse_name',
            'b.branch_name',
        ];
        $selectColumns[] = $hasRepresentative
            ? 'r.user_name as representative_name'
            : DB::raw('NULL as representative_name');
        $selectColumns[] = $hasCostCenter
            ? 'cc.name as cost_center_name'
            : DB::raw('NULL as cost_center_name');

        $query->select($selectColumns);

        if (Auth::user()->subscriber_id ?? null) {
            if (Schema::hasColumn('quotations', 'subscriber_id')) {
                $query->where('q.subscriber_id', Auth::user()->subscriber_id);
            }
        }

        if (!empty(Auth::user()->branch_id)) {
            $query->where('q.branch_id', Auth::user()->branch_id);
        }

        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;

        $query
            ->when($request->quotation_no, fn($q, $v) => $q->where('q.quotation_no', 'like', '%' . $v . '%'))
            ->when($request->customer_id, fn($q, $v) => $q->where('q.customer_id', $v))
            ->when($request->representative_id && $hasRepresentative, fn($q, $v) => $q->where('q.representative_id', $v))
            ->when($request->warehouse_id, fn($q, $v) => $q->where('q.warehouse_id', $v))
            ->when($request->status, fn($q, $v) => $q->where('q.status', $v))
            ->when($request->cost_center_id && $hasCostCenter, fn($q, $v) => $q->where('q.cost_center_id', $v))
            ->when($dateFrom, fn($q) => $q->whereDate('q.date', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->whereDate('q.date', '<=', $dateTo));

        if (empty(Auth::user()->branch_id) && $request->branch_id) {
            $query->where('q.branch_id', $request->branch_id);
        }

        $quotations = $query->orderByDesc('q.date')->get();

        $summary = [
            'total' => $quotations->sum('total'),
            'discount' => $quotations->sum('discount'),
            'tax' => $quotations->sum('tax'),
            'net' => $quotations->sum('net'),
        ];

        return view('admin.Report.quotations_report', compact(
            'warehouses',
            'branches',
            'customers',
            'representatives',
            'costCenters',
            'quotations',
            'summary',
            'dateFrom',
            'dateTo'
        ));
    }

    public function inventoryValueReport(Request $request)
    {
        $siteContrller = new SystemController();
        $warehouses = $siteContrller->getAllWarehouses();
        $branches = $siteContrller->getBranches();
        $categories = Category::orderBy('name')->get();
        $brands = Brand::orderBy('name')->get();

        $branchSelected = $request->branch_id ?? 0;
        if (!empty(Auth::user()->branch_id)) {
            $branchSelected = Auth::user()->branch_id;
        }

        $query = WarehouseProducts::query()
            ->join('products', 'products.id', '=', 'warehouse_products.product_id')
            ->join('warehouses', 'warehouses.id', '=', 'warehouse_products.warehouse_id')
            ->leftJoin('branches', 'branches.id', '=', 'warehouses.branch_id')
            ->select(
                'products.id',
                'products.code',
                'products.name',
                'products.category_id',
                'products.brand',
                'products.cost as product_cost',
                'warehouse_products.quantity',
                'warehouse_products.cost as warehouse_cost',
                'warehouses.name as warehouse_name',
                'branches.branch_name',
                'warehouses.branch_id',
            )
            ->where('warehouse_products.quantity', '>', 0);

        if ($branchSelected > 0) {
            $query->where('warehouses.branch_id', $branchSelected);
        }

        if ($request->warehouse_id) {
            $query->where('warehouse_products.warehouse_id', $request->warehouse_id);
        }

        if ($request->category_id) {
            $query->where('products.category_id', $request->category_id);
        }

        if ($request->brand_id) {
            $query->where('products.brand', $request->brand_id);
        }

        $data = $query->orderBy('products.name')->get()->map(function ($row) {
            $cost = $row->warehouse_cost ?? $row->product_cost ?? 0;
            $row->unit_cost = (float) $cost;
            $row->value = (float) $row->quantity * $row->unit_cost;
            return $row;
        });

        $totalValue = $data->sum('value');

        return view('admin.Report.inventory_value_report', [
            'warehouses' => $warehouses,
            'branches' => $branches,
            'categories' => $categories,
            'brands' => $brands,
            'data' => $data,
            'totalValue' => $totalValue,
            'branchSelected' => $branchSelected,
            'warehouseSelected' => $request->warehouse_id,
            'categorySelected' => $request->category_id,
            'brandSelected' => $request->brand_id,
        ]);
    }

    public function inventoryAgingReport(Request $request)
    {
        $siteContrller = new SystemController();
        $warehouses = $siteContrller->getAllWarehouses();
        $branches = $siteContrller->getBranches();
        $categories = Category::orderBy('name')->get();
        $brands = Brand::orderBy('name')->get();

        $branchSelected = $request->branch_id ?? 0;
        if (!empty(Auth::user()->branch_id)) {
            $branchSelected = Auth::user()->branch_id;
        }

        $lastPurchaseSub = DB::table('purchase_details as pd')
            ->join('purchases as p', 'p.id', '=', 'pd.purchase_id')
            ->select('pd.product_id', 'pd.warehouse_id', DB::raw('MAX(p.date) as last_purchase_date'))
            ->when(Auth::user()->subscriber_id ?? null, fn($q, $v) => $q->where('p.subscriber_id', $v))
            ->groupBy('pd.product_id', 'pd.warehouse_id');

        $query = WarehouseProducts::query()
            ->join('products', 'products.id', '=', 'warehouse_products.product_id')
            ->join('warehouses', 'warehouses.id', '=', 'warehouse_products.warehouse_id')
            ->leftJoin('branches', 'branches.id', '=', 'warehouses.branch_id')
            ->leftJoinSub($lastPurchaseSub, 'lp', function ($join) {
                $join->on('warehouse_products.product_id', '=', 'lp.product_id')
                    ->on('warehouse_products.warehouse_id', '=', 'lp.warehouse_id');
            })
            ->select(
                'products.id',
                'products.code',
                'products.name',
                'products.category_id',
                'products.brand',
                'products.cost as product_cost',
                'warehouse_products.quantity',
                'warehouse_products.cost as warehouse_cost',
                'warehouses.name as warehouse_name',
                'branches.branch_name',
                'warehouses.branch_id',
                'lp.last_purchase_date'
            )
            ->where('warehouse_products.quantity', '>', 0);

        if ($branchSelected > 0) {
            $query->where('warehouses.branch_id', $branchSelected);
        }

        if ($request->warehouse_id) {
            $query->where('warehouse_products.warehouse_id', $request->warehouse_id);
        }

        if ($request->category_id) {
            $query->where('products.category_id', $request->category_id);
        }

        if ($request->brand_id) {
            $query->where('products.brand', $request->brand_id);
        }

        $agingTotals = [
            'current' => 0,
            '30' => 0,
            '60' => 0,
            '90' => 0,
            'over' => 0,
        ];

        $data = $query->orderBy('products.name')->get()->map(function ($row) use (&$agingTotals) {
            $cost = $row->warehouse_cost ?? $row->product_cost ?? 0;
            $row->unit_cost = (float) $cost;
            $row->value = (float) $row->quantity * $row->unit_cost;

            if ($row->last_purchase_date) {
                $days = Carbon::parse($row->last_purchase_date)->diffInDays(now());
            } else {
                $days = null;
            }

            $row->days_since = $days;
            $row->aging_bucket = 'N/A';

            if ($days !== null) {
                if ($days <= 30) {
                    $row->aging_bucket = '0-30';
                    $agingTotals['current'] += $row->value;
                } elseif ($days <= 60) {
                    $row->aging_bucket = '31-60';
                    $agingTotals['30'] += $row->value;
                } elseif ($days <= 90) {
                    $row->aging_bucket = '61-90';
                    $agingTotals['60'] += $row->value;
                } elseif ($days <= 120) {
                    $row->aging_bucket = '91-120';
                    $agingTotals['90'] += $row->value;
                } else {
                    $row->aging_bucket = '120+';
                    $agingTotals['over'] += $row->value;
                }
            }

            return $row;
        });

        return view('admin.Report.inventory_aging_report', [
            'warehouses' => $warehouses,
            'branches' => $branches,
            'categories' => $categories,
            'brands' => $brands,
            'data' => $data,
            'agingTotals' => $agingTotals,
            'branchSelected' => $branchSelected,
            'warehouseSelected' => $request->warehouse_id,
            'categorySelected' => $request->category_id,
            'brandSelected' => $request->brand_id,
        ]);
    }

    public function inventoryVarianceReport(Request $request)
    {
        $inventories = Inventory::query()
            ->with(['warehouse', 'branch'])
            ->orderByDesc('date')
            ->get();

        $selectedInventoryId = $request->has('inventory_id') ? (int) $request->inventory_id : null;
        if ($selectedInventoryId === null && $inventories->isNotEmpty()) {
            $selectedInventoryId = $inventories->first()->id;
        }

        $branchSelected = $request->branch_id ?? 0;
        if (!empty(Auth::user()->branch_id)) {
            $branchSelected = Auth::user()->branch_id;
        }

        $query = InventoryDetails::query()
            ->join('inventorys as i', 'i.id', '=', 'inventory_details.inventory_id')
            ->join('products as p', 'p.id', '=', 'inventory_details.item_id')
            ->leftJoin('warehouses as w', 'w.id', '=', 'i.warehouse_id')
            ->leftJoin('branches as b', 'b.id', '=', 'i.branch_id')
            ->leftJoin('units as u', 'u.id', '=', 'inventory_details.unit')
            ->select(
                'inventory_details.*',
                'p.code as product_code',
                'p.name as product_name',
                'p.cost as product_cost',
                'w.name as warehouse_name',
                'b.branch_name',
                'i.date as inventory_date',
                'i.id as inventory_id',
                'u.name as unit_name',
                'i.branch_id',
                'i.warehouse_id'
            );

        if ($selectedInventoryId) {
            $query->where('inventory_details.inventory_id', $selectedInventoryId);
        }

        if ($branchSelected > 0) {
            $query->where('i.branch_id', $branchSelected);
        }

        if ($request->warehouse_id) {
            $query->where('i.warehouse_id', $request->warehouse_id);
        }

        if ($request->date_from) {
            $query->whereDate('i.date', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('i.date', '<=', $request->date_to);
        }

        $differenceType = $request->difference_type ?? 'all';
        if ($differenceType === 'shortage') {
            $query->whereRaw('inventory_details.new_quantity < inventory_details.quantity');
        } elseif ($differenceType === 'excess') {
            $query->whereRaw('inventory_details.new_quantity > inventory_details.quantity');
        } else {
            $query->whereRaw('inventory_details.new_quantity <> inventory_details.quantity');
        }

        if (Auth::user()->subscriber_id ?? null) {
            if (Schema::hasColumn('inventorys', 'subscriber_id')) {
                $query->where('i.subscriber_id', Auth::user()->subscriber_id);
            }
            if (Schema::hasColumn('inventory_details', 'subscriber_id')) {
                $query->where('inventory_details.subscriber_id', Auth::user()->subscriber_id);
            }
        }

        $data = $query->orderBy('inventory_details.id')->get()->map(function ($row) {
            $row->difference = (float) $row->new_quantity - (float) $row->quantity;
            $row->difference_value = $row->difference * ((float) ($row->product_cost ?? 0));
            return $row;
        });

        $totals = [
            'shortage' => $data->where('difference', '<', 0)->sum('difference_value'),
            'excess' => $data->where('difference', '>', 0)->sum('difference_value'),
        ];

        $siteContrller = new SystemController();
        $warehouses = $siteContrller->getAllWarehouses();
        $branches = $siteContrller->getBranches();

        return view('admin.Report.inventory_variance_report', [
            'inventories' => $inventories,
            'warehouses' => $warehouses,
            'branches' => $branches,
            'data' => $data,
            'totals' => $totals,
            'branchSelected' => $branchSelected,
            'warehouseSelected' => $request->warehouse_id,
            'inventorySelected' => $selectedInventoryId,
            'differenceType' => $differenceType,
            'dateFrom' => $request->date_from,
            'dateTo' => $request->date_to,
        ]);
    }

    public function client_balance_report($id , $slag){
        $data = VendorMovement::query()->where('vendor_id',$id)->get();
        $company = CompanyInfo::first();

        return view('admin.Report.client_movement_report',compact('data' , 'slag','company'));
    }

    public function account_balance(Request $request){
        $startDate = Carbon::now()->addYears(-5);
        $endDate = Carbon::now();

        if($request->has('start_date')){
            $startDate = $request->start_date;
        }

        if($request->has('end_date')){
            $endDate = $request->end_date;
        }

        $accounts = DB::table('accounts_trees')
            ->join('account_movements','accounts_trees.id','=','account_movements.account_id')
            ->select('accounts_trees.code','accounts_trees.name',
                DB::raw('sum(account_movements.credit) as credit'),
                DB::raw('sum(account_movements.debit) as debit'))
            ->groupBy('accounts_trees.id','accounts_trees.code','accounts_trees.name')
            ->where('account_movements.date','>=',$startDate)
            ->where('account_movements.date','<=',$endDate)
            ->get();

        foreach ($accounts as $account){ 
            $accountBalance = DB::table('accounts_trees')
                ->join('account_movements','accounts_trees.id','=','account_movements.account_id')
                ->select('accounts_trees.code','accounts_trees.name',
                    DB::raw('sum(account_movements.credit) as credit'),
                    DB::raw('sum(account_movements.debit) as debit'))
                ->groupBy('accounts_trees.id','accounts_trees.code','accounts_trees.name')
                ->where('account_movements.date','<',$startDate)
                ->where('accounts_trees.code','<',$account->code)
                ->get()->first();

            $account->before_credit = $accountBalance->credit;
            $account->before_debit = $accountBalance->debit;
        }

        return view('admin.Report.account_balance_report',compact('accounts'));
    }

    public function expiryReport(Request $request)
    {
        $branches = Branch::where('status',1)->get();
        $warehouses = Warehouse::all();

        $dateFrom = $request->date_from ?? now()->toDateString();
        $dateTo = $request->date_to ?? now()->addDays(30)->toDateString();

        $data = DB::table('purchase_details as pd')
            ->join('purchases as p','p.id','=','pd.purchase_id')
            ->join('products as pr','pr.id','=','pd.product_id')
            ->join('warehouses as w','w.id','=','pd.warehouse_id')
            ->leftJoin('branches as b','b.id','=','p.branch_id')
            ->select(
                'pr.code','pr.name',
                'pd.batch_no','pd.expiry_date','pd.quantity','pd.warehouse_id',
                'w.name as warehouse_name','b.branch_name',
                DB::raw('DATEDIFF(pd.expiry_date, CURDATE()) as days_to_expiry')
            )
            ->whereNotNull('pd.expiry_date')
            ->whereBetween('pd.expiry_date', [$dateFrom, $dateTo])
            ->when($request->batch_no, fn($q,$v)=>$q->where('pd.batch_no','like','%'.$v.'%'))
            ->when($request->branch_id, fn($q,$v)=>$q->where('p.branch_id',$v))
            ->when($request->warehouse_id, fn($q,$v)=>$q->where('pd.warehouse_id',$v))
            ->when(Auth::user()->branch_id ?? null, fn($q,$v)=>$q->where('p.branch_id',$v))
            ->orderBy('pd.expiry_date','asc')
            ->get();

        if($request->ajax()){
            return response()->json($data);
        }

        return view('admin.Report.expiry_report',[
            'branches'=>$branches,
            'warehouses'=>$warehouses,
            'data'=>$data,
            'dateFrom'=>$dateFrom,
            'dateTo'=>$dateTo,
            'branchSelected'=>$request->branch_id,
            'warehouseSelected'=>$request->warehouse_id,
            'batchSelected'=>$request->batch_no,
        ]);
    }

    public function lowStockReport(Request $request)
    {
        $branches = Branch::where('status',1)->get();
        $warehouses = Warehouse::all();

        $data = WarehouseProducts::query()
            ->join('products','products.id','=','warehouse_products.product_id')
            ->join('warehouses','warehouses.id','=','warehouse_products.warehouse_id')
            ->leftJoin('branches','branches.id','=','warehouses.branch_id')
            ->select(
                'products.code','products.name','products.alert_quantity',
                'warehouse_products.quantity','warehouse_products.cost',
                'warehouses.name as warehouse_name','branches.branch_name','warehouses.branch_id'
            )
            ->where('products.alert_quantity','>',0)
            ->whereColumn('warehouse_products.quantity','<=','products.alert_quantity')
            ->when($request->branch_id, fn($q,$v)=>$q->where('warehouses.branch_id',$v))
            ->when($request->warehouse_id, fn($q,$v)=>$q->where('warehouse_products.warehouse_id',$v))
            ->when(Auth::user()->branch_id ?? null, fn($q,$v)=>$q->where('warehouses.branch_id',$v))
            ->orderBy('warehouse_products.quantity','asc')
            ->get();

        if($request->ajax()){
            return response()->json($data);
        }

        return view('admin.Report.low_stock_report',[
            'branches'=>$branches,
            'warehouses'=>$warehouses,
            'data'=>$data,
            'branchSelected'=>$request->branch_id,
            'warehouseSelected'=>$request->warehouse_id,
        ]);
    }

    public function salonServicesReport(Request $request)
    {
        $dateFrom = $request->date_from ?? now()->subDays(30)->toDateString();
        $dateTo = $request->date_to ?? now()->toDateString();

        $departments = SalonDepartment::query()
            ->when(Auth::user()->subscriber_id ?? null, fn($q,$v) => $q->where('subscriber_id', $v))
            ->orderBy('name')
            ->get();

        $data = DB::table('sale_details as sd')
            ->join('sales as s', 's.id', '=', 'sd.sale_id')
            ->join('products as p', 'p.id', '=', 'sd.product_id')
            ->leftJoin('salon_departments as d', 'd.id', '=', 'p.salon_department_id')
            ->select(
                'p.id',
                'p.name',
                'd.name as department_name',
                DB::raw('SUM(sd.quantity) as quantity'),
                DB::raw('SUM(sd.total) as total'),
                DB::raw('SUM(sd.tax) as tax'),
                DB::raw('SUM(sd.tax_excise) as tax_excise')
            )
            ->whereNotNull('p.salon_department_id')
            ->whereBetween('s.date', [$dateFrom, $dateTo])
            ->when($request->department_id, fn($q,$v) => $q->where('p.salon_department_id', $v))
            ->when(Auth::user()->branch_id ?? null, fn($q,$v) => $q->where('s.branch_id', $v))
            ->when(Auth::user()->subscriber_id ?? null, fn($q,$v) => $q->where('s.subscriber_id', $v))
            ->groupBy('p.id', 'p.name', 'd.name')
            ->orderBy('quantity', 'desc')
            ->get();

        return view('admin.Report.salon_services_report', [
            'departments' => $departments,
            'data' => $data,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'departmentSelected' => $request->department_id,
        ]);
    }

    public function vendorAging(Request $request)
    {
        return $this->companyAgingReport($request, 4);
    }

    public function clientAging(Request $request)
    {
        return $this->companyAgingReport($request, 3);
    }

    public function clientsBalanceReport(Request $request)
    {
        return $this->companyBalanceReport($request, 3);
    }

    public function vendorsBalanceReport(Request $request)
    {
        return $this->companyBalanceReport($request, 4);
    }

    public function clientsMovementReport(Request $request)
    {
        return $this->companyMovementReport($request, 3);
    }

    public function vendorsMovementReport(Request $request)
    {
        return $this->companyMovementReport($request, 4);
    }

    public function representativesReport(Request $request)
    {
        $branches = (new SystemController())->getBranches();
        $representatives = Representative::all();

        $salesQuery = Sales::query()
            ->where('sale_id', 0)
            ->when(Auth::user()->subscriber_id ?? null, fn($q, $v) => $q->where('subscriber_id', $v));

        $purchaseQuery = Purchase::query()
            ->where('returned_bill_id', 0)
            ->when(Auth::user()->subscriber_id ?? null, fn($q, $v) => $q->where('subscriber_id', $v));

        if (!empty(Auth::user()->branch_id)) {
            $salesQuery->where('branch_id', Auth::user()->branch_id);
            $purchaseQuery->where('branch_id', Auth::user()->branch_id);
        } elseif ($request->branch_id) {
            $salesQuery->where('branch_id', $request->branch_id);
            $purchaseQuery->where('branch_id', $request->branch_id);
        }

        if ($request->date_from) {
            $salesQuery->whereDate('date', '>=', $request->date_from);
            $purchaseQuery->whereDate('date', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $salesQuery->whereDate('date', '<=', $request->date_to);
            $purchaseQuery->whereDate('date', '<=', $request->date_to);
        }

        if ($request->representative_id) {
            $salesQuery->where('representative_id', $request->representative_id);
            $purchaseQuery->where('representative_id', $request->representative_id);
        }

        $sales = $salesQuery
            ->select('representative_id', DB::raw('COUNT(*) as invoices'), DB::raw('SUM(net) as net'), DB::raw('SUM(paid) as paid'))
            ->groupBy('representative_id')
            ->get()
            ->keyBy('representative_id');

        $purchases = $purchaseQuery
            ->select('representative_id', DB::raw('SUM(net) as net'))
            ->groupBy('representative_id')
            ->get()
            ->keyBy('representative_id');

        $rows = $representatives->map(function ($rep) use ($sales, $purchases) {
            $salesRow = $sales->get($rep->id);
            $purchaseRow = $purchases->get($rep->id);
            $net = (float) ($salesRow->net ?? 0);
            $paid = (float) ($salesRow->paid ?? 0);
            return [
                'id' => $rep->id,
                'name' => $rep->user_name,
                'invoices' => (int) ($salesRow->invoices ?? 0),
                'sales_net' => $net,
                'sales_paid' => $paid,
                'sales_remain' => $net - $paid,
                'purchase_net' => (float) ($purchaseRow->net ?? 0),
            ];
        })->filter(function ($row) use ($request) {
            if ($request->representative_id) {
                return $row['id'] == $request->representative_id;
            }
            return true;
        })->values();

        $totals = [
            'invoices' => $rows->sum('invoices'),
            'sales_net' => $rows->sum('sales_net'),
            'sales_paid' => $rows->sum('sales_paid'),
            'sales_remain' => $rows->sum('sales_remain'),
            'purchase_net' => $rows->sum('purchase_net'),
        ];

        return view('admin.Report.representatives_report', [
            'branches' => $branches,
            'representatives' => $representatives,
            'rows' => $rows,
            'totals' => $totals,
            'branchSelected' => $request->branch_id,
            'dateFrom' => $request->date_from,
            'dateTo' => $request->date_to,
            'representativeSelected' => $request->representative_id,
        ]);
    }

    private function companyBalanceReport(Request $request, int $groupId)
    {
        $branches = (new SystemController())->getBranches();
        $companies = Company::where('group_id', $groupId)->orderBy('name')->get();
        $representatives = Representative::all();

        $query = VendorMovement::query()
            ->join('companies', 'companies.id', '=', 'vendor_movements.vendor_id')
            ->leftJoin('representatives', 'representatives.id', '=', 'companies.representative_id_')
            ->select(
                'vendor_movements.vendor_id',
                'companies.name as company_name',
                'representatives.user_name as representative_name',
                DB::raw('SUM(vendor_movements.debit) as debit'),
                DB::raw('SUM(vendor_movements.credit) as credit')
            )
            ->where('companies.group_id', $groupId);

        if (Auth::user()->subscriber_id ?? null) {
            if (Schema::hasColumn('vendor_movements', 'subscriber_id')) {
                $query->where('vendor_movements.subscriber_id', Auth::user()->subscriber_id);
            }
            if (Schema::hasColumn('companies', 'subscriber_id')) {
                $query->where('companies.subscriber_id', Auth::user()->subscriber_id);
            }
        }

        if (!empty(Auth::user()->branch_id)) {
            $query->where('vendor_movements.branch_id', Auth::user()->branch_id);
        } elseif ($request->branch_id) {
            $query->where('vendor_movements.branch_id', $request->branch_id);
        }

        if ($request->company_id) {
            $query->where('vendor_movements.vendor_id', $request->company_id);
        }

        if ($request->representative_id) {
            $query->where('companies.representative_id_', $request->representative_id);
        }

        if ($request->date_from) {
            $query->whereDate('vendor_movements.date', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('vendor_movements.date', '<=', $request->date_to);
        }

        $rows = $query
            ->groupBy('vendor_movements.vendor_id', 'companies.name', 'representatives.user_name')
            ->orderBy('companies.name')
            ->get()
            ->map(function ($row) use ($groupId) {
                $debit = (float) $row->debit;
                $credit = (float) $row->credit;
                $row->balance = $groupId === 3 ? ($debit - $credit) : ($credit - $debit);
                return $row;
            });

        $totalBalance = $rows->sum('balance');

        return view($groupId === 3 ? 'admin.Report.clients_balance_report' : 'admin.Report.vendors_balance_report', [
            'rows' => $rows,
            'branches' => $branches,
            'companies' => $companies,
            'representatives' => $representatives,
            'totalBalance' => $totalBalance,
            'branchSelected' => $request->branch_id,
            'companySelected' => $request->company_id,
            'representativeSelected' => $request->representative_id,
            'dateFrom' => $request->date_from,
            'dateTo' => $request->date_to,
        ]);
    }

    private function companyMovementReport(Request $request, int $groupId)
    {
        $branches = (new SystemController())->getBranches();
        $companies = Company::where('group_id', $groupId)->orderBy('name')->get();
        $representatives = Representative::all();

        $query = VendorMovement::query()
            ->join('companies', 'companies.id', '=', 'vendor_movements.vendor_id')
            ->leftJoin('representatives', 'representatives.id', '=', 'companies.representative_id_')
            ->select(
                'vendor_movements.*',
                'companies.name as company_name',
                'representatives.user_name as representative_name'
            )
            ->where('companies.group_id', $groupId);

        if (Auth::user()->subscriber_id ?? null) {
            if (Schema::hasColumn('vendor_movements', 'subscriber_id')) {
                $query->where('vendor_movements.subscriber_id', Auth::user()->subscriber_id);
            }
            if (Schema::hasColumn('companies', 'subscriber_id')) {
                $query->where('companies.subscriber_id', Auth::user()->subscriber_id);
            }
        }

        if (!empty(Auth::user()->branch_id)) {
            $query->where('vendor_movements.branch_id', Auth::user()->branch_id);
        } elseif ($request->branch_id) {
            $query->where('vendor_movements.branch_id', $request->branch_id);
        }

        if ($request->company_id) {
            $query->where('vendor_movements.vendor_id', $request->company_id);
        }

        if ($request->representative_id) {
            $query->where('companies.representative_id_', $request->representative_id);
        }

        if ($request->date_from) {
            $query->whereDate('vendor_movements.date', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('vendor_movements.date', '<=', $request->date_to);
        }

        $rows = $query->orderBy('vendor_movements.date')->get();

        $totalDebit = $rows->sum('debit');
        $totalCredit = $rows->sum('credit');
        $balance = $groupId === 3 ? ($totalDebit - $totalCredit) : ($totalCredit - $totalDebit);

        return view($groupId === 3 ? 'admin.Report.clients_movement_report' : 'admin.Report.vendors_movement_report', [
            'rows' => $rows,
            'branches' => $branches,
            'companies' => $companies,
            'representatives' => $representatives,
            'totalDebit' => $totalDebit,
            'totalCredit' => $totalCredit,
            'balance' => $balance,
            'branchSelected' => $request->branch_id,
            'companySelected' => $request->company_id,
            'representativeSelected' => $request->representative_id,
            'dateFrom' => $request->date_from,
            'dateTo' => $request->date_to,
        ]);
    }

    private function companyAgingReport(Request $request, int $groupId)
    {
        $branches = (new SystemController())->getBranches();
        $companies = Company::where('group_id', $groupId)->orderBy('name')->get();
        $representatives = Representative::all();

        $query = VendorMovement::query()
            ->join('companies', 'companies.id', '=', 'vendor_movements.vendor_id')
            ->leftJoin('representatives', 'representatives.id', '=', 'companies.representative_id_')
            ->select(
                'vendor_movements.vendor_id',
                'vendor_movements.debit',
                'vendor_movements.credit',
                'vendor_movements.date',
                'companies.name as company_name',
                'representatives.user_name as representative_name'
            )
            ->where('companies.group_id', $groupId);

        if (Auth::user()->subscriber_id ?? null) {
            if (Schema::hasColumn('vendor_movements', 'subscriber_id')) {
                $query->where('vendor_movements.subscriber_id', Auth::user()->subscriber_id);
            }
            if (Schema::hasColumn('companies', 'subscriber_id')) {
                $query->where('companies.subscriber_id', Auth::user()->subscriber_id);
            }
        }

        if (!empty(Auth::user()->branch_id)) {
            $query->where('vendor_movements.branch_id', Auth::user()->branch_id);
        } elseif ($request->branch_id) {
            $query->where('vendor_movements.branch_id', $request->branch_id);
        }

        if ($request->company_id) {
            $query->where('vendor_movements.vendor_id', $request->company_id);
        }

        if ($request->representative_id) {
            $query->where('companies.representative_id_', $request->representative_id);
        }

        if ($request->date_from) {
            $query->whereDate('vendor_movements.date', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('vendor_movements.date', '<=', $request->date_to);
        }

        $movements = $query->orderBy('vendor_movements.date')->get();

        $report = $movements->groupBy('vendor_id')->map(function ($rows) use ($groupId) {
            $aging = ['current' => 0, '30' => 0, '60' => 0, '90' => 0, 'over' => 0];
            $company = $rows->first();
            foreach ($rows as $row) {
                $days = now()->diffInDays(Carbon::parse($row->date));
                $val = $groupId === 3
                    ? ((float) $row->debit - (float) $row->credit)
                    : ((float) $row->credit - (float) $row->debit);
                if ($days <= 30) {
                    $aging['current'] += $val;
                } elseif ($days <= 60) {
                    $aging['30'] += $val;
                } elseif ($days <= 90) {
                    $aging['60'] += $val;
                } elseif ($days <= 120) {
                    $aging['90'] += $val;
                } else {
                    $aging['over'] += $val;
                }
            }
            return [
                'company_id' => $rows->first()->vendor_id,
                'company' => $company->company_name ?? '',
                'representative_name' => $company->representative_name ?? '',
                'balance' => array_sum($aging),
                'aging' => $aging,
            ];
        })->values();

        return view($groupId === 3 ? 'admin.Report.clients_aging_report' : 'admin.Report.vendors_aging_report', [
            'report' => $report,
            'branches' => $branches,
            'companies' => $companies,
            'representatives' => $representatives,
            'branchSelected' => $request->branch_id,
            'companySelected' => $request->company_id,
            'representativeSelected' => $request->representative_id,
            'dateFrom' => $request->date_from,
            'dateTo' => $request->date_to,
        ]);
    }
}

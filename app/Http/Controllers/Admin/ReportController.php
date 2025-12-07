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
use App\Models\Sales;
use App\Models\Purchase;
use App\Models\Branch;
use App\Models\WarehouseProducts;
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
        return view('admin.Report.daily_sales_report' , compact('warehouses','branches','customers'));
    }

    public function daily_sales_report_search($date , $warehouse, $branch_id, $customer_id = 0, $vehicle_plate = 'empty'){
        $query = Sales::with(['branch','warehouse','customer'])
                    ->where('sale_id',0)
                    ->when(Auth::user()->subscriber_id ?? null, function($q,$sub){
                        $q->where('subscriber_id',$sub);
                    })
                    ->whereDate('date',$date);

        if( $warehouse >0 ) $query->where('warehouse_id',$warehouse);  
        if( $branch_id >0 ) $query->where('branch_id',$branch_id);
        if( $customer_id >0 ) $query->where('customer_id',$customer_id);
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

        return view('admin.Report.sales_item_report' , compact('warehouses','vendors','branches'));
    }

    public function sales_item_report_search($fdate,$tdate,$warehouse,$branch_id,$item_id,$supplier,$vehicle_plate = 'empty' ){

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
        return view('admin.Report.sales_return_report' , compact('warehouses','vendors','branches'));
    }

    public function sales_return_report_search($fdate, $tdate, $warehouse,$bill_no,$vendor,$branch_id){

        $data = Sales::where('sale_id' , '<>' , 0)
                    ->get();
                    
        if($fdate) $data = $data->where('date','>=',\Carbon\Carbon::parse($fdate)->format('Y-m-d'));
        if($tdate) $data = $data->where('date','<=',\Carbon\Carbon::parse($tdate)->format('Y-m-d'));
        if($warehouse > 0) $data = $data->where('warehouse_id',$warehouse);
        if(isset($bill_no) and $bill_no<>'empty') $data = $data->where('invoice_no',$bill_no);
        if($vendor > 0) $data = $data->where('customer_id',$vendor);
        if($branch_id > 0) $data = $data->where('branch_id',$branch_id);    

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

        return view('admin.Report.purchase_report' , compact('warehouses', 'vendors','branches'));
    }

    public function purchase_report_search($fdate,$tdate, $warehouse,$bill_no,$vendor,$branch_id){

        $data = Purchase::where('returned_bill_id', 0)
                    ->get();
        
        if($fdate) $data = $data->where('date','>=',\Carbon\Carbon::parse($fdate)->format('Y-m-d'));
        if($tdate) $data = $data->where('date','<=',\Carbon\Carbon::parse($tdate)->format('Y-m-d'));
        if($warehouse > 0) $data = $data->where('warehouse_id',$warehouse); 
        if(isset($bill_no) and $bill_no<>'empty') $data = $data->where('invoice_no',$bill_no);
        if($vendor > 0) $data = $data->where('customer_id',$vendor);
        if($branch_id > 0) $data = $data->where('branch_id',$branch_id);    
       
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
        return view('admin.Report.purchase_return_report' , compact('warehouses','vendors','branches'));
    }

    public function purchases_return_report_search($fdate, $tdate, $warehouse,$bill_no,$vendor,$branch_id){

        $data = Purchase::where('returned_bill_id' , '<>' , 0)
                    ->get();
                    
        if($fdate) $data = $data->where('date','>=',\Carbon\Carbon::parse($fdate)->format('Y-m-d'));
        if($tdate) $data = $data->where('date','<=',\Carbon\Carbon::parse($tdate)->format('Y-m-d'));
        if($warehouse > 0) $data = $data->where('warehouse_id',$warehouse);
        if(isset($bill_no) and $bill_no<>'empty') $data = $data->where('invoice_no',$bill_no);
        if($vendor > 0) $data = $data->where('customer_id',$vendor);
        if($branch_id > 0) $data = $data->where('branch_id',$branch_id);    

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

    public function vendorAging(Request $request)
    {
        $vendors = VendorMovement::select('vendor_id')
            ->selectRaw('SUM(debit - credit) as balance')
            ->groupBy('vendor_id')
            ->with('company')
            ->get();

        $report = [];
        foreach($vendors as $v){
            $movs = VendorMovement::where('vendor_id',$v->vendor_id)->orderBy('date')->get();
            $aging = ['current'=>0,'30'=>0,'60'=>0,'90'=>0,'over'=>0];
            foreach($movs as $mv){
                $days = now()->diffInDays(Carbon::parse($mv->date));
                $val = ($mv->debit - $mv->credit);
                if($days <= 30) $aging['current'] += $val;
                elseif($days <= 60) $aging['30'] += $val;
                elseif($days <= 90) $aging['60'] += $val;
                elseif($days <= 120) $aging['90'] += $val;
                else $aging['over'] += $val;
            }
            $report[] = [
                'vendor' => optional($v->company)->name ?? '',
                'balance' => $v->balance,
                'aging' => $aging,
            ];
        }

        return view('admin.Report.vendor_aging', compact('report'));
    }
}

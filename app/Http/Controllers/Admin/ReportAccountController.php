<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccountsTree;
use App\Models\Category;
use App\Models\Company;
use App\Models\CompanyMovement;
use App\Models\EnterMoney; 
use App\Models\ExitMoney; 
use App\Models\Holder;
use App\Models\Product; 
use App\Models\Warehouse;
use App\Models\CompanyInfo;
use App\Models\SaleDetails ; 
use App\Models\PurchaseDetails; 
use App\Models\Inventory; 
use App\Models\InventoryDetails;  
use App\Models\Branch;
use App\Models\CostCenter;
use App\Models\Sales;
use App\Models\Purchase;
use Carbon\Carbon; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class ReportAccountController extends Controller
{
    //
    public function item_list_report(){
        $karats = Karat::all();
        $categories = Category::all();
        $pricings = Pricing::all();
        $branches = Branch::where('status',1)->get();

        return view('admin.ReportAccount.item_list_report' 
            , compact('karats' , 'categories','branches'));
    }

    public function item_list_report_search(Request $request){
        $items = Item::with('karat' , 'category') -> where('item_type' , '<>' , 2);

        if($request -> branch_id > 0) $items = $items -> where('branch_id' ,$request -> branch_id );
        if($request -> category > 0) $items = $items -> where('category_id' , '=' ,$request -> category );

        if($request -> karat > 0) $items = $items -> where('karat_id' , '=' ,$request -> karat ) -> get();
        if($request -> code != null ) $items = $items -> where('code' , '=' , $request ->code ) -> get();
        if($request -> name != null) $items = $items->where('name_ar' , 'like' , '%'.$request -> name .'%') -> get();
        if($request -> weight > 0) $items = $items -> where('weight' , '=' ,$request -> weight ) -> get();


        if($request -> karat == 0  && $request -> category == 0 &&
            $request -> code == null && $request -> name == null && $request -> weight == 0){
            $data = $items -> get();

        } else {
            $data = $items ;
        }

        $fcode = $request -> fcode ?? '000001';
        $tcode = $request -> tcode ?? '999999';
        $items2 = [] ;

        foreach ($data as $item){
            if((int)$item -> code  >= (int) $fcode && (int)$item -> code  <= (int) $tcode){
                array_push($items2 , $item);
            }
        }

        if(!$request -> fcode && ! $request -> tcode ){
            $items2 = $data ;
        }
 
        $company = CompanyInfo::all() -> first();
        $branch = Branch::find($request -> branch_id);

        return view('admin.ReportAccount.item_list_report_result' , ['data' => $items2 ,'branch'=>$branch, 'company' => $company])  ;
    }


    public function sold_items_report(){
        $karats = Karat::all();
        $categories = Category::all(); 
        $branches = Branch::where('status',1)->get();

        return view('admin.ReportAccount.sold_item_list_report' , compact('karats' , 'categories','branches'));
    }

    public function sold_items_report_search(Request $request){ 

        $items = DB::table('sale_details')
            -> join('sales' , 'sale_details.sale_id' , '=' , 'sales.id')
            -> join('karats' , 'sale_details.karat_id' , '=' , 'karats.id')
            ->join('items' , 'sale_details.item_id' , '=' , 'items.id')
            -> select('items.*' , 'karats.name_ar as karat_name_ar' , 'karats.name_en as karat_name_en' ,
                'sale_details.sale_id as bill_id' , 'sales.date as bill_date' ,'sales.bill_number as bill_no')
            -> where('sales.total_money' , '>' , 0)
            -> when(Auth::user()->subscriber_id ?? null,function($q,$sub){
                if (Schema::hasColumn('sale_details','subscriber_id')) {
                    $q->where('sale_details.subscriber_id',$sub);
                }
            }); 

        $items_t = DB::table('exit_work_tax_details')
            -> join('sales_tax' , 'exit_work_tax_details.bill_id' , '=' , 'sales_tax.id')
            -> join('karats' , 'exit_work_tax_details.karat_id' , '=' , 'karats.id')
            ->join('items' , 'exit_work_tax_details.item_id' , '=' , 'items.id')
            -> select('items.*' , 'karats.name_ar as karat_name_ar' , 'karats.name_en as karat_name_en' ,
                'exit_work_tax_details.bill_id as bill_id' , 'sales_tax.date as bill_date' ,'sales_tax.bill_number as bill_no')
            -> where('sales_tax.total_money' , '>' , 0)
            -> when(Auth::user()->subscriber_id ?? null,function($q,$sub){
                if (Schema::hasColumn('exit_work_tax_details','subscriber_id')) {
                    $q->where('exit_work_tax_details.subscriber_id',$sub);
                }
            });  

        if($request -> branch_id > 0) $items = $items -> where('items.branch_id' ,$request -> branch_id );
        if($request -> karat > 0) $items = $items -> where('items.karat_id' , '=' ,$request -> karat ) -> get();
        if($request -> code != null ) $items = $items -> where('items.code' , '=' , $request ->code ) -> get();
        if($request -> name != null) $items = $items->where('items.name_ar' , 'like' , '%'.$request -> name .'%') -> get();
        if($request -> weight > 0) $items = $items -> where('items.weight' , '=' ,$request -> weight ) -> get();

        //if($request -> branch_id > 0) $items_t = $items_t -> where('items.branch_id' ,$request -> branch_id );
        if($request -> karat > 0) $items_t = $items_t -> where('items.karat_id' , '=' ,$request -> karat ) -> get();
        if($request -> code != null ) $items_t = $items_t -> where('items.code' , '=' , $request ->code ) -> get();
        if($request -> name != null) $items_t = $items_t->where('items.name_ar' , 'like' , '%'.$request -> name .'%') -> get();
        if($request -> weight > 0) $items_t = $items_t -> where('items.weight' , '=' ,$request -> weight ) -> get();
                
        if($request -> karat == 0  &&
            $request -> code == null && $request -> name == null && $request -> weight == 0){
            $data1 = $items -> get();
            $data2 = $items_t -> get();

        } else {
            $data1 = $items ;
            $data2 = $items_t ;
        }
  
        //$all = $data1  -> merge($data2);
        $all = $data1 -> mergeRecursive($data2);

        if($request -> has('isStartDate')) $all = $all -> where('bill_date' , '>=' , Carbon::parse($request -> StartDate) -> startOfDay());
        if($request -> has('isEndDate'))   $all = $all -> where('bill_date' , '<=' , Carbon::parse($request -> EndDate) -> addDay());
 
        $startDate = Carbon::now()->addYears(-5);
        $endDate = Carbon::now() -> addDays(1);

        $period = 'Period : ';
        $period_ar = 'الفترة  :';
        if($request -> has('isStartDate')){
            $startDate = $request->StartDate; 
            $period .= $startDate ;
            $period_ar .= $startDate ;
        } else {
            $period .= 'Starting Date';
            $period_ar .= 'من البداية' ;
        }

        if($request -> has('isEndDate')){
            $endDate =  Carbon::parse($request->EndDate) -> addDay()  ; 
            $period .= ' -- '  . $endDate -> format('d-m-Y') ;
            $period_ar .= ' -- '  . $endDate -> format('d-m-Y');
        } else {
            $period .= ' -- '  . 'Today' ;
            $period_ar .= ' -- '  . 'حتى اليوم' ;
        }

        $company = CompanyInfo::all() -> first();
        $branch = Branch::find($request -> branch_id);

        return view('admin.ReportAccount.sold_item_list_report_result' , compact('all', 'branch', 'period', 'period_ar' , 'company'))  ;
    }

    public function sales_report(){
        $branches = Branch::where('status',1)->get(); 
        return view('admin.ReportAccount.sales_report', compact('branches'));
    }

    public function sales_report_search(Request $request){
        $data = DB::table('sale_details')
            -> join('sales' , 'sale_details.sale_id' , '=' , 'sales.id')
            ->join('items' , 'sale_details.item_id' , '=' , 'items.id')
            ->join('karats' , 'sale_details.karat_id' , '=' , 'karats.id')
            ->select('sales.branch_id','sales.bill_number' , 'sales.date' , 'sales.id' ,  'sales.client_id as client_id',
                'sales.discount', 'items.name_ar as item_name_ar' , 'items.name_en as item_name_en'
                ,'karats.name_ar as karat_name_ar' , 'karats.name_en as karat_name_en' , 'sale_details.weight' , 'sale_details.gram_price' ,
                'sale_details.gram_manufacture' , 'sale_details.tax','sale_details.net_money' , 'sale_details.karat_id')
            -> where('sales.net_money' ,'>' , 0)
            -> when(Auth::user()->subscriber_id ?? null,function($q,$sub){
                if (Schema::hasColumn('sale_details','subscriber_id')) {
                    $q->where('sale_details.subscriber_id',$sub);
                }
            })
            -> orderBy('sales.id');


        $data2 = DB::table('exit_old_details')
            -> join('exit_olds' , 'exit_old_details.bill_id' , '=' , 'exit_olds.id')
            ->join('karats' , 'exit_old_details.karat_id' , '=' , 'karats.id')
            ->select('exit_olds.branch_id','exit_olds.bill_number' , 'exit_olds.date' , 'exit_olds.discount' ,'exit_olds.id' , 'exit_olds.supplier_id as client_id'
                ,'karats.name_ar as karat_name_ar' , 'karats.name_en as karat_name_en' , 'exit_old_details.weight' , 'exit_old_details.gram_price' ,
                'exit_old_details.gram_manufacture' , 'exit_old_details.tax','exit_old_details.net_money' , 'exit_old_details.karat_id')
            -> where('exit_olds.net_money' ,'>' , 0)
            -> when(Auth::user()->subscriber_id ?? null,function($q,$sub){
                if (Schema::hasColumn('exit_old_details','subscriber_id')) {
                    $q->where('exit_old_details.subscriber_id',$sub);
                }
            })
            -> orderBy('exit_olds.id');
        
        $data3 = DB::table('exit_work_tax_details')
            -> join('sales_tax' , 'exit_work_tax_details.bill_id' , '=' , 'sales_tax.id')
            ->join('items' , 'exit_work_tax_details.item_id' , '=' , 'items.id')
            ->join('karats' , 'exit_work_tax_details.karat_id' , '=' , 'karats.id')
            ->select('sales_tax.branch_id','sales_tax.bill_number' , 'sales_tax.date' , 'sales_tax.id' ,  'sales_tax.client_id as client_id',
                'sales_tax.discount', 'items.name_ar as item_name_ar' , 'items.name_en as item_name_en'
                ,'karats.name_ar as karat_name_ar' , 'karats.name_en as karat_name_en' , 'exit_work_tax_details.weight' , 'exit_work_tax_details.gram_price' ,
                'exit_work_tax_details.gram_manufacture' , 'exit_work_tax_details.tax','exit_work_tax_details.net_money' , 'exit_work_tax_details.karat_id')
            -> where('sales_tax.net_money' ,'>' , 0)
            -> when(Auth::user()->subscriber_id ?? null,function($q,$sub){
                if (Schema::hasColumn('exit_work_tax_details','subscriber_id')) {
                    $q->where('exit_work_tax_details.subscriber_id',$sub);
                }
            })
            -> orderBy('sales_tax.id');            

        $data4 = DB::table('exit_old_tax_details')
            -> join('exit_olds_tax' , 'exit_old_tax_details.bill_id' , '=' , 'exit_olds_tax.id')
            ->join('karats' , 'exit_old_tax_details.karat_id' , '=' , 'karats.id')
            ->select('exit_olds_tax.branch_id','exit_olds_tax.bill_number' , 'exit_olds_tax.date' , 'exit_olds_tax.discount' ,'exit_olds_tax.id' , 'exit_olds_tax.supplier_id as client_id'
                ,'karats.name_ar as karat_name_ar' , 'karats.name_en as karat_name_en' , 'exit_old_tax_details.weight' , 'exit_old_tax_details.gram_price' ,
                'exit_old_tax_details.gram_manufacture' , 'exit_old_tax_details.tax','exit_old_tax_details.net_money' , 'exit_old_tax_details.karat_id')
            -> where('exit_olds_tax.net_money' ,'>' , 0)
            -> when(Auth::user()->subscriber_id ?? null,function($q,$sub){
                if (Schema::hasColumn('exit_old_tax_details','subscriber_id')) {
                    $q->where('exit_old_tax_details.subscriber_id',$sub);
                }
            })
            -> orderBy('exit_olds_tax.id'); 

        if($request -> branch_id > 0) $data = $data -> where('sales.branch_id' ,$request -> branch_id );
        if($request -> has('isStartDate')) $data = $data -> where('date' , '>=' , Carbon::parse($request -> StartDate) );
        if($request -> has('isEndDate'))   $data = $data -> where('date' , '<=' , Carbon::parse($request -> EndDate) -> addDay());

        if($request -> branch_id > 0) $data2 = $data2 -> where('exit_olds.branch_id' ,$request -> branch_id );
        if($request -> has('isStartDate')) $data2 = $data2 -> where('date' , '>=' , Carbon::parse($request -> StartDate) );
        if($request -> has('isEndDate'))   $data2 = $data2 -> where('date' , '<=' , Carbon::parse($request -> EndDate) -> addDay());

        if($request -> branch_id > 0) $data3 = $data3 -> where('sales_tax.branch_id' ,$request -> branch_id );
        if($request -> has('isStartDate')) $data3 = $data3 -> where('date' , '>=' , Carbon::parse($request -> StartDate) );
        if($request -> has('isEndDate'))   $data3 = $data3 -> where('date' , '<=' , Carbon::parse($request -> EndDate) -> addDay());

        if($request -> branch_id > 0) $data4 = $data4 -> where('exit_olds_tax.branch_id' ,$request -> branch_id );
        if($request -> has('isStartDate')) $data4 = $data4 -> where('date' , '>=' , Carbon::parse($request -> StartDate) );
        if($request -> has('isEndDate'))   $data4 = $data4 -> where('date' , '<=' , Carbon::parse($request -> EndDate) -> addDay());

        if($request ->FromBillNumber ) {
            $fromBill = substr($request -> FromBillNumber , 5  );
            $prefix = substr($request -> FromBillNumber , 0 , 5 );
            if($prefix  == 'SWSI-'){
                $bil = ExitWork::where('bill_number' , '=' , $request -> FromBillNumber) -> first();
                if($bil){
                    $data2 = [];
                    $data = $data -> where('sales.id' , '>=' , $bil -> id );
                }
            }elseif($prefix  == 'SOSI-'){
                $bil = ExitOld::where('bill_number' , '=' , $request -> FromBillNumber) -> first();
                if($bil){
                    $data= [];
                    $data2 = $data2 -> where('exit_olds.id' , '>=' , $bil -> id );
                }

            }elseif($prefix  == 'SWSIX-'){
                $bil = ExitWorkTax::where('bill_number' , '=' , $request -> FromBillNumber) -> first();
                if($bil){
                    $data4 = [];
                    $data3 = $data3 -> where('sales_tax.id' , '>=' , $bil -> id );
                }
            }else{
                $bil = ExitOldTax::where('bill_number' , '=' , $request -> FromBillNumber) -> first();
                if($bil){
                    $data3= [];
                    $data4 = $data4 -> where('exit_olds_tax.id' , '>=' , $bil -> id );
                }
            }
        }

        if($request ->ToBillNumber ) {
            $fromBill = substr($request -> ToBillNumber , 5  );
            $prefix = substr($request -> ToBillNumber , 0 , 5 );
            if($prefix  == 'SWSI-'){
                $bil = ExitWork::where('bill_number' , '=' , $request -> ToBillNumber) -> first();
                if($bil){
                    $data2 = [];
                    $data = $data -> where('sales.id' , '<=' , $bil -> id );
                }

            }elseif($prefix  == 'SOSI-'){
                $bil = ExitOld::where('bill_number' , '=' , $request -> ToBillNumber) -> first();
                if($bil){
                    $data= [];
                    $data2 = $data2 -> where('exit_olds.id' , '<=' , $bil -> id );
                }

            }elseif($prefix  == 'SWSIX-'){
                $bil = ExitWorkTax::where('bill_number' , '=' , $request -> ToBillNumber) -> first();
                if($bil){
                    $data4 = [];
                    $data3 = $data3 -> where('sales_tax.id' , '<=' , $bil -> id );
                }
            }else{
                $bil = ExitOldTax::where('bill_number' , '=' , $request -> ToBillNumber) -> first();
                if($bil){
                    $data3= [];
                    $data4 = $data4 -> where('exit_olds_tax.id' , '<=' , $bil -> id );
                } 
            }
        }

        $bills = array();
        $data22 =[] ;

        foreach (is_array($data) ? $data   : $data -> get()  as $bill){
            $client = Company::find($bill -> client_id);
            if($client)
                $bill -> client =   $client -> name ;
            else
                $bill -> client = '';
            $bill -> type = 1 ;
            array_push($bills , $bill);
        }

        foreach (is_array($data2) ? $data2   : $data2 -> get() as $bill){
            $client = Company::find($bill -> client_id);
            if($client)
                $bill -> client =   $client -> name ;
            else
                $bill -> client = '';

            $bill -> type = 0 ;
            $bill -> item_name_ar  = '--';
            $bill -> item_name_en  = '--';
            array_push($bills , $bill);
            array_push($data22 , $bill);
        }
 
        $data44 =[] ;

        foreach (is_array($data3) ? $data3   : $data3 -> get()  as $bill){
            $client = Company::find($bill -> client_id);
            if($client)
                $bill -> client =   $client -> name ;
            else
                $bill -> client = '';
            $bill -> type = 1 ;
            array_push($bills , $bill);
        }

        foreach (is_array($data4) ? $data4   : $data4 -> get() as $bill){
            $client = Company::find($bill -> client_id);
            if($client)
                $bill -> client =   $client -> name ;
            else
                $bill -> client = '';

            $bill -> type = 0 ;
            $bill -> item_name_ar  = '--';
            $bill -> item_name_en  = '--';
            array_push($bills , $bill);
            array_push($data44 , $bill);

        }

        $all1 = $data -> get() -> merge($data22);
        $all2 = $data3 -> get() -> merge($data44); 

        $all = $all1 -> merge($all2);

        $grouped_ar = $all   -> groupBy('karat_name_ar');
        $grouped_en = $all   -> groupBy('karat_name_en');

        $startDate = Carbon::now()->addYears(-5);
        $endDate = Carbon::now() -> addDays(1);

        $period = 'Period : ';
        $period_ar = 'الفترة  :';
        if($request -> has('isStartDate')){
            $startDate = $request->StartDate;
            $period .= $startDate ;
            $period_ar .= $startDate ;
        } else {
            $period .= 'Starting Date';
            $period_ar .= 'من البداية' ;
        }

        if($request -> has('isEndDate')){
            $endDate =  Carbon::parse($request->EndDate) -> addDay()  ;
            $period .= ' -- '  . $endDate -> format('d-m-Y') ;
            $period_ar .= ' -- '  . $endDate -> format('d-m-Y');
        } else {
            $period .= ' -- '  . 'Today' ;
            $period_ar .= ' -- '  . 'حتى اليوم' ;
        }

        $company = CompanyInfo::all() -> first();
        $branch = Branch::find($request -> branch_id);

        return view('admin.ReportAccount.sales_report_result' , compact('bills', 'branch', 'grouped_ar','grouped_en' , 'period' , 'period_ar' ,'company' ))  ;
    }


    public function sales_collectible_report(){
        $branches = Branch::where('status',1)->get();  
        return view('admin.ReportAccount.sales_collectible_report', compact('branches'))  ;
    }

    public function sales_collectible_report_search(Request $request){

        $data = DB::table('sale_collectibles_details')
            -> join('sale_collectibles' , 'sale_collectibles_details.bill_id' , '=' , 'sale_collectibles.id')
            ->join('items_collectibles' , 'sale_collectibles_details.item_id' , '=' , 'items_collectibles.id')
            ->join('karats' , 'sale_collectibles_details.karat_id' , '=' , 'karats.id')
            ->select('sale_collectibles.branch_id' ,'sale_collectibles.bill_number' , 'sale_collectibles.date' , 'sale_collectibles.id' ,  'sale_collectibles.client_id as client_id',
                'sale_collectibles.discount', 'items_collectibles.name_ar as item_name_ar' , 'items_collectibles.name_en as item_name_en'
                ,'karats.name_ar as karat_name_ar' , 'karats.name_en as karat_name_en' , 'sale_collectibles_details.weight' , 'sale_collectibles_details.gram_price' ,
                'sale_collectibles_details.gram_manufacture' , 'sale_collectibles_details.tax','sale_collectibles_details.net_money' , 'sale_collectibles_details.karat_id')
            -> where('sale_collectibles.net_money' ,'>' , 0)
            -> orderBy('sale_collectibles.id');

     
        if($request -> branch_id > 0) $data = $data -> where('sale_collectibles.branch_id' ,$request -> branch_id );
        if($request -> has('isStartDate')) $data = $data -> where('date' , '>=' , Carbon::parse($request -> StartDate) );
        if($request -> has('isEndDate'))   $data = $data -> where('date' , '<=' , Carbon::parse($request -> EndDate) -> addDay());
 
        if($request ->FromBillNumber ) {
            $fromBill = substr($request -> FromBillNumber , 5  );
            $prefix = substr($request -> FromBillNumber , 0 , 5 );
            if($prefix  == 'SWSIC-'){
                $bil = SaleCollectible::where('bill_number' , '=' , $request -> FromBillNumber) -> first();
                if($bil){ 
                    $data = $data -> where('sale_collectibles.id' , '>=' , $bil -> id );
                }
            } 
        }

        if($request ->ToBillNumber ) {
            $fromBill = substr($request -> ToBillNumber , 5  );
            $prefix = substr($request -> ToBillNumber , 0 , 5 );
            if($prefix  == 'SWSIC-'){
                $bil = SaleCollectible::where('bill_number' , '=' , $request -> ToBillNumber) -> first();
                if($bil){ 
                    $data = $data -> where('sale_collectibles.id' , '<=' , $bil -> id );
                }

            } 
        }

        $bills = array();
        $data22 =[] ;

        foreach (is_array($data) ? $data   : $data -> get()  as $bill){
            $client = Company::find($bill -> client_id);
            if($client)
                $bill -> client =   $client -> name ;
            else
                $bill -> client = '';
            $bill -> type = 1 ;
            array_push($bills , $bill);
        } 

        $all = $data -> get();  

        $grouped_ar = $all   -> groupBy('karat_name_ar');
        $grouped_en = $all   -> groupBy('karat_name_en');

        $startDate = Carbon::now()->addYears(-5);
        $endDate = Carbon::now() -> addDays(1);

        $period = 'Period : ';
        $period_ar = 'الفترة  :';
        if($request -> has('isStartDate')){
            $startDate = $request->StartDate;
            $period .= $startDate ;
            $period_ar .= $startDate ;
        } else {
            $period .= 'Starting Date';
            $period_ar .= 'من البداية' ;
        }

        if($request -> has('isEndDate')){
            $endDate =  Carbon::parse($request->EndDate) -> addDay()  ;
            $period .= ' -- '  . $endDate -> format('d-m-Y') ;
            $period_ar .= ' -- '  . $endDate -> format('d-m-Y');
        } else {
            $period .= ' -- '  . 'Today' ;
            $period_ar .= ' -- '  . 'حتى اليوم' ;
        }

        $company = CompanyInfo::all() -> first();
        $branch = Branch::find($request -> branch_id);
        return view('admin.ReportAccount.sales_collectible_report_result' , compact('bills','branch','grouped_ar' ,'grouped_en' , 'period' , 'period_ar' ,'company' ))  ;
    }

    public function purchase_report(){
        $pricings = Pricing::all(); 
        $branches = Branch::where('status',1)->get(); 
        return view('admin.ReportAccount.purchase_report', compact('branches'));
    }

    public function purchase_report_search(Request $request){
        $data = DB::table('purchase_details')
            -> join('purchases' , 'purchase_details.bill_id' , '=' , 'purchases.id')
            ->join('karats' , 'purchase_details.karat_id' , '=' , 'karats.id')
            ->select('purchases.bill_number' , 'purchases.id' ,'purchases.branch_id', 'purchases.date' , 'purchases.supplier_id as supplier_id'
                ,'karats.name_ar as karat_name_ar' , 'karats.name_en as karat_name_en' , 'purchase_details.weight' , 'purchase_details.made_money' ,
                'purchase_details.net_weight' , 'purchase_details.net_money' , 'purchase_details.karat_id' , 'purchase_details.weight21') ;

        $data2 = DB::table('enter_old_details')
            -> join('enter_olds' , 'enter_old_details.bill_id' , '=' , 'enter_olds.id')
            ->join('karats' , 'enter_old_details.karat_id' , '=' , 'karats.id')
            ->select('enter_olds.bill_number'  , 'enter_olds.id','enter_olds.branch_id', 'enter_olds.date' , 'enter_olds.supplier_id as supplier_id'
                ,'karats.name_ar as karat_name_ar' , 'karats.name_en as karat_name_en' , 'enter_old_details.weight' , 'enter_old_details.made_money' ,
                'enter_old_details.net_weight' , 'enter_old_details.net_money' , 'enter_old_details.karat_id' , 'enter_old_details.weight21')
            ->when(Auth::user()->subscriber_id ?? null,function($q,$sub){
                if (Schema::hasColumn('enter_old_details','subscriber_id')) {
                    $q->where('enter_old_details.subscriber_id',$sub);
                }
            });

        if($request -> branch_id > 0) $data = $data -> where('purchases.branch_id' , $request -> branch_id);        
        if($request -> has('isStartDate')) $data = $data -> where('purchases.date' , '>=' , Carbon::parse($request -> StartDate) );
        if($request -> has('isEndDate'))   $data = $data -> where('purchases.date' , '<=' , Carbon::parse($request -> EndDate) -> addDay());

        if($request -> branch_id > 0) $data2 = $data2 -> where('enter_olds.branch_id' , $request -> branch_id);     
        if($request -> has('isStartDate')) $data2 = $data2 -> where('enter_olds.date' , '>=' , Carbon::parse($request -> StartDate) );
        if($request -> has('isEndDate'))   $data2 = $data2 -> where('enter_olds.date' , '<=' , Carbon::parse($request -> EndDate) -> addDay());


        $bills = array();
        foreach ($data-> get() as $bill){
            $supplier = Company::find($bill -> supplier_id);
            if($supplier)
                $bill -> supplier =   $supplier -> name ;
            else
                $bill -> supplier = '';
            $bill -> type = 1 ;
            array_push($bills , $bill);
        }
        foreach ($data2 -> get() as $bill){
            $supplier = Company::find($bill -> supplier_id);
            if($supplier)
                $bill -> supplier =   $supplier -> name ;
            else
                $bill -> supplier = '';
            $bill -> type = 0 ;
            array_push($bills , $bill);
        }

        $all = $data -> get() -> merge($data2 -> get());

        $grouped_ar = $all   -> groupBy('karat_name_ar');
        $grouped_en = $all   -> groupBy('karat_name_en');

        $period = 'Period : ';
        $period_ar = 'الفترة  :';
        if($request -> has('isStartDate')){
            $startDate = $request->StartDate;

            $period .= $startDate ;
            $period_ar .= $startDate ;
        } else {
            $period .= 'Starting Date';
            $period_ar .= 'من البداية' ;
        }

        if($request -> has('isEndDate')){
            $endDate =  Carbon::parse($request->EndDate) -> addDay()  ;
            $period .= ' -- '  . $endDate -> format('d-m-Y') ;
            $period_ar .= ' -- '  . $endDate -> format('d-m-Y');
        } else {
            $period .= ' -- '  . 'Today' ;
            $period_ar .= ' -- '  . 'حتى اليوم' ;
        }

        $company = CompanyInfo::all() -> first();
        $branch = Branch::find($request -> branch_id);

        return view('admin.ReportAccount.purchase_report_result' 
            , compact('bills', 'branch','grouped_ar','grouped_en'
            , 'period', 'period_ar', 'company'));
    }
    

    public function purchase_collectible_report(){
        $pricings = Pricing::all(); 
        $branches = Branch::where('status',1)->get();

        return view('admin.ReportAccount.purchase_collectible_report', compact('branches'));
    }

    public function purchase_collectible_report_search(Request $request){
        $data = DB::table('purchase_collectible_details')
            -> join('purchases_collectibles' , 'purchase_collectible_details.bill_id' , '=' , 'purchases_collectibles.id') 
            ->join('items_collectibles' , 'purchase_collectible_details.item_id' , '=' , 'items_collectibles.id')
            ->select('purchases_collectibles.bill_number' , 'purchases_collectibles.id','purchases_collectibles.branch_id' , 'purchases_collectibles.date' , 'purchases_collectibles.supplier_id as supplier_id'
                , 'purchase_collectible_details.weight' , 'purchase_collectible_details.made_money' ,'items_collectibles.name_ar as item_ar','items_collectibles.name_en as item_en'
                ,'purchase_collectible_details.net_weight' , 'purchase_collectible_details.net_money' , 'purchase_collectible_details.karat_id');
 
        if($request ->branch_id > 0) $data = $data -> where('purchases_collectibles.branch_id' ,$request ->branch_id);        
        if($request -> has('isStartDate')) $data = $data -> where('purchases_collectibles.date' , '>=' , Carbon::parse($request -> StartDate) );
        if($request -> has('isEndDate'))   $data = $data -> where('purchases_collectibles.date' , '<=' , Carbon::parse($request -> EndDate) -> addDay());
 
        $bills = array();
        foreach ($data-> get() as $bill){
            $supplier = Company::find($bill -> supplier_id);
            if($supplier)
                $bill -> supplier =   $supplier -> name ;
            else
                $bill -> supplier = '';
            $bill -> type = 1 ;
            array_push($bills , $bill);
        }
        

        $all = $data -> get();
        $grouped_ar = $all   -> groupBy('karat_name_ar');
        $grouped_en = $all   -> groupBy('karat_name_en');

        $period = 'Period : ';
        $period_ar = 'الفترة  :';
        if($request -> has('isStartDate')){
            $startDate = $request->StartDate;
            $period .= $startDate ;
            $period_ar .= $startDate ;
        } else {
            $period .= 'Starting Date';
            $period_ar .= 'من البداية' ;
        }

        if($request -> has('isEndDate')){
            $endDate =  Carbon::parse($request->EndDate) -> addDay()  ;
            $period .= ' -- '  . $endDate -> format('d-m-Y') ;
            $period_ar .= ' -- '  . $endDate -> format('d-m-Y');
        } else {
            $period .= ' -- '  . 'Today' ;
            $period_ar .= ' -- '  . 'حتى اليوم' ;
        }

        $company = CompanyInfo::first();
        $branch = Branch::find($request -> branch_id);

        return view('admin.ReportAccount.purchase_collectible_report_result' , compact('bills', 'branch','grouped_ar' ,'grouped_en', 'period' , 'period_ar' , 'company'))  ;

    }

    public function vendor_account(){
        $vendors = Company::all(); 
        $branches = Branch::where('status',1)->get();
        return view('admin.ReportAccount.vendor_account' , compact('vendors','branches'));
    }

    public function vendor_account_search(Request $request){
        $client = Company::find($request -> vendor_id);
        $type = $client -> group_id ;
        $data = CompanyMovement::where('company_id' , '=' , $request -> vendor_id);

        if($request -> branch_id > 0) $data = $data -> where('branch_id' , $request -> branch_id);
        if($request -> has('isStartDate')) $data = $data -> where('date' , '>=' , Carbon::parse($request -> StartDate) );
        if($request -> has('isEndDate')) $data = $data -> where('date' , '<=' , Carbon::parse($request -> EndDate) -> addDay());

        $movements = $data -> get();
        $slag =  14;
        $subSlag = 145 ;
 
        $period = 'Period : ';
        $period_ar = 'الفترة  :';
        if($request -> has('isStartDate')){
            $startDate = $request->StartDate;
            $period .= $startDate ; 
            $period_ar .= ' - '  .Carbon::parse($startDate) -> format('d-m-Y');
        } else {
            $period .= 'Starting Date';
            $period_ar .= 'من البداية' ;
        }

        if($request -> has('isEndDate')){
            $endDate =  Carbon::parse($request->EndDate) -> addDay()  ;
            $period .= ' -- '  . $endDate -> format('d-m-Y') ;
            $period_ar .= ' - '  .Carbon::parse($endDate) -> addDay(-1)  -> format('d-m-Y');
        } else {
            $period .= ' -- '  . 'Today' ;
            $period_ar .= ' -- '  . 'حتى اليوم' ;
        }

        $company = CompanyInfo::first();
        $branch = Branch::find($request -> branch_id);

        return view('admin.company.accountMovement' , compact('type' , 'branch', 'movements', 'slag' , 'subSlag' ,'period' , 'period_ar', 'company','client'));
    }

    public function gold_stock_report(){ 
        $branches = Branch::where('status',1)->get(); 
        return view('admin.ReportAccount.gold_stock_report', compact('branches'));
    }

    public function gold_stock_search(Request  $request){
        $workWarehouses = Warehouse::where('type' , '=' , 1);
        $oldWarehouses = Warehouse::where('type' , '=' , 0) ;
        $pureWarehouses = Warehouse::where('type' , '=' , 2) ;

        if($request -> branch_id > 0 ) $workWarehouses = $workWarehouses -> where('branch_id' , $request -> branch_id);
        if($request -> has('isStartDate')) $workWarehouses = $workWarehouses -> where('date' , '>=' , Carbon::parse($request -> StartDate) );
        if($request -> has('isEndDate'))   $workWarehouses = $workWarehouses -> where('date' , '<=' , Carbon::parse($request -> EndDate) -> addDay());
        
        if($request -> branch_id > 0 ) $oldWarehouses = $oldWarehouses -> where('branch_id' , $request -> branch_id);
        if($request -> has('isStartDate')) $oldWarehouses = $oldWarehouses -> where('date' , '>=' , Carbon::parse($request -> StartDate) );
        if($request -> has('isEndDate'))   $oldWarehouses = $oldWarehouses -> where('date' , '<=' , Carbon::parse($request -> EndDate) -> addDay());
 
        if($request -> branch_id > 0 ) $pureWarehouses = $pureWarehouses -> where('branch_id' , $request -> branch_id);
        if($request -> has('isStartDate')) $pureWarehouses = $pureWarehouses -> where('date' , '>=' , Carbon::parse($request -> StartDate) );
        if($request -> has('isEndDate'))   $pureWarehouses = $pureWarehouses -> where('date' , '<=' , Carbon::parse($request -> EndDate) -> addDay());
 
        $karats = Karat::all();
        $work = $workWarehouses ->get() -> groupBy('karat_id') -> map(function ($item) {
            return [
                'enter_weight' => $item -> sum('enter_weight'),
                'out_weight'=> $item -> sum('out_weight'),
            ];
        });
        $old = $oldWarehouses ->get() -> groupBy('karat_id') -> map(function ($item) {
            return [
                'enter_weight' => $item -> sum('enter_weight'),
                'out_weight'=> $item -> sum('out_weight'),
            ];
        });
        $pure = $pureWarehouses ->get() -> groupBy('karat_id') -> map(function ($item) {
            return [
                'enter_weight' => $item -> sum('enter_weight'),
                'out_weight'=> $item -> sum('out_weight'),
            ];
        });

        $works = DB::table('sale_details')
            -> join('sales' , 'sale_details.sale_id' , '=' , 'sales.id')
            -> where('sales.total_money' , '<' , 0) 
            ->select('sale_details.*' , 'sales.date')
            ->when(Auth::user()->subscriber_id ?? null,function($q,$sub){
                if (Schema::hasColumn('sale_details','subscriber_id')) {
                    $q->where('sale_details.subscriber_id',$sub);
                }
            });

        $olds = DB::table('exit_old_details')
            -> join('exit_olds' , 'exit_old_details.bill_id' , '=' , 'exit_olds.id')
            -> where('exit_olds.total_money' , '<' , 0)
            -> where('exit_olds.bill_type' ,'=', 0)  
            ->select('exit_old_details.*' , 'exit_olds.date')
            ->when(Auth::user()->subscriber_id ?? null,function($q,$sub){
                if (Schema::hasColumn('exit_old_details','subscriber_id')) {
                    $q->where('exit_old_details.subscriber_id',$sub);
                }
            });

        $pures = DB::table('exit_old_details')
            -> join('exit_olds' , 'exit_old_details.bill_id' , '=' , 'exit_olds.id')
            -> where('exit_olds.total_money' , '<' , 0)
            -> where('exit_olds.bill_type' , 2)  
            ->select('exit_old_details.*' , 'exit_olds.date')
            ->when(Auth::user()->subscriber_id ?? null,function($q,$sub){
                if (Schema::hasColumn('exit_old_details','subscriber_id')) {
                    $q->where('exit_old_details.subscriber_id',$sub);
                }
            });

        if($request -> branch_id > 0) $works = $works-> where('sales.branch_id' ,$request -> branch_id);
        if($request -> branch_id > 0) $olds = $olds-> where('exit_olds.branch_id' ,$request -> branch_id);
        if($request -> branch_id > 0) $pures = $pures-> where('exit_olds.branch_id' ,$request -> branch_id);

        $workR = $works ->get() -> groupBy('karat_id') -> map(function ($item) {
            return [
                'RWeight' => $item -> sum('weight'),
            ];
        });

        $oldR = $olds ->get() -> groupBy('karat_id') -> map(function ($item) {
            return [
                'RWeight' => $item -> sum('weight'),
            ];
        });

        $pureR = $pures ->get() -> groupBy('karat_id') -> map(function ($item) {
            return [
                'RWeight' => $item -> sum('weight'),
            ];
        });

        $slag =  14;
        $subSlag = 146 ;
        $period = 'Period : ';
        $period_ar = 'الفترة  :';
        if($request -> has('isStartDate')){
            $startDate = $request->StartDate;
            $period .= $startDate ;
            $period_ar .= Carbon::parse($startDate) -> format('d-m-Y') ;
        } else {
            $period .= 'Starting Date';
            $period_ar .= 'من البداية' ;
        }

        if($request -> has('isEndDate')){
            $endDate =  Carbon::parse($request->EndDate) -> addDay()  ;
            $period .= ' -- '  . $endDate -> format('d-m-Y') ;
            $period_ar .= ' - '  .Carbon::parse($endDate) -> addDay(-1)  -> format('d-m-Y');
        } else {
            $period .= ' -- '  . 'Today' ;
            $period_ar .= ' -- '  . 'حتى اليوم' ;
        }

        $company = CompanyInfo::all() -> first();
        $branch = Branch::find($request -> branch_id);

        return view('admin.Item.gold_stock' , compact('work' , 'branch', 'old' , 'pure', 'karats' , 'slag' , 'subSlag' ,
            'period' , 'period_ar' , 'company'  , 'workR' , 'oldR','pureR')) ;
    
        }

    public function daily_all_movements(){
        $branches = Branch::where('status',1)->get(); 
        return view('admin.ReportAccount.daily_all_movements' , compact('branches'));
    }

    public function daily_all_movements_search(Request $request){

        $workWarehouses = Warehouse::where('type' , '=' , 1);
        $oldWarehouses = Warehouse::where('type' , '<>' , 1) ; 

        if($request -> branch_id > 0) $workWarehouses = $workWarehouses -> where('branch_id' ,$request -> branch_id);
        if($request -> has('isStartDate')) $workWarehouses = $workWarehouses -> where('date' , '>=' , Carbon::parse($request -> StartDate) );
        if($request -> has('isEndDate'))   $workWarehouses = $workWarehouses -> where('date' , '<=' , Carbon::parse($request -> EndDate) -> addDay());

        if($request -> branch_id > 0) $oldWarehouses = $oldWarehouses -> where('branch_id' ,$request -> branch_id);
        if($request -> has('isStartDate')) $oldWarehouses = $oldWarehouses -> where('date' , '>=' , Carbon::parse($request -> StartDate) );
        if($request -> has('isEndDate'))   $oldWarehouses = $oldWarehouses -> where('date' , '<=' , Carbon::parse($request -> EndDate) -> addDay());

        $work = $workWarehouses ->get() -> groupBy('karat_id') -> map(function ($item) {
            return [
                'enter_weight' => $item -> sum('enter_weight'),
                'out_weight'=> $item -> sum('out_weight'),
            ];
        });

        $old = $oldWarehouses ->get() -> groupBy('karat_id') -> map(function ($item) {
            return [
                'enter_weight' => $item -> sum('enter_weight'),
                'out_weight'=> $item -> sum('out_weight'),
            ];
        });

        $enterMoney = EnterMoney::all();
        $exitMoney = ExitMoney::all();

        if($request -> branch_id > 0) $enterMoney = $enterMoney -> where('branch_id' , $request -> branch_id);
        if($request -> has('isStartDate')) $enterMoney = $enterMoney -> where('date' , '>=' , Carbon::parse($request -> StartDate) );
        if($request -> has('isEndDate'))   $enterMoney = $enterMoney -> where('date' , '<=' , Carbon::parse($request -> EndDate) -> addDay());
        
        if($request -> branch_id > 0) $exitMoney = $exitMoney -> where('branch_id' , $request -> branch_id);
        if($request -> has('isStartDate')) $exitMoney = $exitMoney -> where('date' , '>=' , Carbon::parse($request -> StartDate) );
        if($request -> has('isEndDate'))   $exitMoney = $exitMoney -> where('date' , '<=' , Carbon::parse($request -> EndDate) -> addDay());
  

        $period = 'Period : ';
        $period_ar = 'الفترة  :';
        if($request -> has('isStartDate')){
            $startDate = $request->StartDate; 
            $period .= $startDate ;
            $period_ar .= $startDate ;
        } else {
            $period .= 'Starting Date';
            $period_ar .= 'من البداية' ;
        }

        if($request -> has('isEndDate')){
            $endDate =  Carbon::parse($request->EndDate) -> addDay()  ; 
            $period .= ' -- '  . $endDate -> format('d-m-Y') ;
            $period_ar .= ' -- '  . $endDate -> format('d-m-Y');
        } else {
            $period .= ' -- '  . 'Today' ;
            $period_ar .= ' -- '  . 'حتى اليوم' ;
        }

        $company = CompanyInfo::all() -> first();
        $karats = Karat::all();
        $branch = Branch::find($request -> branch_id);

        return view('admin.ReportAccount.daily_all_movements_result' , compact('karats' , 'branch','work', 'old' , 'enterMoney' ,
            'exitMoney' , 'period' , 'period_ar' , 'company'));
    }

    public function account_balance_search(Request $request){
        $startDate = Carbon::now()->addYears(-5);
        $endDate = Carbon::now() -> addDays(1);
        $period = 'Period : ';
        $period_ar = 'الفترة  :';
        
        if($request -> has('isStartDate')){
            $startDate = $request->StartDate;
            $period .= $startDate ;
            $period_ar .= $startDate ;
        } else {
            $period .= 'Starting Date';
            $period_ar .= 'من البداية' ;
        }

        if($request -> has('isEndDate')){
            $endDate =  Carbon::parse($request->EndDate) -> addDay()  ;
            $period .= ' -- '  . $endDate -> format('d-m-Y') ;
            $period_ar .= ' -- '  . $endDate -> format('d-m-Y');
        } else {
            $period .= ' -- '  . 'Today' ;
            $period_ar .= ' -- '  . 'حتى اليوم' ;
        }

        $branchId = (int) ($request->branch_id ?? 0);
        $costCenterId = (int) ($request->cost_center_id ?? 0);

        $accounts = DB::table('accounts_trees')
            ->join('account_movements','accounts_trees.id','=','account_movements.account_id')
            ->join('journals','journals.id','=','account_movements.journal_id')
            ->select('accounts_trees.code','accounts_trees.name',
                DB::raw('sum(account_movements.credit) as credit'),
                DB::raw('sum(account_movements.debit) as debit'))
            ->groupBy('accounts_trees.id','accounts_trees.code','accounts_trees.name')
            ->where('account_movements.date','>=',$startDate)
            ->where('account_movements.date','<=',$endDate)
            ->when($branchId > 0, function ($q) use ($branchId) {
                $q->where('journals.branch_id', $branchId);
            })
            ->when($costCenterId > 0, function ($q) use ($costCenterId) {
                $q->where('journals.cost_center_id', $costCenterId);
            })
            ->when(Auth::user()->subscriber_id ?? null,function($q,$sub){
                $q->where('accounts_trees.subscriber_id',$sub);
            })
            ->get();

        foreach ($accounts as $account){
            $accountBalance = DB::table('accounts_trees')
                ->join('account_movements','accounts_trees.id','=','account_movements.account_id')
                ->join('journals','journals.id','=','account_movements.journal_id')
                ->select('accounts_trees.code','accounts_trees.name',
                    DB::raw('SUM(account_movements.credit) credit'),
                    DB::raw('SUM(account_movements.debit) debit'))
                ->groupBy('accounts_trees.id','accounts_trees.code','accounts_trees.name')
                ->where('account_movements.date','<',$startDate)
                ->where('accounts_trees.code','<',$account->code)
                ->when($branchId > 0, function ($q) use ($branchId) {
                    $q->where('journals.branch_id', $branchId);
                })
                ->when($costCenterId > 0, function ($q) use ($costCenterId) {
                    $q->where('journals.cost_center_id', $costCenterId);
                })
                ->when(Auth::user()->subscriber_id ?? null,function($q,$sub){
                    $q->where('accounts_trees.subscriber_id',$sub);
                })
                ->first();

            if($accountBalance){
                $account->before_credit = $accountBalance->credit;
                $account->before_debit = $accountBalance->debit;
            } else {
                $account->before_credit = 0;
                $account->before_debit = 0;
            }
        }

        $company = CompanyInfo::all() -> first();
        return view('admin.ReportAccount.account_balance_report',compact('accounts' , 'period' , 'period_ar' , 'company'));
    }

    public function account_balance(){
        $subscriberId = Auth::user()->subscriber_id ?? null;
        $branches = Branch::where('status',1)
            ->when($subscriberId, fn($q) => $q->where('subscriber_id', $subscriberId))
            ->get();
        $costCenters = CostCenter::query()
            ->when($subscriberId, fn($q) => $q->where('subscriber_id', $subscriberId))
            ->where('is_active', 1)
            ->orderBy('name')
            ->get();
        return view('admin.ReportAccount.account_balance', compact('branches', 'costCenters'));
    }

    public function box_movement_report(){ 
        $branches = Branch::where('status',1)
            ->when(Auth::user()->subscriber_id ?? null,function($q,$sub){
                $q->where('subscriber_id',$sub);
            })
            ->get(); 
        return view('admin.ReportAccount.box_movement_report', compact('branches') );
    }

    public function box_movement_report_search(Request $request){

        $startDate = Carbon::now()->addYears(-5);
        $endDate = Carbon::now() -> addDays(1);

        if($request -> has('isStartDate')){
            $startDate = $request->StartDate;
        }

        if($request -> has('isEndDate')){
            $endDate =  Carbon::parse($request->EndDate) -> addDay()  ;
        }

        $enterMoney = EnterMoney::where('payment_method' , '=' , 0)
            ->where('date','>=',$startDate)
            ->where('date','<=',$endDate) 
            -> get(); 

        $exitMoney = ExitMoney::where('payment_method' , '=' , 0)
            ->where('date','>=',$startDate)
            ->where('date','<=',$endDate) 
            -> get();

        $catchs = DB::table('catch_recipts')
            -> select('catch_recipts.*' )
            ->where('date','>=',$startDate)
            ->where('date','<=',$endDate)
            -> get();

        $expenses = DB::table('expenses')
            -> select('expenses.*' )
            ->where('date','>=',$startDate)
            ->where('date','<=',$endDate)
            -> get();

        if($request->branch_id > 0 ) $enterMoney = $enterMoney->where('branch_id',$request->branch_id);
        if($request->branch_id > 0 ) $exitMoney = $exitMoney->where('branch_id',$request->branch_id);
        if($request->branch_id > 0 ) $catchs = $catchs->where('branch_id',$request->branch_id);
        if($request->branch_id > 0 ) $expenses = $expenses->where('branch_id',$request->branch_id);

        $holders = [];
        foreach ($enterMoney as $em){
            $holder = new Holder();
            $holder -> id = $em -> id ;
            $holder -> docNumber =  $em -> based_on_bill_number ? $em -> based_on_bill_number  : $em -> doc_number  ;
            $holder -> date = $em -> date  ;
            $holder -> docType =  $em -> based_on_bill_number ? (str_starts_with($em -> based_on_bill_number , 'SWSI') ? 'فاتور بيع ذهب مشغول' : 'فاتورة بيع ذهب كسر')  : 'مستند دخول نقدية' ;
            $holder -> debit = $em -> amount ;
            $holder -> credit = 0; 
            array_push($holders , $holder);
        }

        foreach ($exitMoney as $em){
            $holder = new Holder();
            $holder -> id = $em -> id ;
            $holder -> docNumber =  $em -> based_on_bill_number ? $em -> based_on_bill_number  : $em -> doc_number  ;
            $holder -> date = $em -> date  ;
            $holder -> docType =  ($em -> based_on_bill_number ? (str_starts_with($em -> based_on_bill_number , 'WEO') ? 'فاتور شراء ذهب  كسر/صافي' : '')  : 'مستند خروج نقدية' );
            $holder -> debit = 0 ;
            $holder -> credit = $em -> amount ;
            array_push($holders , $holder);
        }

        foreach ($catchs as $em){
            $holder = new Holder();
            $holder -> id = $em -> id ;
            $holder -> docNumber =  $em -> docNumber ;
            $holder -> date = $em -> date  ;
            $holder -> docType =  'مستند قبض حر'  ;
            $holder -> debit = $em -> amount ;
            $holder -> credit =  0;
            array_push($holders , $holder);
        }
        foreach ($expenses as $em){
            $holder = new Holder();
            $holder -> id = $em -> id ;
            $holder -> docNumber =  $em -> docNumber ;
            $holder -> date = $em -> date  ;
            $holder -> docType =  'مستند صرف حر'  ;
            $holder -> debit = 0 ;
            $holder -> credit =  $em -> amount;
            array_push($holders , $holder);
        }

        $period = 'Period : ';
        $period_ar = 'الفترة  :';
        if($request -> has('isStartDate')){
            $startDate = $request->StartDate;
            $period .= $startDate ;
            $period_ar .= $startDate ;
        } else {
            $period .= 'Starting Date';
            $period_ar .= 'من البداية' ;
        }

        if($request -> has('isEndDate')){
            $endDate =  Carbon::parse($request->EndDate) -> addDay()  ;
            $period .= ' -- '  . $endDate -> format('d-m-Y') ;
            $period_ar .= ' -- '  . $endDate -> format('d-m-Y');
        } else {
            $period .= ' -- '  . 'Today' ;
            $period_ar .= ' -- '  . 'حتى اليوم' ;
        }

        $company = CompanyInfo::all() -> first();
        $branch = Branch::find($request -> branch_id);

        return view('admin.ReportAccount.box_movement_report_result' , compact('holders', 'branch','period', 'period_ar' , 'company'));
    }

    public function bank_movement_report(){ 
        $branches = Branch::where('status',1)->get(); 
        return view('admin.ReportAccount.bank_movement_report', compact('branches'));
    }

    public function bank_movement_report_search(Request $request){
        $startDate = Carbon::now()->addYears(-5);
        $endDate = Carbon::now() -> addDays(1);

        if($request -> has('isStartDate')){
            $startDate = $request->StartDate;
        }

        if($request -> has('isEndDate')){
            $endDate =  Carbon::parse($request->EndDate) -> addDay()  ;
        }

        $enterMoney = EnterMoney::where('payment_method' , '=' , 1)
            ->where('date','>=',$startDate)
            ->where('date','<=',$endDate)
            -> get();

        $exitMoney = ExitMoney::where('payment_method' , '=' , 1)
            ->where('date','>=',$startDate)
            ->where('date','<=',$endDate)
            -> get();

        if($request->branch_id > 0 ) $enterMoney = $enterMoney->where('branch_id',$request->branch_id);
        if($request->branch_id > 0 ) $exitMoney = $exitMoney->where('branch_id',$request->branch_id);
        
        $holders = [];
        foreach ($enterMoney as $em){
            $holder = new Holder();
            $holder -> id = $em -> id ;
            $holder -> docNumber =  $em -> based_on_bill_number ? $em -> based_on_bill_number  : $em -> doc_number  ;
            $holder -> date = $em -> date  ;
            $holder -> docType =  $em -> based_on_bill_number ? (str_starts_with($em -> based_on_bill_number , 'SWSI') ? 'فاتور بيع ذهب مشغول' : 'فاتورة بيع ذهب كسر')  : 'مستند دخول نقدية' ;
            $holder -> debit = $em -> amount ;
            $holder -> credit = 0 ;
            array_push($holders , $holder);
        }

        foreach ($exitMoney as $em){
            $holder = new Holder();
            $holder -> id = $em -> id ;
            $holder -> docNumber =  $em -> based_on_bill_number ? $em -> based_on_bill_number  : $em -> doc_number  ;
            $holder -> date = $em -> date  ;
            $holder -> docType =  $em -> based_on_bill_number ? (str_starts_with($em -> based_on_bill_number , 'WEO') ? 'فاتور شراء ذهب كسر/صافي' : '')  : 'مستند خروج نقدية' ;
            $holder -> debit = 0 ;
            $holder -> credit = $em -> amount ;
            array_push($holders , $holder);
        }

        $period = 'Period : ';
        $period_ar = 'الفترة  :';
        if($request -> has('isStartDate')){
            $startDate = $request->StartDate; 
            $period .= $startDate ;
            $period_ar .= $startDate ;
        } else {
            $period .= 'Starting Date';
            $period_ar .= 'من البداية' ; 
        }

        if($request -> has('isEndDate')){
            $endDate =  Carbon::parse($request->EndDate) -> addDay()  ; 
            $period .= ' -- '  . $endDate -> format('d-m-Y') ;
            $period_ar .= ' -- '  . $endDate -> format('d-m-Y');
        } else {
            $period .= ' -- '  . 'Today' ;
            $period_ar .= ' -- '  . 'حتى اليوم' ;
        }

        $company = CompanyInfo::all() -> first();
        $branch = Branch::find($request -> branch_id);

        return view('admin.ReportAccount.bank_movement_report_result' , compact('holders' , 'branch','period', 'period_ar' , 'company'));
    }

    public function sales_total_report(){ 
        $branches = Branch::where('status',1)->get(); 
        return view('admin.ReportAccount.sales_total_report', compact('branches'));
    }

    public function sales_total_report_search(Request $request){

        $data = ExitWork::where('net_money' , '>' , 0);
        $data2 = ExitOld::where('net_money' ,'>' , 0);
        $data3 = ExitWorkTax::where('net_money' ,'>' , 0);
        $data4 = ExitOldTax::where('net_money' ,'>' , 0);    

        if($request -> branch_id > 0) $data = $data -> where('branch_id' , $request -> branch_id);
        if($request -> has('isStartDate')) $data = $data -> where('date' , '>=' , Carbon::parse($request -> StartDate) );
        if($request -> has('isEndDate'))   $data = $data -> where('date' , '<=' , Carbon::parse($request -> EndDate) -> addDay());
        if($request -> billNumber) $data = $data -> where('bill_number' , '=' ,$request -> billNumber );
        if($request -> netMoney) $data = $data -> where('net_money' , '=' ,$request -> netMoney );

        if($request -> branch_id > 0) $data2 = $data2 -> where('branch_id' , $request -> branch_id);
        if($request -> has('isStartDate')) $data2 = $data2 -> where('date' , '>=' , Carbon::parse($request -> StartDate) );
        if($request -> has('isEndDate'))   $data2 = $data2 -> where('date' , '<=' , Carbon::parse($request -> EndDate) -> addDay());
        if($request -> billNumber) $data2 = $data2 -> where('bill_number' , '=' ,$request -> billNumber );
        if($request -> netMoney) $data2 = $data2 -> where('net_money' , '=' ,$request -> netMoney );

        if($request -> branch_id > 0) $data3 = $data3 -> where('branch_id' , $request -> branch_id);
        if($request -> has('isStartDate')) $data3 = $data3 -> where('date' , '>=' , Carbon::parse($request -> StartDate) );
        if($request -> has('isEndDate'))   $data3 = $data3 -> where('date' , '<=' , Carbon::parse($request -> EndDate) -> addDay());
        if($request -> billNumber) $data3 = $data3 -> where('bill_number' , '=' ,$request -> billNumber );
        if($request -> netMoney) $data3 = $data3 -> where('net_money' , '=' ,$request -> netMoney );

        if($request -> branch_id > 0) $data4 = $data4 -> where('branch_id' , $request -> branch_id);
        if($request -> has('isStartDate')) $data4 = $data4 -> where('date' , '>=' , Carbon::parse($request -> StartDate) );
        if($request -> has('isEndDate'))   $data4 = $data4 -> where('date' , '<=' , Carbon::parse($request -> EndDate) -> addDay());
        if($request -> billNumber) $data4 = $data4 -> where('bill_number' , '=' ,$request -> billNumber );
        if($request -> netMoney) $data4 = $data4 -> where('net_money' , '=' ,$request -> netMoney );


        $bills = array();
        $data22 =[] ;
        foreach ($data-> get() as $bill){
            $client = Company::find($bill -> client_id);
            if($client)
                $bill -> client = $client -> name;
            else
                $bill -> client = '';
            $bill -> type = 1 ;
            array_push($bills , $bill);
        }
 
        foreach (is_array($data2) ? $data2   : $data2 -> get() as $bill){
            $client = Company::find($bill -> supplier_id);
            if($client)
                $bill -> client = $client -> name;
            else
                $bill -> client = '';

            $bill -> type = 0 ;
            $bill -> item_name_ar  = '--';
            $bill -> item_name_en  = '--';
            array_push($data22 , $bill);
        }

        $bills2 = array();
        $data44 =[] ;
        foreach ($data3-> get() as $bill){
            $client = Company::find($bill -> client_id);
            if($client)
                $bill -> client = $client -> name;
            else
                $bill -> client = '';
            $bill -> type = 1 ;
            array_push($bills2 , $bill);
        }
 
        foreach (is_array($data4) ? $data4   : $data4 -> get() as $bill){
            $client = Company::find($bill -> supplier_id);
            if($client)
                $bill -> client = $client -> name;
            else
                $bill -> client = '';

            $bill -> type = 0 ;
            $bill -> item_name_ar  = '--';
            $bill -> item_name_en  = '--';
 
            array_push($data44 , $bill);
        }        

        $all1 =  collect($bills)  -> merge($data22);
        $all2 =  collect($bills2)  -> merge($data44);

        $all = $all1 -> merge($all2); 

        $period = 'Period : ';
        $period_ar = 'الفترة  :';
        if($request -> has('isStartDate')){
            $startDate = $request->StartDate;
            $period .= $startDate ;
            $period_ar .= $startDate ;
        } else {
            $period .= 'Starting Date';
            $period_ar .= 'من البداية' ;
        }

        if($request -> has('isEndDate')){
            $endDate =  Carbon::parse($request->EndDate) -> addDay()  ;
            $period .= ' -- '  . $endDate -> format('d-m-Y') ;
            $period_ar .= ' -- '  . $endDate -> format('d-m-Y');
        } else {
            $period .= ' -- '  . 'Today' ;
            $period_ar .= ' -- '  . 'حتى اليوم' ;
        }

        $company = CompanyInfo::all() -> first();
        $branch = Branch::find($request -> branch_id);

        return view('admin.ReportAccount.sales_total_report_result' , compact('all' , 'branch','period', 'period_ar' , 'company'));
    }
    
    public function sales_collectible_total_report(){ 
        $branches = Branch::where('status',1)->get();
        return view('admin.ReportAccount.sales_collectible_total_report', compact('branches'));

    }

    public function sales_collectible_total_report_search(Request $request){

        $data = SaleCollectible::where('net_money' , '>' , 0); 

        if($request -> has('isStartDate')) $data = $data -> where('date' , '>=' , Carbon::parse($request -> StartDate) );
        if($request -> has('isEndDate'))   $data = $data -> where('date' , '<=' , Carbon::parse($request -> EndDate) -> addDay());
        if($request -> branch_id > 0) $data = $data -> where('branch_id' ,$request -> branch_id);
        if($request -> billNumber) $data = $data -> where('bill_number' , '=' ,$request -> billNumber );
        if($request -> netMoney) $data = $data -> where('net_money' , '=' ,$request -> netMoney );
 
        $bills = array(); 
        foreach ($data-> get() as $bill){
            $client = Company::find($bill -> client_id);
            if($client)
                $bill -> client = $client -> name;
            else
                $bill -> client = '';
            $bill -> type = 1 ;
            array_push($bills , $bill);
        } 

        $all =  $bills;

        $period = 'Period : ';
        $period_ar = 'الفترة  :';
        if($request -> has('isStartDate')){
            $startDate = $request->StartDate;
            $period .= $startDate ;
            $period_ar .= $startDate ;
        } else {
            $period .= 'Starting Date';
            $period_ar .= 'من البداية' ;
        }

        if($request -> has('isEndDate')){
            $endDate =  Carbon::parse($request->EndDate) -> addDay()  ;
            $period .= ' -- '  . $endDate -> format('d-m-Y') ;
            $period_ar .= ' -- '  . $endDate -> format('d-m-Y');
        } else {
            $period .= ' -- '  . 'Today' ;
            $period_ar .= ' -- '  . 'حتى اليوم' ;
        }

        $company = CompanyInfo::all() -> first();
        $branch = Branch::find($request -> branch_id);

        return view('admin.ReportAccount.sales_collectible_total_report_result' , compact('all' , 'branch','period', 'period_ar' , 'company'));
    }

    public function purchase_total_report(){ 
        $branches = Branch::where('status',1)->get(); 
        return view('admin.ReportAccount.purchase_total_report', compact('branches'));
    }

    public function purchase_total_report_search(Request $request){
    
        $data = EnterWork::where('net_money' , '>' , 0); 
        $data2 = EnterOld::where('net_money' ,'>' , 0);
        
        if($request -> branch_id > 0) $data = $data -> where('branch_id' ,$request -> branch_id );
        if($request -> has('isStartDate')) $data = $data -> where('date' , '>=' , Carbon::parse($request -> StartDate) );
        if($request -> has('isEndDate'))   $data = $data -> where('date' , '<=' , Carbon::parse($request -> EndDate) -> addDay());
        if($request -> billNumber) $data = $data -> where('bill_number' , '=' ,$request -> billNumber );
        if($request -> netMoney) $data = $data -> where('net_money' , '=' ,$request -> netMoney );

        if($request -> branch_id > 0) $data2 = $data2 -> where('branch_id' ,$request -> branch_id );
        if($request -> has('isStartDate')) $data2 = $data2 -> where('date' , '>=' , Carbon::parse($request -> StartDate) );
        if($request -> has('isEndDate')) $data2 = $data2 -> where('date' , '<=' , Carbon::parse($request -> EndDate) -> addDay());
        if($request -> billNumber) $data2 = $data2 -> where('bill_number' , '=' ,$request -> billNumber );
        if($request -> netMoney) $data2 = $data2 -> where('net_money' , '=' ,$request -> netMoney );

        $bills = array();
        $data22 =[] ;

        foreach ($data-> get() as $bill){
            $supplier = Company::find($bill -> supplier_id);
            if($supplier)
                $bill -> supplier =   $supplier -> name ;
            else
                $bill -> supplier = '';
            $bill -> type = 1 ;
            array_push($bills , $bill);
        }
        foreach ($data2 -> get() as $bill){
            $supplier = Company::find($bill -> supplier_id);
            if($supplier)
                $bill -> supplier =   $supplier -> name ;
            else
                $bill -> supplier = '';
            $bill -> type = 0 ;
            array_push($bills , $bill);
        }

        $all =  collect($bills)  -> merge($data22);
        $period = 'Period : ';
        $period_ar = 'الفترة  :';
        if($request -> has('isStartDate')){
            $startDate = $request->StartDate;
            $period .= $startDate ;
            $period_ar .= $startDate ;
        } else {
            $period .= 'Starting Date';
            $period_ar .= 'من البداية' ;
        }

        if($request -> has('isEndDate')){
            $endDate =  Carbon::parse($request->EndDate) -> addDay(); 
            $period .= ' -- '  . $endDate -> format('d-m-Y') ;
            $period_ar .= ' -- '  . $endDate -> format('d-m-Y');
        } else {
            $period .= ' -- '  . 'Today' ;
            $period_ar .= ' -- '  . 'حتى اليوم' ;
        }

        $company = CompanyInfo::all() -> first();
        $branch = Branch::find($request -> branch_id);

        return view('admin.ReportAccount.purchase_total_report_result' , compact('all', 'branch', 'period', 'period_ar' , 'company'));
    }


    public function purchase_collectible_total_report(){ 
        $branches = Branch::where('status',1)->get(); 
        return view('admin.ReportAccount.purchase_collectible_total_report', compact('branches'));
    }
    
    public function purchase_collectible_total_report_search(Request $request){
    
        $data = PurchasesCollectible::where('net_money' , '>' , 0); 

        if($request -> branch_id > 0) $data = $data -> where('branch_id' ,$request -> branch_id );
        if($request -> has('isStartDate')) $data = $data -> where('date' , '>=' , Carbon::parse($request -> StartDate) );
        if($request -> has('isEndDate'))   $data = $data -> where('date' , '<=' , Carbon::parse($request -> EndDate) -> addDay());
        if($request -> billNumber) $data = $data -> where('bill_number' , '=' ,$request -> billNumber );
        if($request -> netMoney) $data = $data -> where('net_money' , '=' ,$request -> netMoney );
 
        $bills = array(); 

        foreach ($data-> get() as $bill){
            $supplier = Company::find($bill -> supplier_id);
            if($supplier)
                $bill -> supplier =   $supplier -> name ;
            else
                $bill -> supplier = '';
            $bill -> type = 1 ;
            array_push($bills , $bill);
        }
    
        $all =   $bills ;
        $period = 'Period : ';
        $period_ar = 'الفترة  :';
        if($request -> has('isStartDate')){
            $startDate = $request->StartDate;
            $period .= $startDate ;
            $period_ar .= $startDate ;
        } else {
            $period .= 'Starting Date';
            $period_ar .= 'من البداية' ;
        }

        if($request -> has('isEndDate')){
            $endDate =  Carbon::parse($request->EndDate) -> addDay()  ;

            $period .= ' -- '  . $endDate -> format('d-m-Y') ;
            $period_ar .= ' -- '  . $endDate -> format('d-m-Y');
        } else {
            $period .= ' -- '  . 'Today' ;
            $period_ar .= ' -- '  . 'حتى اليوم' ;
        }

        $company = CompanyInfo::all() -> first();
        $branch = Branch::find($request -> branch_id);

        return view('admin.ReportAccount.purchase_collectible_total_report_result' , compact('all' ,'branch', 'period' , 'period_ar' , 'company'));

    }

    public function purchase_sales_total_report(){
        return view('admin.ReportAccount.purchase_sales_total_report');
    }
    
    public function movement_report(){ 
        return view('admin.ReportAccount.movement_report');
    }

    public function movement_report_search(Request $request){ 

        $period = 'Period : ';
        $period_ar = 'الفترة  :';
        if($request -> has('isStartDate')){
            $startDate = $request->StartDate;

            $period .= $startDate ;
            $period_ar .= $startDate ;
        } else {
            $period .= 'Starting Date';
            $period_ar .= 'من البداية' ;

        }

        if($request -> has('isEndDate')){
            $endDate =  Carbon::parse($request->EndDate) -> addDay()  ;
            $period .= ' -- '  . $endDate -> format('d-m-Y') ;
            $period_ar .= ' -- '  . $endDate -> format('d-m-Y');
        } else {
            $period .= ' -- '  . 'Today' ;
            $period_ar .= ' -- '  . 'حتى اليوم' ;
        }

        $company = CompanyInfo::all() -> first();
        $karats = Karat::all();

        $Warehouses = Warehouse::where('type' , '<>' , 2);
        if($request -> has('isStartDate')) $Warehouses = $Warehouses -> where('date' , '>=' , Carbon::parse($request -> StartDate) );
        if($request -> has('isEndDate'))   $Warehouses = $Warehouses -> where('date' , '<=' , Carbon::parse($request -> EndDate) -> addDay());
        $ware = $Warehouses ->get() -> groupBy('karat_id') -> map(function ($item) {
            return [
                'enter_weight' => $item -> sum('enter_weight'),
                'out_weight'=> $item -> sum('out_weight'),
            ];
        });
        $data = collect($ware);

        $returnW = DB::table('sale_details')
            -> join('sales' , 'sale_details.sale_id' , '=' , 'sales.id')
            -> select('sale_details.*' , 'sales.date' )
            ->where('sales.returned_bill_id' , '>'  , 0);
        $returnO = DB::table('exit_old_details')
            -> join('exit_olds' , 'exit_old_details.bill_id' , '=' , 'exit_olds.id')
            -> select('exit_old_details.*' , 'exit_olds.date' )
            ->where('exit_olds.returned_bill_id' , '>'  , 0)
            ->when(Auth::user()->subscriber_id ?? null,function($q,$sub){
                if (Schema::hasColumn('exit_old_details','subscriber_id')) {
                    $q->where('exit_old_details.subscriber_id',$sub);
                }
            }) ;

        if($request -> has('isStartDate')) $returnW = $returnW -> where('date' , '>=' , Carbon::parse($request -> StartDate) );
        if($request -> has('isEndDate'))   $returnW = $returnW -> where('date' , '<=' , Carbon::parse($request -> EndDate) -> addDay());
        if($request -> has('isStartDate')) $returnO = $returnO -> where('date' , '>=' , Carbon::parse($request -> StartDate) );
        if($request -> has('isEndDate'))   $returnO = $returnO -> where('date' , '<=' , Carbon::parse($request -> EndDate) -> addDay());
        
        $reW = $returnW ->get() -> groupBy('karat_id') -> map(function ($item) {
            return [
                'weight' => $item -> sum('weight'),
            ];
        });

        $reO = $returnO ->get() -> groupBy('karat_id') -> map(function ($item) {
            return [
                'weight' => $item -> sum('weight'),
            ];
        });

        $salesW = DB::table('sales')
            ->where('sales.returned_bill_id' , '='  , 0)
            -> sum('sales.total_money');

        $salesO = DB::table('exit_olds')
            ->where('exit_olds.returned_bill_id' , '='  , 0)
            ->when(Auth::user()->subscriber_id ?? null,function($q,$sub){
                if (Schema::hasColumn('exit_olds','subscriber_id')) {
                    $q->where('exit_olds.subscriber_id',$sub);
                }
            })
            -> sum('exit_olds.total_money');

        $returnW = DB::table('sales')
            ->where('sales.returned_bill_id' , '<>'  , 0)
            ->when(Auth::user()->subscriber_id ?? null,function($q,$sub){
                if (Schema::hasColumn('sales','subscriber_id')) {
                    $q->where('sales.subscriber_id',$sub);
                }
            })
            -> sum('sales.total_money');

        $returnO = DB::table('exit_olds')
            ->where('exit_olds.returned_bill_id' , '<>'  , 0)
            ->when(Auth::user()->subscriber_id ?? null,function($q,$sub){
                if (Schema::hasColumn('exit_olds','subscriber_id')) {
                    $q->where('exit_olds.subscriber_id',$sub);
                }
            })
            -> sum('exit_olds.total_money');

        $purchaseW = DB::table('purchases')
            ->when(Auth::user()->subscriber_id ?? null,function($q,$sub){
                if (Schema::hasColumn('purchases','subscriber_id')) {
                    $q->where('purchases.subscriber_id',$sub);
                }
            })
            -> sum('purchases.total_money');

        $purchaseO = DB::table('enter_olds')
            ->when(Auth::user()->subscriber_id ?? null,function($q,$sub){
                if (Schema::hasColumn('enter_olds','subscriber_id')) {
                    $q->where('enter_olds.subscriber_id',$sub);
                }
            })
            -> sum('enter_olds.total_money');

        $salesWorkVAl = DB::table('sale_details')
            ->join('sales' , 'sale_details.sale_id' , '=' , 'sales.id')
            -> join('items' , 'sale_details.item_id' , '=' ,'items.id')
            ->where('sales.returned_bill_id' , '='  , 0)
            -> select(DB::raw('sum(items.made_Value * items.weight) as total'))->get() -> first();

        $returnWorkVAl = DB::table('sale_details')
            ->join('sales' , 'sale_details.sale_id' , '=' , 'sales.id')
            -> join('items' , 'sale_details.item_id' , '=' ,'items.id')
            ->where('sales.returned_bill_id' , '<>'  , 0)
            -> select(DB::raw('sum(items.made_Value * items.weight) as total'))->get() -> first();

        $expenses = DB::table('expenses')
            ->join('accounts_trees' , 'expenses.to_account' , '=' , 'accounts_trees.id')
            ->select('expenses.*' , 'accounts_trees.name as account_name');

        if($request -> has('isStartDate')) $expenses = $expenses -> where('date' , '>=' , Carbon::parse($request -> StartDate) );
        if($request -> has('isEndDate'))   $expenses = $expenses -> where('date' , '<=' , Carbon::parse($request -> EndDate) -> addDay());

        $exp = $expenses ->get() -> groupBy('account_name') -> map(function ($item) {
            return [
                'total' => $item -> sum('amount'),
            ];
        });

        return view('admin.ReportAccount.movement_report_result' , compact('company' , 'routes' , 'data' , 'period' , 'period_ar' , 'karats' , 'reW' , 'reO' ,
            'salesW' , 'salesO' , 'returnW' , 'returnO' , 'purchaseW' , 'purchaseO' , 'salesWorkVAl' , 'returnWorkVAl' , 'exp'));

    }

    public function account_movement_report(){ 
        $subscriberId = Auth::user()->subscriber_id ?? null;
        $accounts = AccountsTree::all();
        $branches = Branch::where('status',1)
            ->when($subscriberId, fn($q) => $q->where('subscriber_id', $subscriberId))
            ->get();
        $costCenters = CostCenter::query()
            ->when($subscriberId, fn($q) => $q->where('subscriber_id', $subscriberId))
            ->where('is_active', 1)
            ->orderBy('name')
            ->get();
        return view('admin.ReportAccount.account_movement' , compact('accounts','branches','costCenters'));
    }

    public function account_movement_report_search(Request $request){

        $startDate = Carbon::now()->addYears(-5);
        $endDate = Carbon::now() -> addDays(2);
        $period = 'Period : ';
        $period_ar = 'الفترة  :';

        if($request -> has('isStartDate')){
            $startDate = $request->StartDate;
            $period .= $startDate ;
            $period_ar .= Carbon::parse($startDate) -> format('d-m-Y') ;
        } else {
            $period .= 'Starting Date';
            $period_ar .= 'من البداية' ;
        }

        if($request -> has('isEndDate')){
            $endDate =  Carbon::parse($request->EndDate) -> addDay(2)  ;
            $period .= ' -- '  . $endDate -> format('d-m-Y') ;
            $period_ar .= ' - '  .Carbon::parse($endDate) -> addDay(-2)  -> format('d-m-Y');
        } else {
            $period .= ' -- '  . 'Today' ;
            $period_ar .= ' -- '  . 'حتى اليوم' ;
        }

        $branchId = (int) ($request->branch_id ?? 0);
        $costCenterId = (int) ($request->cost_center_id ?? 0);

        $accounts = DB::table('accounts_trees')
                        ->join('account_movements','accounts_trees.id','=','account_movements.account_id')
                        ->join('journals','journals.id','=','account_movements.journal_id')
                        ->select('accounts_trees.code','accounts_trees.name','accounts_trees.side'
                            ,'journals.basedon_no','journals.baseon_text'
                            ,'account_movements.credit as credit'
                            ,'account_movements.debit as debit' 
                            ,'account_movements.date') 
                        ->where('account_movements.date','>=',$startDate)
                        ->where('account_movements.date','<=',$endDate)
                        ->where('accounts_trees.id' , '=' , $request -> account_id) 
                        ->when($branchId > 0, function ($q) use ($branchId) {
                            $q->where('journals.branch_id', $branchId);
                        })
                        ->when($costCenterId > 0, function ($q) use ($costCenterId) {
                            $q->where('journals.cost_center_id', $costCenterId);
                        })
                        ->when(Auth::user()->subscriber_id ?? null,function($q,$sub){
                            if (Schema::hasColumn('accounts_trees','subscriber_id')) {
                                $q->where('accounts_trees.subscriber_id',$sub);
                            }
                        })
                        ->get();

        $account_balance = DB::table('accounts_trees')
                        ->join('account_movements','accounts_trees.id','=','account_movements.account_id')
                        ->join('journals','journals.id','=','account_movements.journal_id')
                        ->select('accounts_trees.code','accounts_trees.name as account_name','accounts_trees.side',
                            DB::raw('SUM(account_movements.credit) before_credit'),
                            DB::raw('SUM(account_movements.debit) before_debit'))
                        ->groupBy('accounts_trees.id','accounts_trees.code','accounts_trees.name','accounts_trees.side')
                        ->where('account_movements.date','<',$startDate)
                        ->where('accounts_trees.id' , '=' , $request -> account_id) 
                        ->when($branchId > 0, function ($q) use ($branchId) {
                            $q->where('journals.branch_id', $branchId);
                        })
                        ->when($costCenterId > 0, function ($q) use ($costCenterId) {
                            $q->where('journals.cost_center_id', $costCenterId);
                        })
                        ->when(Auth::user()->subscriber_id ?? null,function($q,$sub){
                            if (Schema::hasColumn('accounts_trees','subscriber_id')) {
                                $q->where('accounts_trees.subscriber_id',$sub);
                            }
                        })
                        ->first();
 
       
        $isaccount = AccountsTree::where('id',$request -> account_id) -> first();
        $account_name = $isaccount ? ($isaccount->name .' - '. $isaccount->code) : '';
        $companyAccount = Company::query()->where('account_id', $request->account_id)->first();
        if ($companyAccount && (float) $companyAccount->opening_balance != 0.0) {
            $openingAmount = abs((float) $companyAccount->opening_balance);
            $openingDebit = 0.0;
            $openingCredit = 0.0;
            $isCustomer = (int) $companyAccount->group_id === 3;
            $isSupplier = (int) $companyAccount->group_id === 4;

            if ($companyAccount->opening_balance < 0) {
                $openingDebit = $isSupplier ? $openingAmount : 0.0;
                $openingCredit = $isCustomer ? $openingAmount : 0.0;
            } else {
                $openingDebit = $isCustomer ? $openingAmount : 0.0;
                $openingCredit = $isSupplier ? $openingAmount : 0.0;
            }

            if ($account_balance) {
                $account_balance->before_debit = (float) ($account_balance->before_debit ?? 0) + $openingDebit;
                $account_balance->before_credit = (float) ($account_balance->before_credit ?? 0) + $openingCredit;
            } else {
                $account_balance = (object) [
                    'side' => $isaccount->side ?? ($isSupplier ? 2 : 1),
                    'before_debit' => $openingDebit,
                    'before_credit' => $openingCredit,
                ];
            }
        }
        $company = CompanyInfo::all() -> first();
      
        return view('admin.ReportAccount.account_movement_report',compact('accounts','account_balance','period', 'period_ar', 'company','account_name'));
    }

    public function account_company_report_search($id){ 

        $period_ar = 'الفترة  :'; 
        $period_ar .= 'من البداية' ;  
        $period_ar .= ' -- '  . 'حتى اليوم' ; 

        $accounts = DB::table('accounts_trees')
                        ->join('account_movements','accounts_trees.id','=','account_movements.account_id')
                        ->join('journals','journals.id','=','account_movements.journal_id')
                        ->select('accounts_trees.code','accounts_trees.name','accounts_trees.side'
                            ,'journals.basedon_no','journals.baseon_text'
                            ,'account_movements.credit as credit'
                            ,'account_movements.debit as debit' 
                            ,'account_movements.date')  
                        ->where('accounts_trees.id',$id) 
                        ->get();

        $account_balance = DB::table('accounts_trees')
                        ->join('account_movements','accounts_trees.id','=','account_movements.account_id')
                        ->select('accounts_trees.code','accounts_trees.name as account_name','accounts_trees.side',
                            DB::raw('SUM(account_movements.credit) before_credit'),
                            DB::raw('SUM(account_movements.debit) before_debit'))
                        ->groupBy('accounts_trees.id','accounts_trees.code','accounts_trees.name','accounts_trees.side')
                        ->whereYear('account_movements.date','<',date("Y"))
                        ->where('accounts_trees.id', $id) 
                        ->first();
 
       
        $isaccount = AccountsTree::where('id',$id) -> first();
        $account_name = $isaccount ? ($isaccount->name .' - '. $isaccount->code) : '';
        $companyAccount = Company::query()->where('account_id', $id)->first();
        if ($companyAccount && (float) $companyAccount->opening_balance != 0.0) {
            $openingAmount = abs((float) $companyAccount->opening_balance);
            $openingDebit = 0.0;
            $openingCredit = 0.0;
            $isCustomer = (int) $companyAccount->group_id === 3;
            $isSupplier = (int) $companyAccount->group_id === 4;

            if ($companyAccount->opening_balance < 0) {
                $openingDebit = $isSupplier ? $openingAmount : 0.0;
                $openingCredit = $isCustomer ? $openingAmount : 0.0;
            } else {
                $openingDebit = $isCustomer ? $openingAmount : 0.0;
                $openingCredit = $isSupplier ? $openingAmount : 0.0;
            }

            if ($account_balance) {
                $account_balance->before_debit = (float) ($account_balance->before_debit ?? 0) + $openingDebit;
                $account_balance->before_credit = (float) ($account_balance->before_credit ?? 0) + $openingCredit;
            } else {
                $account_balance = (object) [
                    'side' => $isaccount->side ?? ($isSupplier ? 2 : 1),
                    'before_debit' => $openingDebit,
                    'before_credit' => $openingCredit,
                ];
            }
        }
        $company = CompanyInfo::all() -> first();
      
        return view('admin.ReportAccount.account_movement_report',compact('accounts','account_balance','period_ar', 'company','account_name'));
    }

 
    public function account_companies_details_report(){ 
        $accounts = AccountsTree::where('parent_code',2101) -> get();
        $subscriberId = Auth::user()->subscriber_id ?? null;
        $branches = Branch::where('status',1)
            ->when($subscriberId, fn($q) => $q->where('subscriber_id', $subscriberId))
            ->get();
        $costCenters = CostCenter::query()
            ->when($subscriberId, fn($q) => $q->where('subscriber_id', $subscriberId))
            ->where('is_active', 1)
            ->orderBy('name')
            ->get();
        return view('admin.ReportAccount.account_companies_details' , compact('accounts','branches','costCenters'));
    }

    public function account_companies_details_search(Request $request){

        $startDate = Carbon::now()->addYears(-5);
        $endDate = Carbon::now() -> addDays(1);
        $period = 'Period : ';
        $period_ar = 'الفترة  :';

        if($request -> has('isStartDate')){
            $startDate = $request->StartDate;
            $period .= $startDate ;
            $period_ar .= Carbon::parse($startDate) -> format('d-m-Y') ;
        } else {
            $period .= 'Starting Date';
            $period_ar .= 'من البداية' ;
        }

        if($request -> has('isEndDate')){
            $endDate =  Carbon::parse($request->EndDate) -> addDay()  ;
            $period .= ' -- '  . $endDate -> format('d-m-Y') ;
            $period_ar .= ' - '  .Carbon::parse($endDate) -> addDay(-1)  -> format('d-m-Y');
        } else {
            $period .= ' -- '  . 'Today' ;
            $period_ar .= ' -- '  . 'حتى اليوم' ;
        }

        $costCenterId = (int) ($request->cost_center_id ?? 0);
        $accounts = DB::table('accounts_trees')
                        ->join('account_movements','accounts_trees.id','=','account_movements.account_id')
                        ->join('journals','journals.id','=','account_movements.journal_id')
                        ->select('accounts_trees.code','accounts_trees.name','accounts_trees.side'
                            ,'journals.basedon_no','journals.branch_id', 'journals.basedon_id','journals.baseon_text','journals.total_credit AS K24','journals.total_debit AS K21','journals.notes AS K18'
                            ,'account_movements.credit as credit'
                            ,'account_movements.debit as debit' , 'account_movements.notes','account_movements.date') 
                        ->where('account_movements.date','>=',$startDate)
                        ->where('account_movements.date','<=',$endDate)
                        ->where('accounts_trees.id' , '=' , $request -> account_id); 

        if($request -> branch_id > 0){
            $accounts = $accounts->where('journals.branch_id', $request -> branch_id);
        }
        if ($costCenterId > 0) {
            $accounts = $accounts->where('journals.cost_center_id', $costCenterId);
        }
        $accounts = $accounts->get();
         
        if($request -> branch_id > 0){
            $account_balance = DB::table('accounts_trees')
                ->join('account_movements','accounts_trees.id','=','account_movements.account_id')
                ->join('journals','journals.id','=','account_movements.journal_id')
                ->select('accounts_trees.code','accounts_trees.name as account_name','accounts_trees.side',
                    DB::raw('SUM(account_movements.credit) before_credit'),
                    DB::raw('SUM(account_movements.debit) before_debit'))
                ->groupBy('accounts_trees.id','accounts_trees.code','accounts_trees.name','accounts_trees.side')
                ->where('account_movements.date','<',$startDate)
                ->where('accounts_trees.id' , '=' , $request -> account_id)
                ->where('journals.branch_id' , '=' , $request -> branch_id)
                ->when($costCenterId > 0, function ($q) use ($costCenterId) {
                    $q->where('journals.cost_center_id', $costCenterId);
                })
                ->first();
        }else{
            $account_balance = DB::table('accounts_trees')
                ->join('account_movements','accounts_trees.id','=','account_movements.account_id')
                ->join('journals','journals.id','=','account_movements.journal_id')
                ->select('accounts_trees.code','accounts_trees.name as account_name','accounts_trees.side',
                    DB::raw('SUM(account_movements.credit) before_credit'),
                    DB::raw('SUM(account_movements.debit) before_debit'))
                ->groupBy('accounts_trees.id','accounts_trees.code','accounts_trees.name','accounts_trees.side')
                ->where('account_movements.date','<',$startDate)
                ->where('accounts_trees.id' , '=' , $request -> account_id)
                ->when($costCenterId > 0, function ($q) use ($costCenterId) {
                    $q->where('journals.cost_center_id', $costCenterId);
                })
                ->first();
        }
  
        foreach ($accounts as $account){ 
             
            if (EnterOld::where('bill_number', $account->basedon_no)->exists()) {  
                $accountkarat = DB::table('enter_old_details')
                    ->join('karats','enter_old_details.karat_id','=','karats.id')
                    ->select('enter_old_details.id','karats.name_ar', 
                        DB::raw('SUM(CASE WHEN karats.label="K24"   THEN enter_old_details.weight END) K24'),
                        DB::raw('SUM(CASE WHEN karats.label="K21"  THEN enter_old_details.weight END) K21'),
                        DB::raw('SUM(CASE WHEN karats.label="K18" THEN enter_old_details.weight END) K18') )
                    ->groupBy('enter_old_details.id','karats.name_ar' )
                    ->where('enter_old_details.bill_id',$account->basedon_id) 
                    ->first();

                if($accountkarat){
                    $account->K24 = $accountkarat->K24;
                    $account->K21 = $accountkarat->K21;
                    $account->K18 = $accountkarat->K18;
                }
            } 

            if (EnterWork::where('bill_number', $account->basedon_no)->exists()) {  
                $accountkarats = DB::table('purchase_details')
                    ->join('karats','purchase_details.karat_id','=','karats.id')
                    ->select('karats.name_ar', 
                        DB::raw('SUM(CASE WHEN karats.label="K24"  THEN purchase_details.weight END) K24'),
                        DB::raw('SUM(CASE WHEN karats.label="K21"  THEN purchase_details.weight END) K21'),
                        DB::raw('SUM(CASE WHEN karats.label="K18"  THEN purchase_details.weight END) K18') )
                    ->groupBy('purchase_details.karat_id','karats.name_ar')
                    ->where('purchase_details.bill_id',$account->basedon_id) 
                    ->when(Auth::user()->subscriber_id ?? null,function($q,$sub){
                        if (Schema::hasColumn('purchase_details','subscriber_id')) {
                            $q->where('purchase_details.subscriber_id',$sub);
                        }
                    })
                    ->get();

                if($accountkarats){
                    foreach($accountkarats as $accountkarat){
                        if( $accountkarat->K24 >0 ){
                            $account->K24 = $accountkarat->K24;
                        }
                        if( $accountkarat->K21 >0 ){
                            $account->K21 = $accountkarat->K21;
                        }   
                        if( $accountkarat->K18 >0 ){
                            $account->K18 = $accountkarat->K18;
                        }      
                    }
                }
            } 
        }
         
        $isaccount = AccountsTree::where('id',$request -> account_id) -> first();
        $account_name = $isaccount->name .' - '. $isaccount ->code;
        $company = CompanyInfo::all() -> first();
        $branch = Branch::find($request -> branch_id);

        return view('admin.ReportAccount.account_companies_details_report'
            ,compact('accounts','branch','account_balance' ,'period' , 'period_ar' 
            , 'company','account_name'));
  
    } 

    public function tax_declaration(){
        $branches = Branch::where('status',1)->get();
        $taxNumbers = $branches->whereNotNull('tax_number')->pluck('tax_number')->unique()->values();
        return view('admin.ReportAccount.tax_declaration_report', compact('branches','taxNumbers'));
    }

    public function tax_declaration_report_search(Request $request){
        $startDate = Carbon::now()->addYears(-5);
        $endDate = Carbon::now() -> addDays(1);

        $branches = Branch::where('status',1)->get();
        $taxNumbers = $branches->whereNotNull('tax_number')->pluck('tax_number')->unique()->values();

        $request->validate([
            'branch_id' => 'nullable',
            'tax_number' => 'nullable'
        ]);

        $branchIds = [];
        if($request->branch_id && $request->branch_id > 0){
            $branchIds = [$request->branch_id];
        } elseif($taxNumbers->count() > 1 && empty($request->tax_number)){
            return redirect()->back()->withErrors(['tax_number' => __('main.tax_number').' '.__('validation.required')]);
        } elseif(!empty($request->tax_number)){
            $branchIds = Branch::where('tax_number',$request->tax_number)->pluck('id')->toArray();
        }

        $period = 'Period : ';
        $period_ar = 'الفترة  : ';
        if($request -> has('isStartDate')){
            $startDate = $request->StartDate;
            $period .= $startDate ;
            $period_ar .= $startDate ;
        } else {
            $period .= 'Starting Date';
            $period_ar .= 'من البداية' ;
        }

        if($request -> has('isEndDate')){
            $endDate =  Carbon::parse($request->EndDate)  ;
            $period .= ' -- '  . $endDate -> format('d-m-Y') ;
            $period_ar .= ' -- '  . $endDate -> format('d-m-Y');
        } else {
            $period .= ' -- '  . 'Today' ;
            $period_ar .= ' -- '  . 'حتى اليوم' ;
        }

        $company = CompanyInfo::first();
   
        $sales = SaleDetails::join('sales', 'sale_details.sale_id', '=', 'sales.id')
                    ->select(SaleDetails::raw('sum(sale_details.total) as total,sum(sale_details.tax) as tax'))
                    ->where('sales.date','>=', Carbon::parse($request -> StartDate) )
                    ->where('sales.date','<=', Carbon::parse($request -> EndDate) -> addDay()) 
                    ->where('sales.net','>', 0)
                    ->where('sale_details.tax','>', 0);
        if(!empty($branchIds)){
            $sales = $sales->whereIn('sales.branch_id',$branchIds);
        }
        $sales = $sales->first();

        $salesReturn = SaleDetails::join('sales', 'sale_details.sale_id', '=', 'sales.id')
                        ->select(SaleDetails::raw('sum(sale_details.total * -1) as total,sum(sale_details.tax * -1) as tax'))
                        ->where('sales.date','>=', Carbon::parse($request -> StartDate) )
                        ->where('sales.date','<=', Carbon::parse($request -> EndDate) -> addDay()) 
                        ->where('sales.net','<', 0)
                        ->where('sale_details.tax','<', 0);   
        if(!empty($branchIds)){
            $salesReturn = $salesReturn->whereIn('sales.branch_id',$branchIds);
        }
        $salesReturn = $salesReturn->first();   

        $salesTaxZero = SaleDetails::join('sales', 'sale_details.sale_id', '=', 'sales.id')
                            ->select(SaleDetails::raw('sum(sale_details.total) as total,sum(sale_details.tax) as tax'))
                            ->where('sales.date', '>=', Carbon::parse($request -> StartDate) )
                            ->where('sales.date', '<=', Carbon::parse($request -> EndDate) -> addDay()) 
                            ->where('sales.net', '>', 0)   
                            ->where('sale_details.tax','=', 0);
        if(!empty($branchIds)){
            $salesTaxZero = $salesTaxZero->whereIn('sales.branch_id',$branchIds);
        }
        $salesTaxZero = $salesTaxZero->first();

        $salesReturnTaxZero = SaleDetails::join('sales', 'sale_details.sale_id', '=', 'sales.id')
                                ->select(SaleDetails::raw('sum(sale_details.total * -1) as total,sum(sale_details.tax * -1) as tax'))
                                ->where('sales.date','>=', Carbon::parse($request -> StartDate) )
                                ->where('sales.date','<=', Carbon::parse($request -> EndDate) -> addDay()) 
                                ->where('sales.net', '<', 0)  
                                ->where('sale_details.tax', '=', 0);
        if(!empty($branchIds)){
            $salesReturnTaxZero = $salesReturnTaxZero->whereIn('sales.branch_id',$branchIds);
        }
        $salesReturnTaxZero = $salesReturnTaxZero->first();  
                
        $purchase = DB::table('purchase_details')
                        ->join('purchases', 'purchase_details.purchase_id', '=', 'purchases.id') 
                        ->select(PurchaseDetails::raw('sum(purchase_details.total) total,sum(purchase_details.tax) tax'))
                        ->where('purchases.date', '>=', Carbon::parse($request -> StartDate) )
                        ->where('purchases.date', '<=', Carbon::parse($request -> EndDate) -> addDay())  
                        ->where('purchase_details.tax', '>', 0);
        if(!empty($branchIds)){
            $purchase = $purchase->whereIn('purchases.branch_id',$branchIds);
        }
        $purchase = $purchase->first(); 

        $purchaseReturn = DB::table('purchase_details')
                        ->join('purchases', 'purchase_details.purchase_id', '=', 'purchases.id') 
                        ->select(PurchaseDetails::raw('sum(purchase_details.total * -1) total,sum(purchase_details.tax * -1) tax'))
                        ->where('purchases.date', '>=', Carbon::parse($request -> StartDate) )
                        ->where('purchases.date', '<=', Carbon::parse($request -> EndDate) -> addDay())  
                        ->where('purchase_details.tax', '<', 0)  
                        ->where('purchases.net', '<', 0);
        if(!empty($branchIds)){
            $purchaseReturn = $purchaseReturn->whereIn('purchases.branch_id',$branchIds);
        }
        $purchaseReturn = $purchaseReturn->first(); 
 
        $purchaseTaxZero =  DB::table('purchase_details')
                            ->join('purchases', 'purchase_details.purchase_id', '=', 'purchases.id') 
                            ->select(PurchaseDetails::raw('sum(purchase_details.total) total')) 
                            ->where('purchases.date', '>=', Carbon::parse($request -> StartDate) )
                            ->where('purchases.date', '<=', Carbon::parse($request -> EndDate) -> addDay()) 
                            ->where('purchase_details.tax',0);
        if(!empty($branchIds)){
            $purchaseTaxZero = $purchaseTaxZero->whereIn('purchases.branch_id',$branchIds);
        }
        $purchaseTaxZero = $purchaseTaxZero->first();  

        $purchaseReturnTaxZero =  DB::table('purchase_details')
                            ->join('purchases', 'purchase_details.purchase_id', '=', 'purchases.id') 
                            ->select(PurchaseDetails::raw('sum(purchase_details.total * -1) total')) 
                            ->where('purchases.date', '>=', Carbon::parse($request -> StartDate) )
                            ->where('purchases.date', '<=', Carbon::parse($request -> EndDate) -> addDay()) 
                            ->where('purchases.net', '<', 0)  
                            ->where('purchase_details.tax',0);
        if(!empty($branchIds)){
            $purchaseReturnTaxZero = $purchaseReturnTaxZero->whereIn('purchases.branch_id',$branchIds);
        }
        $purchaseReturnTaxZero = $purchaseReturnTaxZero->first();  
      
        return view('admin.ReportAccount.tax_declaration_report_result', compact('company', 'period', 'period_ar'  
                    ,'sales', 'salesReturn', 'salesTaxZero', 'salesReturnTaxZero', 'purchase'
                    , 'purchaseReturn', 'purchaseTaxZero','purchaseReturnTaxZero'));
      
      
    }


    public function inventory_report($id){

        $inventory = Inventory::FindOrFail($id);    

        $inventory_sum = Product::join('inventory_details','products.id','=','inventory_details.item_id')
            ->selectRaw('sum((case when inventory_details.is_counted = 1 then inventory_details.new_quantity else inventory_details.quantity end) * products.cost) sum_weight_new, sum(inventory_details.quantity * products.cost) sum_weight_old')
            ->where('inventory_details.inventory_id' ,$id)    
            ->first(); 

        $inventory_items = InventoryDetails::where('inventory_id' ,$id)   
            ->get();  

        $company = CompanyInfo::all() -> first();

        return view('admin.ReportAccount.item_inventory_report_result' , compact('company','inventory_sum','inventory_items','inventory'));
            
    }

    
}

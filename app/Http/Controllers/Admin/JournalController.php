<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller; 
use App\Models\Journal;
use App\Http\Requests\StoreJournalRequest;
use App\Http\Requests\UpdateJournalRequest;
use App\Models\CompanyInfo;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class JournalController extends Controller
{
    public function incoming_list(){ 
        return view('admin.ReportAccount.incoming_list');
    }

    public function search_incoming_list(Request $request){
        
        $startDate = Carbon::now()->addYears(-5);
        $endDate = Carbon::now()->addDays(1);
        $period = 'Period : ';
        $period_ar = 'الفترة  :';

        if($request -> has('isStartDate')){
            $startDate = $request->StartDate;
            $period .= $startDate;
            $period_ar .= $startDate ;
        } else {
            $period .= 'Starting Date';
            $period_ar .= 'من البداية';
        }

        if($request -> has('isEndDate')){
            $endDate =  Carbon::parse($request->EndDate)->addDay();
            $period .= ' - '.$endDate;
            $period_ar .= ' - '.Carbon::parse($endDate)->addDay(-1)->format('d-m-Y');
        } else {
            $period .= ' - '.'Today';
            $period_ar .= ' - '.'حتي اليوم';
        }

        $accounts = DB::table('accounts_trees')
            ->join('account_movements','accounts_trees.id','=','account_movements.account_id')
            ->select('accounts_trees.id as idd','accounts_trees.code','accounts_trees.name' , 'accounts_trees.parent_id' , 'accounts_trees.level',
                DB::raw('SUM(account_movements.credit) credit'),
                DB::raw('SUM(account_movements.debit) debit'),
                DB::raw('(CASE WHEN accounts_trees.parent_id = account_movements.account_id THEN accounts_trees.name END) childs'),
            )
            ->groupBy('accounts_trees.id','accounts_trees.code','accounts_trees.name' , 'accounts_trees.parent_id' , 'accounts_trees.level' , 'account_movements.account_id')
            ->where('accounts_trees.department','=',2) 
            ->where('account_movements.date','>=',$startDate)
            ->where('account_movements.date','<=',$endDate)
            ->get();

        $accounts1 =  $accounts -> where('level' ,'=', 1) ; 

        foreach ($accounts1 as $account){
            $list = $accounts -> where('parent_id' ,'=',$account -> idd);
            $account -> childs =  $list ? $list  : []; 
            foreach($account -> childs as $child){
                $list2 = $accounts -> where('parent_id','=',$child -> idd);
                $child -> childs = $list2 ? $list2 : []; 
                foreach($child -> childs as $subChild){
                    $list22 = $accounts -> where('parent_id','=',$subChild -> idd);
                    $subChild -> childs = $list22 ? $list22  : [];
                }
            }
        }

        $company = CompanyInfo::first();

        return view('admin.ReportAccount.incoming_list_report'
            ,compact('accounts1', 'period', 'period_ar', 'company'));
        
    } 

    public function incoming_list_old(){
        $accounts = DB::table('accounts_trees')
            ->join('account_movements','accounts_trees.id','=','account_movements.account_id')
            ->select('accounts_trees.code','accounts_trees.name',
                DB::raw('sum(account_movements.credit) as credit'),
                DB::raw('sum(account_movements.debit) as debit'))
            ->groupBy('accounts_trees.id','accounts_trees.code','accounts_trees.name')
            ->where('accounts_trees.department',0)
            ->get();

        return view('admin.Report.incoming_list_report',compact('accounts'));
    }
 

    public function balance_sheet(){
        return view('admin.ReportAccount.balance_sheet');
    }

    public function search_balance_sheet(Request $request){

        $startDate = Carbon::now()->addYears(-5);
        $endDate = Carbon::now() -> addDays(1);
        $period = 'Period : ';
        $period_ar = 'الفترة  :';

        if($request -> has('isStartDate')){
            $startDate = $request->StartDate;
            $period .= $startDate;
            $period_ar .= $startDate;
        } else {
            $period .= 'Starting Date';
            $period_ar .= 'من البداية';
        }

        if($request -> has('isEndDate')){
            $endDate =  Carbon::parse($request->EndDate) -> addDay();
            $period .= ' - '  . $endDate;
            $period_ar .= ' - '  . $endDate ;
        } else {
            $period .= ' - '  . 'Today';
            $period_ar .= ' - '  . 'حتي اليوم';
        }

        $accounts = DB::table('accounts_trees')
            ->join('account_movements','accounts_trees.id','=','account_movements.account_id')
            ->select('accounts_trees.id as idd','accounts_trees.code','accounts_trees.name',  'accounts_trees.parent_id' , 'accounts_trees.level',
                DB::raw('sum(account_movements.credit) as credit'),
                DB::raw('sum(account_movements.debit) as debit'))
            ->groupBy('accounts_trees.id','accounts_trees.code','accounts_trees.name' , 'accounts_trees.parent_id' , 'accounts_trees.level' )
            ->where('accounts_trees.department',1)
            ->get();

        $accounts1 =  $accounts -> where('level' , '=' ,1); 
 
        foreach ($accounts1 as $account){
            $list = $accounts -> where('parent_id' , '=' ,$account -> idd );
            $account -> childs =  $list ? $list  : [] ;

            foreach($account -> childs as $child){
                $list2 = $accounts -> where('parent_id' , '=' ,$child -> idd );
                $child -> childs = $list2 ? $list2 : [];

                foreach($child -> childs as $subChild){
                    $list22 = $accounts -> where('parent_id' , '=' ,$subChild -> idd );
                    $subChild -> childs = $list22 ? $list22  : [];
                }
            }

        }
   
        $company = CompanyInfo::first();
      
        return view('admin.ReportAccount.balance_sheet_report'
            ,compact('accounts1', 'period', 'period_ar', 'company'));
    }

    public function create(){
        return view('admin.accounts.manual');
    }

    public function store(Request $request){
        $siteController = new SystemController();
        $header =[
            'date' => date('Y-m-d').'T'.date('H:i'),
            'basedon_no' => '',
            'basedon_id' => 0,
            'baseon_text' => 'سند قيد يدوي',
            'total_credit' => 0,
            'total_debit' => 0,
            'notes' => $request->notes ? $request->notes : ''
        ];

        $details = [];
        foreach ($request->account_id as $index=>$account_id){
            $accountId = $account_id;
            $credit = $request->credit[$index];
            $debit = $request->debit[$index];
            $ledger = 0;
            $details[] = [
                'account_id' => $accountId,
                'credit' => $credit,
                'debit' => $debit,
                'ledger_id' => $ledger,
                'notes' => ''
            ];
        }

        $siteController->insertJournal($header,$details,1);

        return redirect()->route('journals');
    }

    public function delete($id){

        $header = [
            'date' => '',
            'basedon_no' => '',
            'basedon_id' => '',
            'baseon_text' => 'سند قيد يدوي رقم '.$id,
            'total_credit' => 0,
            'total_debit' => 0,
            'notes' => ''
        ];
        $siteController = new SystemController();
        $siteController->deleteJournal($header);

        return redirect()->route('journals');
    }

    public function manual_number(){
        $bills = Journal::orderBy('id', 'ASC') ->get();
        
        if(count($bills) > 0){
            $id = $bills[count($bills) -1] -> id ;
        } else{
            $id = 0 ;
        }
            
        $no = json_encode (str_pad($id + 1, 6 , '0' , STR_PAD_LEFT)) ;
        echo $no ;
        exit;
    }

}

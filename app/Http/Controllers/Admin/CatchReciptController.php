<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccountsTree;
use App\Models\AccountSetting;
use App\Models\CatchRecipt;
use App\Models\ExpenseType;
use App\Models\Company;
use App\Models\Journal;
use App\Models\JournalDetails;
use App\Models\AccountMovement;
use App\Models\Branch;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Alkoumi\LaravelArabicTafqeet\Tafqeet;
use App\Models\CompanyInfo;

class CatchReciptController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $bills = CatchRecipt::join('accounts_trees as from_account' , 'from_account.id' , '=' , 'catch_recipts.from_account')
            -> join('accounts_trees as to_account' , 'to_account.id' , '=' , 'catch_recipts.to_account')
            -> select('catch_recipts.*'  , 'from_account.name as from_account_name' , 'to_account.name as to_account_name')
            -> orderBy('id', 'DESC')
            -> get(); 

        if (!empty(Auth::user()->branch_id)) {
            $bills = $bills  -> where('branch_id', Auth::user()->branch_id);
        }  

        $branches = Branch::where('status',1)->get();
        $subscriberId = Auth::user()->subscriber_id ?? null;
        $accounts = AccountsTree::withoutGlobalScope('subscriber')
            ->where('type', 3)
            ->when($subscriberId !== null, function ($q) use ($subscriberId) {
                $q->where(function ($query) use ($subscriberId) {
                    $query->whereNull('subscriber_id')
                        ->orWhere('subscriber_id', 0)
                        ->orWhere('subscriber_id', $subscriberId);
                });
            })
            ->when(\Schema::hasColumn('accounts_trees', 'is_active'), function ($q) {
                $q->where('is_active', 1);
            })
            ->orderBy('name')
            ->get(['id', 'name']);
        
        return view('admin.catchs.index' , compact( 'bills','accounts','branches' ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'docNumber' =>  'required|unique:catch_recipts',
            'date' => 'required', 
            'amount' => 'required',
            'account_id' => 'required',
            'parent_code' => 'required',
            'payment_type' => 'required',
            'branch_id' => 'required'
        ]);

        $settings = AccountSetting::where('branch_id', $request->branch_id)->first();
        if ($request->payment_type == 0) {
            $from_account = $settings?->safe_account ?? null;
        } else {
            $from_account = $settings?->bank_account ?? null;
        }
        if (!$from_account) {
            return redirect()->route('catches')
                ->with('error', __('main.account_settings') . ': ' . __('validation.required', ['attribute' => __('main.accounting')]));
        }

        $id =  CatchRecipt::create([
            'branch_id' => $request -> branch_id,
            'docNumber' => $request -> docNumber,
            'date' => Carbon::parse($request -> date) ,
            'from_account' => $from_account,
            'to_account' => $request -> account_id,
            'client' => $request -> client ?? '',
            'amount' => $request -> amount,
            'notes' => $request -> notes ?? '', 
            'payment_type' => $request -> payment_type,
            'user_id' => Auth::user() -> id
        ]) -> id   ;

        $auto_accounting =  env("AUTO_ACCOUNTING", 1);
        if($auto_accounting == 1){
            $systemController = new SystemController(); 
            $systemController -> CatchAccounting($id);
        }

        return redirect()->route('catches')->with('success', __('main.created'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CatchRecipt  $catchRecipt
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $expense = CatchRecipt::find($id);
        if ($expense) {
            $account = AccountsTree::withoutGlobalScope('subscriber')->find($expense->to_account);
            $expense->parent_code = $account ? $account->parent_code : null;
            $expense->account_type = $account ? $account->type : null;
        }

        return response()->json($expense);

    }
    public function print($id){
        $bill = CatchRecipt::find($id);
        //$valAr = Tafqeet::inArabic($bill -> amount,'sar');
        $valAr ='';
        $company = CompanyInfo::all() -> first();
        $account = AccountsTree::where('id',$bill->to_account)->first();
        return view('admin.catchs.print' , compact('bill' , 'valAr' , 'company','account'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CatchRecipt  $catchRecipt
     * @return \Illuminate\Http\Response
     */
    public function edit(CatchRecipt $catchRecipt)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CatchRecipt  $catchRecipt
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CatchRecipt $catchRecipt)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CatchRecipt  $catchRecipt
     * @return \Illuminate\Http\Response
     */
    public function  destroy($id)
    {
        if(CatchRecipt::where('id', $id)->exists()){
            $bill = CatchRecipt::find($id);  
            $journal_id2 = Journal::where('basedon_no', $bill->docNumber)->first()->id;
            JournalDetails::where('journal_id', $journal_id2)->delete();
            Journal::where('basedon_no', $bill->docNumber)->delete();
            AccountMovement::where('journal_id', $journal_id2)->delete();  
            $bill ->delete();
        }
        return back()->with('success',__('main.deleted'));
    }

    public function getSupplierAccount($id){ 
        $subscriberId = Auth::user()->subscriber_id ?? null;
        $typeOptions = [0, 1, 2, 3];
        if (in_array((int) $id, $typeOptions, true)) {
            $accounts = AccountsTree::withoutGlobalScope('subscriber')
                ->where('type', (int) $id)
                ->when($subscriberId !== null, function ($q) use ($subscriberId) {
                    $q->where(function ($query) use ($subscriberId) {
                        $query->whereNull('subscriber_id')
                            ->orWhere('subscriber_id', 0)
                            ->orWhere('subscriber_id', $subscriberId);
                    });
                })
                ->when(\Schema::hasColumn('accounts_trees', 'is_active'), function ($q) {
                    $q->where('is_active', 1);
                })
                ->orderBy('name')
                ->get(['id', 'name']);
        } else {
            $parentById = AccountsTree::withoutGlobalScope('subscriber')->where('id', $id)->first();
            $parentByCode = AccountsTree::withoutGlobalScope('subscriber')->where('code', $id)->first();
            $parentIds = collect([$parentById?->id, $parentByCode?->id])->filter()->unique()->values();
            $parentCodes = collect([$parentById?->code, $parentByCode?->code, $id])->filter()->unique()->values();

            $accounts = AccountsTree::withoutGlobalScope('subscriber')
                ->where(function ($query) use ($parentIds, $parentCodes) {
                    if ($parentCodes->isNotEmpty()) {
                        $query->whereIn('parent_code', $parentCodes);
                    }
                    if ($parentIds->isNotEmpty()) {
                        $query->orWhereIn('parent_id', $parentIds);
                    }
                })
                ->when($subscriberId !== null, function ($q) use ($subscriberId) {
                    $q->where(function ($query) use ($subscriberId) {
                        $query->whereNull('subscriber_id')
                            ->orWhere('subscriber_id', 0)
                            ->orWhere('subscriber_id', $subscriberId);
                    });
                })
                ->when(\Schema::hasColumn('accounts_trees', 'is_active'), function ($q) {
                    $q->where('is_active', 1);
                })
                ->orderBy('name')
                ->get(['id', 'name']);
        }

        return response()->json($accounts);
    } 

    public function get_Catch_no($branch_id){
        $bills = CatchRecipt::where('branch_id', $branch_id) 
            ->count();
        if($bills > 0){
            $id = $bills ;
        } else{
            $id = 0 ;
        }
      
        $i = 0;
        do { 
            $i++;
            $prefix = "ETSM-".$branch_id."-";  
            $no = json_encode($prefix . str_pad($id + $i, 6 , '0' , STR_PAD_LEFT)) ;
        } while (CatchRecipt::where("docNumber","=",$prefix . str_pad($id + $i, 6 , '0' , STR_PAD_LEFT))->exists());
        echo $no ;
        exit;
    }
}

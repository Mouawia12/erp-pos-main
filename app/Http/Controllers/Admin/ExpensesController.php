<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccountsTree;
use App\Models\AccountSetting;
use App\Models\Expenses;
use App\Models\ExpenseType;
use App\Models\Branch;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Alkoumi\LaravelArabicTafqeet\Tafqeet;
use App\Models\CompanyInfo;

class ExpensesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $bills = Expenses::join('accounts_trees as from_account', 'from_account.id','=', 'expenses.from_account')
            -> join('accounts_trees as to_account', 'to_account.id', '=', 'expenses.to_account')
            -> select('expenses.*' , 'from_account.name as from_account_name', 'to_account.name as to_account_name')
            -> orderBy('id', 'DESC')
            -> get();

        if (!empty(Auth::user()->branch_id)) {
            $bills = $bills  -> where('branch_id', Auth::user()->branch_id);
            $settings = AccountSetting::query()->where('branch_id',Auth::user()->branch_id)->first();

            if ($settings) {
                $accountIds = array_filter([$settings->safe_account, $settings->bank_account]);
                $faccounts = AccountsTree::whereIn('id', $accountIds)->get();
            } else {
                $faccounts = AccountsTree::where('parent_code',1101)->orWhere('parent_code',1102)->get();
            }
        } else{
            $faccounts = AccountsTree::where('parent_code',1101)->orWhere('parent_code',1102)->get();
        }  

        $branches = Branch::where('status',1)->get();
       
        $accounts = AccountsTree::all();  

        return view('admin.Expenses.index' , compact( 'bills','accounts','branches','faccounts'));

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
            'docNumber' =>  'required|unique:expenses',
            'date' => 'required', 
            'amount' => 'required',
            'tax_amount' => 'nullable|numeric',
            'from_account' => 'required',
            'to_account' => 'required',
        ]);

        $taxAmount = $request->tax_amount ?? 0;
        $totalWithTax = $request->amount + $taxAmount;

        $id =  Expenses::create([
            'docNumber' => $request -> docNumber,
            'date' => Carbon::parse($request -> date) ,
            'from_account' => $request -> from_account,
            'to_account' => $request -> to_account,
            'client' => $request -> client ?? '',
            'amount' => $request -> amount,
            'tax_amount' => $taxAmount,
            'notes' => $request -> notes ?? '',  
            'payment_type' => $request -> payment_type ?? 0,
            'branch_id' => $request -> branch_id,
            'user_id' => Auth::user() -> id
        ]) -> id   ;

        $auto_accounting =  env("AUTO_ACCOUNTING", 1);
        if($auto_accounting == 1){
            $systemController = new SystemController();
            $systemController -> ExpenseAccounting($id);
        }

        return redirect()->route('expenses')->with('success', __('main.created'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Expenses  $expenses
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $expense = Expenses::find($id);
        echo json_encode($expense);
        exit();

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Expenses  $expenses
     * @return \Illuminate\Http\Response
     */
    public function edit(Expenses $expenses)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Expenses  $expenses
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Expenses $expenses)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Expenses  $expenses
     * @return \Illuminate\Http\Response
     */
    public function destroy(Expenses $expenses)
    {
        //
    } 

    public function get_expense_no($branch_id){
        $bills = Expenses::where('branch_id', $branch_id) 
            ->count();
        if($bills > 0){
            $id = $bills ;
        } else{
            $id = 0 ;
        }
      
        $i = 0;
        do { 
            $i++;
            $prefix = "EXSM-".$branch_id."-";  
            $no = json_encode($prefix . str_pad($id + $i, 6 , '0' , STR_PAD_LEFT)) ;
        } while (Expenses::where("docNumber","=",$prefix . str_pad($id + $i, 6 , '0' , STR_PAD_LEFT))->exists());
        echo $no ;
        exit;
    }


    public function print($id){
        $bill = Expenses::find($id);
        //$valAr = Tafqeet::inArabic($bill -> amount,'sar');
        $valAr = '';
        $company = CompanyInfo::all() -> first();
        return view('admin.Expenses.print' , compact('bill' , 'valAr' , 'company'));
    }
}

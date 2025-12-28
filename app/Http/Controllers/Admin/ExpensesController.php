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
                $faccounts = AccountsTree::whereIn('id', $accountIds)
                    ->when(\Schema::hasColumn('accounts_trees', 'is_active'), function ($q) {
                        $q->where('is_active', 1);
                    })
                    ->get();
            } else {
                $faccounts = AccountsTree::where('parent_code',1101)
                    ->orWhere('parent_code',1102)
                    ->when(\Schema::hasColumn('accounts_trees', 'is_active'), function ($q) {
                        $q->where('is_active', 1);
                    })
                    ->get();
            }
        } else{
            $faccounts = AccountsTree::where('parent_code',1101)
                ->orWhere('parent_code',1102)
                ->when(\Schema::hasColumn('accounts_trees', 'is_active'), function ($q) {
                    $q->where('is_active', 1);
                })
                ->get();
        }  

        $branches = Branch::where('status',1)->get();
       
        $accounts = AccountsTree::query()
            ->when(\Schema::hasColumn('accounts_trees', 'is_active'), function ($q) {
                $q->where('is_active', 1);
            })
            ->get();  

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
            'amount' => 'nullable|numeric|min:0',
            'tax_amount' => 'nullable|numeric',
            'from_account' => 'required',
            'to_account' => 'nullable|integer',
            'detail_account_id' => 'nullable|array',
            'detail_account_id.*' => 'required|integer',
            'detail_amount' => 'nullable|array',
            'detail_amount.*' => 'required|numeric|min:0.01',
            'detail_tax_amount' => 'nullable|array',
            'detail_tax_amount.*' => 'nullable|numeric|min:0',
        ]);

        $detailAccounts = $request->detail_account_id ?? [];
        $detailAmounts = $request->detail_amount ?? [];
        $detailTaxes = $request->detail_tax_amount ?? [];
        $detailNotes = $request->detail_notes ?? [];
        $details = [];
        if (!empty($detailAccounts)) {
            foreach ($detailAccounts as $idx => $accountId) {
                $amount = (float) ($detailAmounts[$idx] ?? 0);
                if ($amount <= 0) {
                    continue;
                }
                $details[] = [
                    'account_id' => (int) $accountId,
                    'amount' => $amount,
                    'tax_amount' => (float) ($detailTaxes[$idx] ?? 0),
                    'notes' => $detailNotes[$idx] ?? null,
                ];
            }
        } elseif ($request->to_account && $request->amount) {
            $details[] = [
                'account_id' => (int) $request->to_account,
                'amount' => (float) $request->amount,
                'tax_amount' => (float) ($request->tax_amount ?? 0),
                'notes' => $request->notes ?? null,
            ];
        }

        if (empty($details)) {
            return redirect()->route('expenses')->with('error', __('main.invoice_details_required'));
        }

        $taxAmount = collect($details)->sum('tax_amount');
        $totalAmount = collect($details)->sum('amount');

        $id =  Expenses::create([
            'docNumber' => $request -> docNumber,
            'date' => Carbon::parse($request -> date) ,
            'from_account' => $request -> from_account,
            'to_account' => $details[0]['account_id'] ?? $request->to_account,
            'client' => $request -> client ?? '',
            'amount' => $totalAmount,
            'tax_amount' => $taxAmount,
            'notes' => $request -> notes ?? '',  
            'payment_type' => $request -> payment_type ?? 0,
            'branch_id' => $request -> branch_id,
            'user_id' => Auth::user() -> id
        ]) -> id   ;

        foreach ($details as $detail) {
            $detail['expense_id'] = $id;
            \App\Models\ExpenseDetail::create($detail);
        }

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

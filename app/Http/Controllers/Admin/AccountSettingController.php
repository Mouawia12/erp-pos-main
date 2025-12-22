<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller; 
use App\Models\AccountSetting;
use App\Http\Requests\StoreAccountSettingRequest;
use App\Http\Requests\UpdateAccountSettingRequest;
use App\Models\AccountsTree;
use App\Models\Warehouse;
use App\Models\Branch;

class AccountSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $accounts = AccountSetting::all();
        $subscriberId = auth()->user()?->subscriber_id;
        
        foreach ($accounts as $account){
            $accountLookup = AccountsTree::withoutGlobalScope('subscriber')
                ->when($subscriberId !== null, function ($q) use ($subscriberId) {
                    $q->where(function ($query) use ($subscriberId) {
                        $query->whereNull('subscriber_id')
                            ->orWhere('subscriber_id', 0)
                            ->orWhere('subscriber_id', $subscriberId);
                    });
                });
            $account->branch_name = optional(Branch::find($account->branch_id))->branch_name;
            $account->safe_account_name = optional((clone $accountLookup)->where('id', $account->safe_account)->first())->name;
            $account->sales_account_name = optional((clone $accountLookup)->where('id', $account->sales_account)->first())->name;
            $account->purchase_account_name = optional((clone $accountLookup)->where('id', $account->purchase_account)->first())->name;
            $account->return_sales_account_name = optional((clone $accountLookup)->where('id', $account->return_sales_account)->first())->name;
            $account->return_purchase_account_name = optional((clone $accountLookup)->where('id', $account->return_purchase_account)->first())->name;
            $account->stock_account_name = optional((clone $accountLookup)->where('id', $account->stock_account)->first())->name;
            $account->sales_discount_account_name = optional((clone $accountLookup)->where('id', $account->sales_discount_account)->first())->name;
            $account->sales_tax_account_name = optional((clone $accountLookup)->where('id', $account->sales_tax_account)->first())->name;
            $account->purchase_discount_account_name = optional((clone $accountLookup)->where('id', $account->purchase_discount_account)->first())->name;
            $account->purchase_tax_account_name = optional((clone $accountLookup)->where('id', $account->purchase_tax_account)->first())->name;
            $account->cost_account_name = optional((clone $accountLookup)->where('id', $account->cost_account)->first())->name;
            $account->profit_account_name = optional((clone $accountLookup)->where('id', $account->profit_account)->first())->name;
            $account->reverse_profit_account_name = optional((clone $accountLookup)->where('id', $account->reverse_profit_account)->first())->name;
        }

        return view('admin.accounts.settings',compact('accounts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $accounts = AccountsTree::query()
            ->where('type','>',1)
            ->when(\Schema::hasColumn('accounts_trees', 'is_active'), function ($q) {
                $q->where('is_active', 1);
            })
            ->get();
        $warehouses = Warehouse::all();

        return view('admin.accounts.create_settings',compact('accounts','warehouses'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreAccountSettingRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAccountSettingRequest $request)
    {
        AccountSetting::create($request->all());
        return redirect()->route('account_settings_list');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AccountSetting  $accountSetting
     * @return \Illuminate\Http\Response
     */
    public function show(AccountSetting $accountSetting)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\AccountSetting  $accountSetting
     * @return \Illuminate\Http\Response
     */
    public function edit(AccountSetting $accountSetting)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateAccountSettingRequest  $request
     * @param  \App\Models\AccountSetting  $accountSetting
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAccountSettingRequest $request, AccountSetting $accountSetting)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AccountSetting  $accountSetting
     * @return \Illuminate\Http\Response
     */
    public function destroy(AccountSetting $accountSetting)
    {
        //
    }

}

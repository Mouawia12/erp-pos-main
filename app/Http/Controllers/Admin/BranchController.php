<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;  
use App\Models\Branch; 
use App\Models\User; 
use App\Models\AccountsTree; 
use App\Models\AccountSetting;
use App\Models\SystemSettings;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class BranchController extends Controller
{
    public function index()
    {
        $subscriberId = Auth::user()->subscriber_id;
        $data = Branch::query()
            ->when($subscriberId, fn ($q) => $q->where('subscriber_id', $subscriberId))
            ->orderByDesc('id')
            ->get();

        return view('admin.branches.index', compact('data'));
    }

    public function create()
    {
        $subscriberId = Auth::user()->subscriber_id;
        $settingsQuery = SystemSettings::query();
        if ($subscriberId) {
            $settingsQuery->where('subscriber_id', $subscriberId);
        }
        $defaultInvoiceType = optional($settingsQuery->first())->default_invoice_type ?? 'simplified_tax_invoice';

        return view('admin.branches.create', compact('defaultInvoiceType'));
    }

    public function store(Request $request)
    {
        $subscriberId = Auth::user()->subscriber_id;

        $subscriberId = Auth::user()->subscriber_id;

        $this->validate($request, [
            'branch_name' => [
                'required',
                Rule::unique('branches', 'branch_name')
                    ->when($subscriberId, fn ($rule) => $rule->where('subscriber_id', $subscriberId)),
            ],
            'branch_phone' => [
                'required',
                Rule::unique('branches', 'branch_phone')
                    ->when($subscriberId, fn ($rule) => $rule->where('subscriber_id', $subscriberId)),
            ],
            'branch_address' => 'required',
            'tax_number' => 'nullable',
            'cr_number' => 'nullable',
            'default_invoice_type' => ['nullable', Rule::in(['tax_invoice','simplified_tax_invoice','non_tax_invoice'])],
        ]);

        $branchesCount = Branch::query()
            ->when($subscriberId, fn ($q) => $q->where('subscriber_id', $subscriberId))
            ->count();

        $settingsQuery = SystemSettings::query();
        if ($subscriberId) {
            $settingsQuery->where('subscriber_id', $subscriberId);
        }
        $settings = $settingsQuery->select('max_branches')->first();
        $maxBranchs = $settings->max_branches ?? null;

        if ($maxBranchs !== null && $branchesCount >= $maxBranchs) {
            return redirect()->back()->with('error', __('main.max_warehouse'));
        }

        DB::beginTransaction();
        try {
            $branch = Branch::create([
                'branch_name' => $request->branch_name,
                'cr_number' => $request->cr_number,
                'tax_number' => $request->tax_number,
                'branch_phone' => $request->branch_phone,
                'branch_address' => $request->branch_address,
                'manager_name' => $request->manager_name,
                'contact_email' => $request->contact_email,
                'default_invoice_type' => $request->default_invoice_type ?? 'simplified_tax_invoice',
                'status' => (int) $request->input('status', 1),
                'subscriber_id' => $subscriberId,
            ]);

            $this->getAccountSetting($branch->id);
            $this->getAccountSetting_private($branch->id);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Failed to create branch', ['error' => $e->getMessage()]);

            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('admin.branches.index')
            ->with('success', 'تم اضافة فرع بنجاح');
    }

    public function show($id)
    {
        $branch = Branch::findorfail($id);
        return view('admin.branches.show', compact('branch'));
    }

    public function edit($id)
    {
        $subscriberId = Auth::user()->subscriber_id;
        $branch = Branch::query()
            ->when($subscriberId, fn ($q) => $q->where('subscriber_id', $subscriberId))
            ->findOrFail($id);

        $settingsQuery = SystemSettings::query();
        if ($subscriberId) {
            $settingsQuery->where('subscriber_id', $subscriberId);
        }
        $defaultInvoiceType = optional($settingsQuery->first())->default_invoice_type ?? 'simplified_tax_invoice';

        return view('admin.branches.edit', compact('branch','defaultInvoiceType'));
    }

    public function update(Request $request, $id)
    {
        $subscriberId = Auth::user()->subscriber_id;

        $this->validate($request, [
            'branch_name' => [
                'required',
                Rule::unique('branches', 'branch_name')
                    ->ignore($id)
                    ->when($subscriberId, fn ($rule) => $rule->where('subscriber_id', $subscriberId)),
            ],
            'branch_phone' => [
                'required',
                Rule::unique('branches', 'branch_phone')
                    ->ignore($id)
                    ->when($subscriberId, fn ($rule) => $rule->where('subscriber_id', $subscriberId)),
            ],
            'branch_address' => 'required',
            'tax_number' => 'nullable',
            'cr_number' => 'nullable',
            'default_invoice_type' => ['nullable', Rule::in(['tax_invoice','simplified_tax_invoice','non_tax_invoice'])],
        ]);
        $input = $request->all();
        if(!isset($input['status'])){
            $input['status'] = 0;
        }
        $branch = Branch::query()
            ->when($subscriberId, fn ($q) => $q->where('subscriber_id', $subscriberId))
            ->findOrFail($id);
        $branch->update($input);
        return redirect()->route('admin.branches.index')
            ->with('success', 'تم تعديل بيانات الفرع بنجاح');
    }

    public function destroy(Request $request)
    { 

       $User = User::findOrFail($request->branch_id); 
 

        if( empty($User)){

            Branch::findOrFail($request->branch_id)->delete();
            return redirect()->route('admin.branches.index')
                ->with('success', 'تم حذف الفرع بنجاح');
        }else{
            return redirect()->route('admin.branches.index')
            ->with('success', 'هذا الفرع مرتبط باعضاء');
        }
   
    }

    public function remove_selected(Request $request)
    {
        $branches_id = $request->branches;
        /*
        foreach ($branches_id as $branch_id) {
            $branch = Branch::FindOrFail($branch_id);
            $branch->delete();
        }
        return redirect()->route('admin.branches.index')
            ->with('success', 'تم الحذف بنجاح');
            */
    }

    public function getAccountSetting($branche_id)
    {
        try {
            $setting = AccountSetting::latest('id')->first();
            $setting_const = AccountSetting::select(
                'warehouse_id',
                'sales_tax_account',
                'purchase_tax_account',
                'profit_account',
                'reverse_profit_account',
                'sales_tax_excise_account'
            )->first();

            if (! $setting || ! $setting_const) {
                return;
            }

            $account_setting = $this->getTableColumns('account_settings');
          
            $account_setting_branch = AccountSetting::create([ 
                'safe_account' => 0,
                'bank_account' => 0,
                'sales_account' => 0,
                'purchase_account' => 0, 
                'return_sales_account' => 0,
                'return_purchase_account' => 0,
                'stock_account' => 0, 
                'sales_discount_account' => 0, 
                'purchase_discount_account' => 0,   
                'cost_account' => 0, 
                'reverse_profit_account' => $setting_const->reverse_profit_account, 
                'profit_account' => $setting_const->profit_account,  
                'sales_tax_account' => $setting_const->sales_tax_account,
                'purchase_tax_account' => $setting_const->purchase_tax_account,  
                'sales_tax_excise_account' => $setting_const->sales_tax_excise_account,  
                'warehouse_id' => 0,
                'branch_id' => $branche_id,
            ]);
    
            for($i = 1; $i < count($account_setting)-10; $i++){
                
                $account_tree = AccountsTree::find($setting[$account_setting[$i]]);
                if($account_tree){
                    $name = str_replace('الرئيسي', '', $account_tree->name);
                    $name = preg_replace('/[0-9]+/', '', $name);

                    $name .= $branche_id; 
                    $sub_accounts = AccountsTree::where('parent_id',$account_tree->id)->get();
        
                    if($sub_accounts->count()>0){ 
                        if (AccountsTree::where('name', $name)->doesntExist()) {
                            $isaaccount = AccountsTree::create([
                                'name' => $name,
                                'code' => $account_tree -> code + 1,
                                'type' => $account_tree -> type,
                                'parent_id' =>$account_tree ->parent_id,
                                'parent_code' => $account_tree -> parent_code,
                                'level' => $account_tree -> level,
                                'list' => $account_tree -> list,
                                'department' => $account_tree -> department,
                                'side' => $account_tree -> side,
                                'is_active' => 1,
                            ]);
        
                            if($account_setting_branch[$account_setting[$i]] == 0){
                                $account_setting_branch[$account_setting[$i]]= $isaaccount ->id;
                                $account_setting_branch->save();
                            }
                        } 
                        
                        $j = 1;
                        foreach($sub_accounts as $sub_account){
                            $name = str_replace('الرئيسي', '', $sub_account->name);
                            $name = preg_replace('/[0-9]+/', '', $name);
                            $name .= $branche_id;
                            $code = $isaaccount -> code;
                            $code .='0'; 
                            $code .= $j;
                            if (AccountsTree::where('name', $name)->doesntExist()) {
                                $issub = AccountsTree::create([
                                    'name' => $name,
                                    'code' => $code,
                                    'type' => $sub_account -> type,
                                    'parent_id' => $isaaccount ->id,
                                    'parent_code' => $isaaccount ->code,
                                    'level' => $sub_account -> level,
                                    'list' => $sub_account -> list,
                                    'department' => $sub_account -> department,
                                    'side' => $sub_account -> side,
                                    'is_active' => 1,
                                ]);
            
                                if($account_setting_branch[$account_setting[$i+$j]] == 0){
                                    $account_setting_branch[$account_setting[$i+$j]]= $issub ->id;
                                    $account_setting_branch->save();
                                }
                            }
        
                            $j++;
                        }
        
                    }else{ 
                        if (AccountsTree::where('name', $name)->doesntExist()) {
                            $isaaccount = AccountsTree::create([
                                'name' => $name,
                                'code' => $account_tree -> code + 1,
                                'type' => $account_tree -> type,
                                'parent_id' =>$account_tree ->parent_id,
                                'parent_code' => $account_tree -> parent_code,
                                'level' => $account_tree -> level,
                                'list' => $account_tree -> list,
                                'department' => $account_tree -> department,
                                'side' => $account_tree -> side,
                                'is_active' => 1,
                            ]);
                            
                            if($account_setting_branch[$account_setting[$i]] == 0){
                                $account_setting_branch[$account_setting[$i]]= $isaaccount ->id;
                                $account_setting_branch->save();
                            }
                        }
                    }
                }            
            }
        } catch (QueryException $ex) {
            return redirect()->route('pos')->with('error', $ex->getMessage());
        }
    }

    public function getAccountSetting_private($branche_id)
    {
        $setting = AccountSetting::where('branch_id', $branche_id)->first();
        $account_tree = AccountsTree::where('parent_code', 52)->latest('id')->first();

        if (! $setting || ! $account_tree) {
            return;
        }

        $account_tree_subs = AccountsTree::where('parent_code','like','%'. $account_tree->code .'%')-> get();
        $name = str_replace('الرئيسي', '', $account_tree->name);
        $name = preg_replace('/[0-9]+/', '', $name);
        $name .= $branche_id;
        if($account_tree){
            if (AccountsTree::where('name', $name)->doesntExist()) {
                $account = AccountsTree::create([
                    'name' => $name,
                    'code' => $account_tree -> code + 1,
                    'type' => $account_tree -> type,
                    'parent_id' => $account_tree -> parent_id,
                    'parent_code' => $account_tree -> parent_code,
                    'level' => $account_tree -> level,
                    'list' => $account_tree -> list,
                    'department' => $account_tree -> department,
                    'side' => $account_tree -> side,
                    'is_active' => 1,
                ]); 
            }
            $j = 1;
            foreach($account_tree_subs as $account_tree_sub){ 
                $name = str_replace('الرئيسي', '', $account_tree_sub->name);
                $name = preg_replace('/[0-9]+/', '', $name);
                $name .= $branche_id;
                if(isset($child)){
                    $code = $child -> code + 1; 
                    $parent_id = $child -> id;
                    $parent_code = $child ->code;
                }else{
                    $code = $account -> code;
                    $code .='0'; 
                    $code .= $j;
                    $parent_id = $account ->id;
                    $parent_code = $account ->code;
                }

                if (AccountsTree::where('name', $name)->doesntExist()) {
                    $child = AccountsTree::create([
                        'name' => $name,
                        'code' => $code,
                        'type' => $account_tree_sub -> type,
                        'parent_id' => $parent_id,
                        'parent_code' => $parent_code,
                        'level' => $account_tree -> level,
                        'list' => $account_tree -> list,
                        'department' => $account_tree -> department,
                        'side' => $account_tree -> side,
                        'is_active' => 1,
                    ]);
                } 
            }
            $j++;

            if(isset($child)){
                $setting->cost_account = $child->id;
                $setting->save();
            }
        }
    }


    public function getTableColumns($table)
    {
        return DB::getSchemaBuilder()->getColumnListing($table);
    
        // OR
    
        //return Schema::getColumnListing($table);
    
    }
 
}

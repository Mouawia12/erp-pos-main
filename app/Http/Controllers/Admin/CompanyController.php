<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller; 
use App\Models\AccountsTree;
use App\Models\Company;
use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Models\CustomerGroup;
use App\Models\SystemSettings;
use App\Models\Representative;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($type)
    {
        $subscriberId = Auth::user()->subscriber_id;

        $companies = Company::with('group')
            ->where('group_id', $type)
            ->when($subscriberId, fn($q) => $q->where('subscriber_id', $subscriberId))
            ->get();

        $groups = CustomerGroup::query()
            ->when($subscriberId, fn($q) => $q->where('subscriber_id', $subscriberId))
            ->get();

        $parentCompanies = Company::query()
            ->where('group_id', $type)
            ->when($subscriberId, fn($q) => $q->where('subscriber_id', $subscriberId))
            ->get();

        $representatives = Representative::query()
            ->when($subscriberId, fn($q) => $q->where('subscriber_id', $subscriberId))
            ->get();

        $parentCode = $type == 3 ? 1107 : 2101;
        $accountsBaseQuery = AccountsTree::withoutGlobalScope('subscriber')
            ->when($subscriberId !== null, function ($q) use ($subscriberId) {
                $q->where(function ($query) use ($subscriberId) {
                    $query->whereNull('subscriber_id')
                        ->orWhere('subscriber_id', 0)
                        ->orWhere('subscriber_id', $subscriberId);
                });
            })
            ->when(Schema::hasColumn('accounts_trees', 'is_active'), function ($q) {
                $q->where('is_active', 1);
            });
        $parentAccount = (clone $accountsBaseQuery)
            ->where('code', $parentCode)
            ->first();
        if ($parentAccount) {
            $descendantIds = collect();
            $queue = collect([$parentAccount->id]);
            while ($queue->isNotEmpty()) {
                $batchIds = $queue->values();
                $queue = collect();
                $children = (clone $accountsBaseQuery)
                    ->whereIn('parent_id', $batchIds)
                    ->get(['id']);
                $childIds = $children->pluck('id');
                $descendantIds = $descendantIds->merge($childIds);
                $queue = $queue->merge($childIds);
            }
            $accounts = (clone $accountsBaseQuery)
                ->whereIn('id', $descendantIds->unique())
                ->orderBy('name')
                ->get();
        } else {
            $accounts = (clone $accountsBaseQuery)
                ->where('parent_code', $parentCode)
                ->orderBy('name')
                ->get();
        }

        $settingsQuery = SystemSettings::query();
        if ($subscriberId && Schema::hasColumn('system_settings', 'subscriber_id')) {
            $settingsQuery->where('subscriber_id', $subscriberId);
        }
        $settings = $settingsQuery->first() ?? SystemSettings::first();

        $viewData = compact('type', 'companies', 'groups', 'accounts', 'settings', 'parentCompanies', 'representatives');

        return $type == 3
            ? view('admin.company.clients', $viewData)
            : view('admin.company.supplier', $viewData);
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
     * @param  \App\Http\Requests\StoreCompanyRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $subscriberId = Auth::user()->subscriber_id;

        if ($request -> id == 0){
            $validated = $request->validate([
                'company' => ['required','string','max:191'],
                'name' => ['required','string','max:191'],
                'opening_balance' => ['required','numeric'],
                'type' => ['required','integer'],
                'customer_group_id' => ['nullable','integer'],
                'cr_number' => ['nullable','string','max:191'],
                'tax_number' => ['nullable','string','max:191'],
                'parent_company_id' => ['nullable','integer'],
                'price_level_id' => ['nullable','integer'],
                'default_discount' => ['nullable','numeric','min:0'],
                'email' => ['nullable','email','max:191'],
                'phone' => ['nullable','string','max:50'],
                'account_id' => ['nullable','integer','exists:accounts_trees,id'],
                'national_address_short' => ['nullable','string','max:191'],
                'national_address_building_no' => ['nullable','string','max:50'],
                'national_address_street' => ['nullable','string','max:191'],
                'national_address_district' => ['nullable','string','max:191'],
                'national_address_city' => ['nullable','string','max:191'],
                'national_address_region' => ['nullable','string','max:191'],
                'national_address_postal_code' => ['nullable','string','max:50'],
                'national_address_additional_no' => ['nullable','string','max:50'],
                'national_address_unit_no' => ['nullable','string','max:50'],
                'national_address_proof_no' => ['nullable','string','max:191'],
                'national_address_proof_issue_date' => ['nullable','date'],
                'national_address_proof_expiry_date' => ['nullable','date'],
            ]);
            try {
                $exists = Company::query()
                    ->where('company', $validated['company'])
                    ->when($subscriberId, fn($q) => $q->where('subscriber_id', $subscriberId));

                if ($exists->doesntExist()) {
                   
                    $company = Company::create([
                        'group_id' => $validated['type'],
                        'group_name' => '',
                        'customer_group_id' => $validated['customer_group_id'] ?? 0 ,
                        'customer_group_name' => '',
                        'name' => $validated['name'] ?? $validated['company'],
                        'company' => $validated['company'],
                        'cr_number' => $validated['cr_number'] ?? null,
                        'tax_number' => $validated['tax_number'] ?? null,
                        'parent_company_id' => $validated['parent_company_id'] ?? null,
                        'price_level_id' => $validated['price_level_id'] ?? null,
                        'default_discount' => $validated['default_discount'] ?? 0,
                        'vat_no' => $request->tax_number ?? $validated['tax_number'] ?? '',
                        'address' => $request-> address ?? '',
                        'city' => '' ,
                        'state' => '',
                        'postal_code' => '',
                        'country' => '',
                        'email' => $validated['email'] ?? '',
                        'phone' => $validated['phone'] ?? '',
                        'invoice_footer' => '',
                        'logo' => '',
                        'award_points' => 0 ,
                        'deposit_amount' => 0 ,
                        'opening_balance' => $request -> opening_balance ?? 0 ,
                        'credit_amount' => $request -> credit_amount ?? 0 ,
                        'stop_sale' =>$request -> has('stop_sale')? 1: 0 ,
                        'account_id' => $request->account_id ?? 0,
                        'user_id' => Auth::user() -> id,
                        'representative_id_' => $request->representative_id_ ?? 0,
                        'subscriber_id' => $subscriberId,
                        'national_address_short' => $request->national_address_short ?? null,
                        'national_address_building_no' => $request->national_address_building_no ?? null,
                        'national_address_street' => $request->national_address_street ?? null,
                        'national_address_district' => $request->national_address_district ?? null,
                        'national_address_city' => $request->national_address_city ?? null,
                        'national_address_region' => $request->national_address_region ?? null,
                        'national_address_postal_code' => $request->national_address_postal_code ?? null,
                        'national_address_additional_no' => $request->national_address_additional_no ?? null,
                        'national_address_unit_no' => $request->national_address_unit_no ?? null,
                        'national_address_proof_no' => $request->national_address_proof_no ?? null,
                        'national_address_proof_issue_date' => $request->national_address_proof_issue_date ?? null,
                        'national_address_proof_expiry_date' => $request->national_address_proof_expiry_date ?? null,
                    ]);
    
                    if (!$company->account_id) {
                        if (!$company->ensureAccount()) {
                            return redirect()->route('clients' , $request -> type)
                                ->with('error', __('main.account_settings') . ': ' . __('validation.required', ['attribute' => __('main.accounting')]));
                        }
                    }
                } else {
                    return redirect()->route('clients' , $request -> type)
                        ->with('error' , __('validation.unique', ['attribute' => __('main.company')]));
                }
                
                return redirect()->route('clients' , $request -> type)->with('success' , __('main.created'));
            } catch(QueryException $ex){ 
                return redirect()->route('clients' , $request -> type)->with('error' ,  $ex->getMessage());
            }

        } else {
            return  $this -> update($request);
        }
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function show(Company $company)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $company = Company::find($id);
        echo json_encode ($company);
        exit;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCompanyRequest  $request
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function update(Request  $request)
    {
        $subscriberId = Auth::user()->subscriber_id;
        $company = Company::query()
            ->when($subscriberId, fn($q) => $q->where('subscriber_id', $subscriberId))
            ->findOrFail($request -> id);
        if($company){
            $validated = $request->validate([
                'company' => 'required',
                'name' => 'required',
                'opening_balance' => 'required', 
                'type' => 'required',
                'account_id' => 'nullable|integer|exists:accounts_trees,id',
                'national_address_short' => ['nullable','string','max:191'],
                'national_address_building_no' => ['nullable','string','max:50'],
                'national_address_street' => ['nullable','string','max:191'],
                'national_address_district' => ['nullable','string','max:191'],
                'national_address_city' => ['nullable','string','max:191'],
                'national_address_region' => ['nullable','string','max:191'],
                'national_address_postal_code' => ['nullable','string','max:50'],
                'national_address_additional_no' => ['nullable','string','max:50'],
                'national_address_unit_no' => ['nullable','string','max:50'],
                'national_address_proof_no' => ['nullable','string','max:191'],
                'national_address_proof_issue_date' => ['nullable','date'],
                'national_address_proof_expiry_date' => ['nullable','date'],
            ]);
            try {
                $company -> update([
                    'group_id' => $request -> type,
                    'group_name' => '',
                    'customer_group_id' => $request -> customer_group_id ? $request -> customer_group_id : $company -> customer_group_id,
                    'customer_group_name' => '',
                    'name' => $request->name ?? $company->name,
                    'company' => $request->company ?? $company->company,
                    'cr_number' => $request->cr_number ?? $company->cr_number,
                    'tax_number' => $request->tax_number ?? $company->tax_number,
                    'vat_no' => $request->tax_number ?? $request->vat_no ?? $company->vat_no,
                    'address' => $request-> address ?? '',
                    'city' => '' ,
                    'state' => '',
                    'postal_code' => '',
                    'country' => '',
                    'email' => $request -> email ? $request -> email : '',
                    'phone' => $request -> phone ? $request -> phone : '',
                    'invoice_footer' => '',
                    'logo' => '',
                    'award_points' => 0 ,
                    'deposit_amount' => 0 ,
                    'opening_balance' =>$request -> opening_balance? $request -> opening_balance: $company ->  opening_balance,
                    'credit_amount' =>$request -> has('credit_amount')? $request -> credit_amount: $company -> credit_amount ,
                    'stop_sale' =>$request -> has('stop_sale')? 1: $company -> stop_sale ,
                    'account_id' => $request->account_id ?? $company->account_id,
                    'parent_company_id' => $request->parent_company_id ?? $company->parent_company_id,
                    'price_level_id' => $request->price_level_id ?? $company->price_level_id,
                    'default_discount' => $request->default_discount ?? $company->default_discount,
                    'representative_id_' => $request->representative_id_ ?? $company->representative_id_,
                    'national_address_short' => $request->national_address_short ?? $company->national_address_short,
                    'national_address_building_no' => $request->national_address_building_no ?? $company->national_address_building_no,
                    'national_address_street' => $request->national_address_street ?? $company->national_address_street,
                    'national_address_district' => $request->national_address_district ?? $company->national_address_district,
                    'national_address_city' => $request->national_address_city ?? $company->national_address_city,
                    'national_address_region' => $request->national_address_region ?? $company->national_address_region,
                    'national_address_postal_code' => $request->national_address_postal_code ?? $company->national_address_postal_code,
                    'national_address_additional_no' => $request->national_address_additional_no ?? $company->national_address_additional_no,
                    'national_address_unit_no' => $request->national_address_unit_no ?? $company->national_address_unit_no,
                    'national_address_proof_no' => $request->national_address_proof_no ?? $company->national_address_proof_no,
                    'national_address_proof_issue_date' => $request->national_address_proof_issue_date ?? $company->national_address_proof_issue_date,
                    'national_address_proof_expiry_date' => $request->national_address_proof_expiry_date ?? $company->national_address_proof_expiry_date,

                ]);

                if (!$company->account_id) {
                    $company->ensureAccount();
                }

                if($company->vat_no > 1 && $company->account_id){
                    $account = AccountsTree::find($company->account_id);
                    if($account){
                        $account->name = $request->company ?? $company->company;
                        $account->save();
                    }
                }

                return redirect()->route('clients' , $request -> type)->with('success', __('main.updated'));

            } catch(QueryException $ex){ 
                return redirect()->route('clients' , $request -> type)->with('error' ,  $ex->getMessage());
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function destroy( $id)
    {
        $subscriberId = Auth::user()->subscriber_id;
        $company = Company::query()
            ->when($subscriberId, fn($q) => $q->where('subscriber_id', $subscriberId))
            ->findOrFail($id);
        if($company){
            $company -> delete();
            return redirect()->route('clients' , $company->group_id)->with('success' , __('main.deleted'));
        }
    }

    public function get_account_code_no($company){ 

        if($company->group_id == 4){ 
            $account = AccountsTree::where('parent_code',2101)->latest('id')->first(); 
            if($account){
                $code = $account->code + 1 ;
            } else{
                $code = 210101;
            }
        } else { 
            $account = AccountsTree::where('parent_code',1107)->latest('id')->first();  
            if($account){
                $code = $account->code + 1 ;
            } else{
                $code = 110701;
            }
                
        }

        return $code;
 
    }
}

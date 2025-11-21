<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Models\CompanyInfo;
use App\Models\CompanyMovement;
use App\Models\CustomerGroup;
use App\Models\Pricing;
use App\Models\Company;
use App\Models\AccountsTree;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($type)
    {
        $companies = Company::where('group_id',$type)->orderBy('id', 'DESC')->get();
        $accounts = AccountsTree::whereIn('parent_code',[2101,1107])->get();
   
        return view('admin.Company.index' , ['type' => $type , 'companies' =>
            $companies , 'accounts' => $accounts ] );
    }

    public function clientAccount($id){
        $client = Company::find($id);
        $company = CompanyInfo::all() -> first();
        $type = $client -> group_id ;
        $movements = CompanyMovement::where('company_id' , '=' , $id) -> get();
        $slag =  $type == 3 ? 5 : 4;
        $subSlag = 4 ; 
        $period = ' ';
        $period_ar = '';
        return view('admin.Company.accountMovement' , compact('type' , 'movements' , 'slag' , 'subSlag' , 'client' , 'company' , 'period' , 'period_ar'));
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

        if ($request -> id == 0){
            $validated = $request->validate([
                'company' => 'required',  
                'opening_balance' => 'required',
                'type' => 'required'
            ]);
            try {
                if (Company::where('company', $request->company)->doesntExist()) {
                    $company = Company::create([
                        'group_id' => $request -> type,
                        'group_name' => '',
                        'customer_group_id' => $request -> customer_group_id ? $request -> customer_group_id : 0 ,
                        'customer_group_name' => '',
                        'name' => $request->company,
                        'company' => $request->company,
                        'vat_no' => $request->vat_no ? $request->vat_no : '',
                        'address' => $request-> address ? $request-> address: '',
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
                        'credit_gold' => 0 ,
                        'deposit_gold' => 0 ,
                        'opening_balance' =>$request -> opening_balance? $request -> opening_balance: 0 ,
                        'credit_amount' =>$request -> has('credit_amount')? $request -> credit_amount: 0 ,
                        'stop_sale' =>$request -> has('stop_sale')? 1: 0 ,
                        'account_id' => 0,
                        'user_id' => Auth::user() -> id 
                    ]);

                    $code = $this->get_account_code_no($company);

                    if($company->group_id == 4){
                        
                        if (AccountsTree::where('parent_code', 2101)
                            ->where('name', $company->company)
                            ->doesntExist()) { 

                            $id = AccountsTree::create([
                                'code' => $code,
                                'name' => $company->company,
                                'type' => 2,
                                'parent_id' => 28,
                                'parent_code' => 2101,
                                'level' => 4,
                                'list' => 2,
                                'department' => 1,
                                'side' => 2
                            ])->id; 

                            $company->account_id = $id;
                            $company->save(); 
                        }

                    }else if($company->group_id == 3){

                        if (AccountsTree::where('parent_code', 1107)
                            ->where('name', $company->company)
                            ->doesntExist()) { 
                                
                            $id = AccountsTree::create([
                                'code' => $code,
                                'name' => $company->company,
                                'type' => 2,
                                'parent_id' => 13,
                                'parent_code' => 1107,
                                'level' => 4,
                                'list' => 1,
                                'department' => 1,
                                'side' => 1
                            ])->id; 

                            $company->account_id = $id;
                            $company->save(); 
                        }


                    }
                }
                

                if(isset($request -> postax)){ 
                    return redirect()->route('pos_tax_create' , $request -> type)->with('success' , __('main.created'));
                }else{
                    return redirect()->route('clients' , $request -> type)->with('success' , __('main.created'));
                }
               
            } catch(QueryException $ex){
                return redirect()->route('clients' , $request -> type)->with('error' ,  $ex->getMessage());
            }
        } else {
         return   $this -> update($request);
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
        $company = Company::find($request -> id);
        if($company){
            $validated = $request->validate([
                'company' => 'required',
                'opening_balance' => 'required',
                'type' => 'required'
            ]);
            try {
                $company -> update([
                    'group_id' => $request -> type,
                    'group_name' => '',
                    'customer_group_id' => $request -> customer_group_id ? $request -> customer_group_id : $company -> customer_group_id,
                    'customer_group_name' => '',
                    'name' => $request->company,
                    'company' => $request->company,
                    'vat_no' => $request->vat_no ,
                    'address' => $request-> address ? $company-> address: '',
                    'city' => '' ,
                    'state' => '',
                    'postal_code' => '',
                    'country' => '',
                    'email' => $request -> email ? $request -> email : '',
                    'phone' => $request -> phone ? $request -> phone : '',
                    'invoice_footer' => '',
                    'logo' => '',
                    'account_id' => $request ->account_id,
                    'award_points' => 0 ,
                    'opening_balance' =>$request -> opening_balance? $request -> opening_balance: $company ->  opening_balance,
                    'stop_sale' =>$request -> has('stop_sale')? 1: $company -> stop_sale ,
                    'user_id' => Auth::user() -> id

                ]);

               
                if($company ->vat_no > 1){
                    $account = AccountsTree::find($company ->account_id);
                    $account ->name =  $request->company;
                    $account -> save();
                }
             

                return redirect()->route('clients' , $request -> type)->with('success' , __('main.updated'));
            }catch(QueryException $ex){ 
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
        $company = Company::find($id);
        if($company){
            $type = $company -> group_id ;
            $company -> delete();
            return redirect()->route('clients' , $type )->with('success' , __('main.deleted'));
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

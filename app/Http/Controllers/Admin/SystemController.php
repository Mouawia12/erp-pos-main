<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller; 
use App\Models\AccountSetting;
use App\Models\AccountsTree;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Company;
use App\Models\Currency;
use App\Models\CustomerGroup;
use App\Models\ExpensesCategory;
use App\Models\Journal;
use App\Models\JournalDetails;
use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Purchase;
use App\Models\Sales;
use App\Models\TaxRates;
use App\Models\Unit;
use App\Models\Warehouse;
use App\Models\WarehouseProducts;
use App\Models\Branch;
use App\Models\Payment;
use App\Models\CatchRecipt;
use App\Models\Expenses; 
use App\Models\SystemSettings;
use Database\Factories\JournalFactory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SystemController extends Controller
{
    public function getAllBrands(){
        return Brand::all();
    }

    public  function getBrandById($id){
        return Brand::find($id);
    }

    public function getAllMainCategories(){

        return Category::where('parent_id',0)->get();
    }

    public function getCategoryById($id){
        return Category::find($id);
    }

    public function getAllSubCategories($id){
        return Category::where('parent_id',$id)->get();
    }

    public function getAllClients(){
        return Company::where('group_id',3)->get();
    }

    public function getClientById($id){
        return Company::find($id);
    }


    public function getAllVendors(){
        return Company::where('group_id',4)->get();
    }

    public function getVendorById($id){
        return Company::find($id);
    }


    public function getAllCurrencies(){
        return Currency::all();
    }

    public function getCurrencyById($id){
        return Currency::find($id);
    }

    public function getAllCustomerGroups(){
        return CustomerGroup::all();
    }

    public function getCustomerGroupById($id){
        return CustomerGroup::find($id);
    }

    public function getAllExpensesCategories(){
        return ExpensesCategory::all();
    }

    public function getExpensesCategoryById($id){
        return ExpensesCategory::find($id);
    }

    public function getAllTaxRates(){
        return TaxRates::orderBy('id', 'desc')->get();
    }

    public function getTaxRateById($id){
        return TaxRates::find($id);
    }

    public function getAllTaxTypes(){
        return [
            [
                'id' => '1',
                'name' => 'Included'
            ],[
                'id'=> '2',
                'name' =>'Excluded'
            ]
        ];
    }

    public function getTaxTypeById($id){
        if($id==1){
            return "Included";
        }else{
            return "Excluded";
        }
    }

    public function getAllUnits(){
        return Unit::all();
    }

    public function getUnitById($id){
        return Unit::find($id);
    }

    public function getAllWarehouses(){
        $query = Warehouse::query();
        $user = Auth::user();

        if(!empty($user?->branch_id)) {
            return $query->where('branch_id', $user->branch_id)->get();
        }

        if(!empty($user?->subscriber_id) && Schema::hasColumn('warehouses','subscriber_id')) {
            $query->where('subscriber_id',$user->subscriber_id);
        }

        return $query->get();
    }
  
    public function getBranches(){
        $user = Auth::user();
        if(!empty($user?->branch_id)) {
            return Branch::where('id', $user->branch_id)->get();
        }

        $query = Branch::query();
        if(!empty($user?->subscriber_id) && Schema::hasColumn('branches','subscriber_id')) {
            $query->where('subscriber_id',$user->subscriber_id);
        }

        return $query->where('status', 1)->get();
    }

    public function getWarehousesBranches($id){  
        return Warehouse::where('branch_id', $id)->get(); 
    }

    public function getWarehouseById($id){
        return Warehouse::find($id);
    }

    public function getProductById($id){
        return Product::find($id);
    }

    public function allowSellingWithoutStock(): bool
    {
        $query = SystemSettings::query();
        $user = Auth::user();
        if ($user && Schema::hasColumn('system_settings', 'subscriber_id')) {
            $query->where('subscriber_id', $user->subscriber_id);
        }

        $settings = $query->first() ?? SystemSettings::first();

        return (int) optional($settings)->sell_without_stock === 2;
    }

    public function syncQnt($items=null,$oldItems=null,$isMinus = true){

        $multy = $isMinus ? -1:1;

        if($items){
            foreach ($items as $item){
                $item->quantity = $item->quantity * $multy;
                $productId = $item->product_id;
                $warehouseId = $item->warehouse_id;
                $product = Product::find($productId);
                $product->update([
                    'quantity' => $product->quantity + $item->quantity
                ]);

                $warehouseProduct = WarehouseProducts::query()
                    ->where('product_id',$productId)
                    ->where('warehouse_id',$warehouseId)
                    ->first();

                if($warehouseProduct){
                    $warehouseProduct->update([
                        'quantity' => $warehouseProduct->quantity + $item->quantity
                    ]);
                }else{
                    //new 01-04-2024
                    WarehouseProducts::create([
                        'warehouse_id' => $warehouseId,
                        'product_id' => $productId,
                        'cost' => $product->cost,
                        'quantity' => $item->quantity
                    ]);
                }

            }
        }

        if($oldItems){
            foreach ($oldItems as $item){

                $item->quantity = $item->quantity * $multy;
                $productId = $item->product_id;
                $warehouseId = $item->warehouse_id;

                $product = Product::find($productId);
                $product->update([
                    'quantity' => $product->quantity - $item->quantity
                ]);

                $warehouseProduct = WarehouseProducts::query()
                    ->where('product_id',$productId)
                    ->where('warehouse_id',$warehouseId)
                    ->get()->first();


                $warehouseProduct->update([
                    'quantity' => $warehouseProduct->quantity - $item->quantity
                ]);

            }
        }

    }

    //region Journals

    public function saleJournals($id){
        $saleInvoice = Sales::find($id);
        if($saleInvoice->net < 0){
            return $this->returnSaleJournal($id);
        }

        //$settings = AccountSetting::query()->where('warehouse_id',$saleInvoice->warehouse_id)->first();
        $settings = AccountSetting::query()->where('branch_id',$saleInvoice->branch_id)->first();
        if(!$settings){
            return;
        }
            
        $headerData = [
            'branch_id' => $saleInvoice->branch_id,
            'date' => $saleInvoice->date,
            'basedon_no' => $saleInvoice->invoice_no,
            'basedon_id' => $id,
            'baseon_text' => 'فاتورة مبيعات',
            'total_debit' => 0,
            'total_credit' => 0, 
            'notes' => ''
        ];
        //journal details
        $detailsData = [];

        //credit for details
        //حساب الصندوق - الخصم
        if($saleInvoice->discount > 0){
            $detailsData[] = [
                'account_id' => $settings->sales_discount_account,
                'debit' => $saleInvoice->discount,
                'credit' => 0, 
                'ledger_id' => 0,
                'notes' => ''
            ];
        }

        if($saleInvoice->net > 0){

            $remain = $saleInvoice->net;

            if($remain > 0){
                $customerAccount = $this->getClientById($saleInvoice->customer_id)->account_id;
                $detailsData[] = [
                    'account_id' => $customerAccount,
                    'debit' => $remain,
                    'credit' => 0, 
                    'ledger_id' => $saleInvoice->customer_id,
                    'notes' => ''
                ];
            }
        }
        //debit for details
        //الضريبة - المبيعات
        if($saleInvoice->total > 0){
            $detailsData[] = [
                'account_id' => $settings->sales_account,
                'debit' => 0,
                'credit' => $saleInvoice->total,
                'ledger_id' => 0,
                'notes' => ''
            ];
        }

        if($saleInvoice->tax > 0){
            $detailsData[] = [
                'account_id' => $settings->sales_tax_account,
                'debit' => 0,
                'credit' => $saleInvoice->tax,
                'ledger_id' => 0,
                'notes' => ''
            ];
        }

        if($saleInvoice->tax_excise > 0){
            $detailsData[] = [
                'account_id' => $settings->sales_tax_excise_account,
                'debit' => 0,
                'credit' => $saleInvoice->tax_excise,
                'ledger_id' => 0,
                'notes' => ''
            ];
        }

        if($saleInvoice->total > 0 && $settings->profit_account > 0 && $settings->cost_account > 0){

            // هيدخل هنا في التكلفة وفي الارباح
            $detailsData[] = [
                'account_id' => $settings->profit_account,
                'debit' => $saleInvoice->profit,
                'credit' => 0, 
                'ledger_id' => 0,
                'notes' => ''
            ];

            if($settings->reverse_profit_account > 0){
                $detailsData[] = [
                    'account_id' => $settings->reverse_profit_account,
                    'debit' => 0,
                    'credit' => $saleInvoice->profit,
                    'ledger_id' => 0,
                    'notes' => ''
                ];
            }

            $detailsData[] = [
                'account_id' => $settings->cost_account,
                'debit' => $saleInvoice->total - $saleInvoice->profit,
                'credit' => 0, 
                'ledger_id' => 0,
                'notes' => ''
            ];

            $detailsData[] = [
                'account_id' => $settings->stock_account,
                'debit' => 0,
                'credit' => $saleInvoice->total - $saleInvoice->profit,
                'ledger_id' => 0,
                'notes' => ''
            ];

        }

        $this->insertJournal($headerData,$detailsData);
    }

    private function returnSaleJournal($id){

        $saleInvoice = Sales::find($id); 
        //$settings = AccountSetting::query()->where('warehouse_id',$saleInvoice->warehouse_id)->get()->first();
        $settings = AccountSetting::query()->where('branch_id',$saleInvoice->branch_id)->first();
        if(!$settings){
            return;
        }
            
        //journal header
        $headerData = [
            'branch_id' => $saleInvoice->branch_id,
            'date' => $saleInvoice->date,
            'basedon_no' => $saleInvoice->invoice_no,
            'basedon_id' => $id,
            'baseon_text' => 'مرتجع مبيعات',
            'total_debit' => 0,
            'total_credit' => 0, 
            'notes' => ''
        ];
        //journal details
        $detailsData = [];

        //credit for details
        //حساب الصندوق - الخصم
        if($saleInvoice->discount <> 0){
            $detailsData[] = [
                'account_id' => $settings->sales_discount_account,
                'debit' => 0,
                'credit' => $saleInvoice->discount*-1,
                'ledger_id' => 0,
                'notes' => ''
            ];
        }

        if($saleInvoice->net <> 0){

            $customerAccount = $this->getClientById($saleInvoice->customer_id)->account_id;
            $detailsData[] = [
                'account_id' => $customerAccount,
                'debit' => 0,
                'credit' => $saleInvoice->net * -1,
                'ledger_id' => $saleInvoice->customer_id,
                'notes' => ''
            ]; 
        }
        //debit for details
        //الضريبة - المبيعات
        if($saleInvoice->total <>0){
            $detailsData[] = [
                'account_id' => $settings->return_sales_account,
                'debit' => $saleInvoice->total*-1,
                'credit' => 0, 
                'ledger_id' => 0,
                'notes' => ''
            ];
        }

        if($saleInvoice->tax <>0){
            $detailsData[] = [
                'account_id' => $settings->sales_tax_account,
                'debit' => $saleInvoice->tax*-1,
                'credit' => 0, 
                'ledger_id' => 0,
                'notes' => ''
            ];
        }

        if($saleInvoice->tax_excise <> 0){
            $detailsData[] = [
                'account_id' => $settings->sales_tax_excise_account,
                'debit' => $saleInvoice->tax_excise*-1,
                'credit' => 0,
                'ledger_id' => 0,
                'notes' => ''
            ];
        }

        if($saleInvoice->total <> 0 && $settings->profit_account > 0 && $settings->cost_account > 0){

            // هيدخل هنا في التكلفة وفي الارباح
            $detailsData[] = [
                'account_id' => $settings->profit_account, 
                'debit' => 0,
                'credit' => $saleInvoice->profit * -1,
                'ledger_id' => 0,
                'notes' => ''
            ];
        
            if($settings->reverse_profit_account > 0){
                $detailsData[] = [ 
                    'account_id' => $settings->reverse_profit_account,
                    'debit' => $saleInvoice->profit * -1,
                    'credit' => 0, 
                    'ledger_id' => 0,
                    'notes' => ''
                ];
            }
        
            $detailsData[] = [ 
                'account_id' => $settings->cost_account,
                'debit' => 0,
                'credit' => ($saleInvoice->total - $saleInvoice->profit)*-1,
                'ledger_id' => 0,
                'notes' => ''
            ];
        
            $detailsData[] = [ 
                'account_id' => $settings->stock_account,
                'debit' => ($saleInvoice->total - $saleInvoice->profit) *-1,
                'credit' => 0, 
                'ledger_id' => 0,
                'notes' => ''
            ];
     
        }

        $this->insertJournal($headerData,$detailsData);
    }


    public function purchaseJournals($id){

        $purchaseInvoice = Purchase::find($id);
        if($purchaseInvoice->net < 0){
            return $this->returnPurchaseJournals($id);
        }
           
        //$settings = AccountSetting::query()->where('warehouse_id',$purchaseInvoice->warehouse_id)->get()->first();
        $settings = AccountSetting::query()->where('branch_id',$purchaseInvoice->branch_id)->first();
        if(!$settings){
            return;
        } 

        $headerData = [
            'branch_id' => $purchaseInvoice->branch_id,
            'date' => $purchaseInvoice->date,
            'basedon_no' => $purchaseInvoice->invoice_no,
            'basedon_id' => $id,
            'baseon_text' => 'فاتورة مشتريات',
            'total_debit' => 0,
            'total_credit' => 0, 
            'notes' => ''
        ];

        $detailsData = [];

        //credit for details
        //حساب الصندوق - الخصم
        if($purchaseInvoice->discount > 0){
            $detailsData[] = [
                'account_id' => $settings->purchase_discount_account,
                'debit' => 0,
                'credit' => $purchaseInvoice->discount,
                'ledger_id' => 0,
                'notes' => ''
            ];
        }

        ////log_message('error','F6 :'.$id);

        if($purchaseInvoice->net > 0){
            $customerAccount = $this->getClientById($purchaseInvoice->customer_id)->account_id;;
            $detailsData[] = [
                'account_id' => $customerAccount,
                'debit' => 0,
                'credit' => $purchaseInvoice->net,
                'ledger_id' => $purchaseInvoice->customer_id,
                'notes' => ''
            ];
        }

        ////log_message('error','F7 :'.$id);
        //debit for details  
        if($purchaseInvoice->total > 0){
            $detailsData[] = [
                'account_id' => $settings->purchase_account,
                'debit' => $purchaseInvoice->total,
                'credit' => 0, 
                'ledger_id' => 0,
                'notes' => ''
            ];
            
            $detailsData[] = [
                'account_id' => $settings->stock_account,
                'debit' => $purchaseInvoice->total,
                'credit' => 0, 
                'ledger_id' => 0,
                'notes' => ''
            ];
            
            $detailsData[] = [
                'account_id' => $settings->cost_account,
                'debit' => 0,
                'credit' => $purchaseInvoice->total,
                'ledger_id' => 0,
                'notes' => ''
            ];
             
        }

        if($purchaseInvoice->tax > 0){
            $detailsData[] = [
                'account_id' => $settings->purchase_tax_account,
                'debit' => $purchaseInvoice->tax,
                'credit' => 0, 
                'ledger_id' => 0,
                'notes' => ''
            ];
        }

        $this->insertJournal($headerData,$detailsData);
    }

    public function returnPurchaseJournals($id){

        $purchaseInvoice = Purchase::find($id);
        //$settings = AccountSetting::query()->where('warehouse_id',$purchaseInvoice->warehouse_id)->first();
        $settings = AccountSetting::query()->where('branch_id',$purchaseInvoice->branch_id)->first();
       
        if(!$settings){
            return;
        }
            
        //journal header
        $headerData = [
            'branch_id' => $purchaseInvoice->branch_id,
            'date' => $purchaseInvoice->date,
            'basedon_no' => $purchaseInvoice->invoice_no,
            'basedon_id' => $id,
            'baseon_text' => 'مردود مشتريات',
            'total_credit' => 0,
            'total_debit' => 0,
            'notes' => ''
        ];
        //journal details
        $detailsData = [];

        //credit for details
        //حساب الصندوق - الخصم
        if($purchaseInvoice->order_discount < 0){
            $detailsData[] = [
                'account_id' => $settings->purchase_discount_account,
                'debit' => 0,
                'credit' => $purchaseInvoice->order_discount*-1,
                'ledger_id' => 0,
                'notes' => ''
            ];
        }

        if($purchaseInvoice->net < 0){ 
                $customerAccount = $this->getClientById($purchaseInvoice->customer_id)->account_id;
                $detailsData[] = [
                    'account_id' => $customerAccount,
                    'debit' => $purchaseInvoice->net *-1,
                    'credit' => 0, 
                    'ledger_id' => $purchaseInvoice->customer_id,
                    'notes' => ''
                ];
        }
        //debit for details 
        if($purchaseInvoice->total < 0){
            $detailsData[] = [
                'account_id' => $settings->return_purchase_account,
                'debit' => 0,
                'credit' => $purchaseInvoice->total*-1,
                'ledger_id' => 0,
                'notes' => ''
            ];

            $detailsData[] = [
                'account_id' => $settings->stock_account,
                'debit' => 0,
                'credit' => $purchaseInvoice->total*-1,
                'ledger_id' => 0,
                'notes' => ''
            ];

            $detailsData[] = [
                'account_id' => $settings->cost_account,
                'debit' => $purchaseInvoice->total*-1,
                'credit' => 0,  
                'ledger_id' => 0,
                'notes' => ''
            ];
        }

        if($purchaseInvoice->tax < 0){
            $detailsData[] = [
                'account_id' => $settings->purchase_tax_account,
                'debit' => 0,
                'credit' => $purchaseInvoice->tax*-1,
                'ledger_id' => 0,
                'notes' => ''
            ];
        }

        $this->insertJournal($headerData,$detailsData);
    }

    public function EnterMoneyAccounting($id){
        $bill = Payment::find($id);
        if($bill){ 
            $settings = AccountSetting::query()->where('branch_id',$bill->branch_id)->first(); 
            if (!$settings){
                return;
            } 

            if($bill->paid_by == 'cash'){
                $accountId = $settings->safe_account;
                $baseonText = 'مستند قبض';
            }else{
                $accountId = $settings->bank_account; 
                $baseonText = 'تحويل بنكي فيزا';
            }                   
            //journal header
            $headerData = [
                'branch_id' => $bill->branch_id,
                'date' => $bill->date,
                'basedon_no' => $bill->doc_number,
                'basedon_id' => $id,
                'baseon_text' => $baseonText,
                'total_credit' => 0,
                'total_debit' => 0,
                'notes' => ''
            ];

            $detailsData = []; 
            $customerAccount = Company::find($bill->company_id)->account_id;
             
            //حساب العميل  نقدا - الي حساب الصندوق نقدا
            $detailsData[] = [
                'account_id' => $accountId,
                'debit' => $bill -> amount ,
                'credit' => 0,
                'ledger_id' => 0,
                'notes' => ''
            ];

            $detailsData[] = [
                'account_id' => $customerAccount,
                'debit' => 0,
                'credit' => $bill -> amount,
                'ledger_id' => 0,
                'notes' => ''
            ];

            $this->insertJournal($headerData, $detailsData);
        }
    }

    public function ExitMoneyAccounting($id){
        $bill = Payment::find($id);
        if($bill){
            //$settings = AccountSetting::first();
            $settings = AccountSetting::where('branch_id',$bill->branch_id)->first();
           
            if (!$settings){
                return;
            }
                
            if($bill->paid_by == 'cash'){
                $accountId = $settings->safe_account;
                $baseonText = 'مستند صرف';
            }else{
                $accountId = $settings->bank_account; 
                $baseonText = 'تحويل بنكي فيزا';
            }                   
            //journal header
            $headerData = [
                'branch_id' => $bill->branch_id,
                'date' => $bill->date,
                'basedon_no' => $bill->doc_number,
                'basedon_id' => $id,
                'baseon_text' => $baseonText,
                'total_debit' => 0,
                'total_credit' => 0, 
                'notes' => ''
            ];

            $detailsData = []; 
            $customerAccount = Company::find($bill->company_id)->account_id;
             
            //من حساب الصندوق او البنك - الى حساب المورد 
            $detailsData[] = [
                'account_id' => $customerAccount,
                'debit' => $bill -> amount,
                'credit' => 0,
                'ledger_id' => 0,
                'notes' => ''
            ];

            $detailsData[] = [
                'account_id' => $accountId,
                'debit' => 0,
                'credit' => $bill -> amount,
                'ledger_id' => 0,
                'notes' => ''
            ];

            $this->insertJournal($headerData, $detailsData);
        }
    }

    public function ExpenseAccounting($id){
        $bill = Expenses::find($id);
        if ($bill) {
            $settings = AccountSetting::where('branch_id',$bill->branch_id)->first();
            if (!$settings)
                return;

            //journal header
            $headerData = [
                'branch_id' => $bill->branch_id,
                'date' => $bill->date,
                'basedon_no' => $bill->docNumber,
                'basedon_id' => $id,
                'baseon_text' => 'سند صرف',
                'total_credit' => 0,
                'total_debit' => 0,
                'notes' => ''
            ];

            $detailsData = [];

            $from_account = AccountsTree::find($bill->from_account)->id;
            $to_account = AccountsTree::find($bill->to_account)->id;
            $taxAccount = $settings->purchase_tax_account ?? $settings->sales_tax_account ?? null;
            $taxAmount = $bill->tax_amount ?? 0;
            $totalOut = $bill->amount + $taxAmount;

            $detailsData[] = [
                'account_id' => $from_account,
                'debit' =>  0,
                'credit' => $totalOut,
                'ledger_id' => 0,
                'notes' => ''
            ];
            $detailsData[] = [
                'account_id' => $to_account,
                'debit' => $bill -> amount,
                'credit' => 0,
                'ledger_id' => 0,
                'notes' => ''
            ];
            if($taxAccount && $taxAmount > 0){
                $detailsData[] = [
                    'account_id' => $taxAccount,
                    'debit' => $taxAmount,
                    'credit' => 0,
                    'ledger_id' => 0,
                    'notes' => 'ضريبة مصروف'
                ];
            }
            $this->insertJournal($headerData, $detailsData);

        }
    }

    public function CatchAccounting($id){
        $bill = CatchRecipt::find($id);
        if ($bill) {
            $settings = AccountSetting::where('branch_id',$bill->branch_id)->first();
            if (!$settings)
                return;

            //journal header
            $headerData = [
                'branch_id' => $bill->branch_id,
                'date' => $bill->date,
                'basedon_no' => $bill->docNumber,
                'basedon_id' => $id,
                'baseon_text' => 'سند قبض',
                'total_credit' => 0,
                'total_debit' => 0,
                'notes' => ''
            ];

            $detailsData = [];

            $from_account = AccountsTree::find($bill->from_account)->id;
            $to_account = AccountsTree::find($bill->to_account)->id;

            $detailsData[] = [
                'account_id' => $from_account,
                'credit' =>  0,
                'debit' => $bill -> amount,
                'ledger_id' => 0,
                'notes' => ''
            ];
            $detailsData[] = [
                'account_id' => $to_account,
                'credit' => $bill -> amount,
                'debit' => 0,
                'ledger_id' => 0,
                'notes' => ''
            ];
            $this->insertJournal($headerData, $detailsData);

        }
    }

    public function getJournal($data){

        $data = Journal::query()
            ->where('basedon_no',$data['basedon_no'])
            ->where('basedon_id',$data['basedon_id'])
            ->where('baseon_text',$data['baseon_text'])->get()->first();

        if($data){
            return $data->id;
        }

        return 0;
    }

    private function getOldDetails($id){
        return JournalDetails::query()->where('journal_id',$id)->get();
    }

    public function insertJournal($header,$details,$manual = 0){

        if($id = $this->getJournal($header)){
            dd($id);
            $journal = Journal::find($id);
            $journal->update($header);

            $oldDetails = $this->getOldDetails($id);
            ////log_message('error',$id);
            foreach($oldDetails as $oldDetail){
                $this->updateAccountBalance($oldDetail->account_id,-1*$oldDetail->credit,-1*$oldDetail->debit,$header['date'],$id);
            }

            DB::table('journal_details')
                ->where('journal_id' ,$id)
                ->delete();

            DB::table('account_movements')
                ->where('journal_id' ,$id)
                ->delete();


            foreach($details as $detail){
                $detail['journal_id'] = $id;

                DB::table('journal_details')
                    ->insert($detail);

                $this->updateAccountBalance($detail['account_id'],$detail['credit'],$detail['debit'],$header['date'],$id);
            }

            return true;
        }else{
            $journal_id = DB::table('journals')
                ->insertGetId($header);
            if ($journal_id) {

                foreach($details as $detail){
                    $detail['journal_id'] = $journal_id;

                    DB::table('journal_details')
                        ->insert($detail);

                    $this->updateAccountBalance($detail['account_id'],$detail['credit'],$detail['debit'],$header['date'],$journal_id);
                }

                if($manual  == 1){
                    $journal  = Journal::find($journal_id);
                    $journal->update(['baseon_text' => 'سند قيد يدوي رقم '.$journal_id]);
                }
            }
            return true;
        }

        return false;

    }


    private function updateAccountBalance($id,$credit,$debit,$date,$journalId){
        $account = $this->getAccountById($id);

        if(!$account){
            return;
        } 

        if($credit <> 0 || $debit <> 0){
            $accountMData = [
                'journal_id' => $journalId,
                'account_id' => $id,
                'credit'     => $credit,
                'debit'      => $debit,
                'date'       => $date
            ];

            DB::table('account_movements')->insert($accountMData);
        }

        if($account->parent_id > 0){
            $this->updateAccountBalance($account->parent_id,$credit,$debit,$date,$journalId);
        }

    }

    private function getAccountById($id){
        if(!$id){
            $id = 0;
        }

        return AccountsTree::find($id);
    }

    private function getJournalForDelete($data){

        $data = Journal::query()
            ->where('basedon_id',$data['basedon_id'])
            ->where('baseon_text',$data['baseon_text'])->get()->first();

        if($data){
            return $data->id;
        }
        return 0;
    }

    public function deleteJournal($header){

        if($id = $this->getJournalForDelete($header)){

            $oldDetails = $this->getOldDetails($id);
            foreach($oldDetails as $oldDetail){
                $this->updateAccountBalance($oldDetail->account_id,-1*$oldDetail->credit,-1*$oldDetail->debit
                    ,$header['date'],$id);
            }

            DB::table('journal_details')
                ->where('journal_id' ,$id)
                ->delete();

            DB::table('account_movements')
                ->where('journal_id' ,$id)
                ->delete();

            DB::table('journals')
                ->where('id' ,$id)
                ->delete();

            return true;
        }
    }

    //endregion
}

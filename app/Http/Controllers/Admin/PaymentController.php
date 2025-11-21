<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller; 
use App\Models\Payment;
use App\Http\Requests\StorePaymentRequest;
use App\Http\Requests\UpdatePaymentRequest;
use App\Models\Purchase;
use App\Models\Sales;
use App\Models\SystemSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DataTables;

class PaymentController extends Controller
{
    public function getSalesPayments($id){
        $payments = Payment::where('sale_id',$id)
            ->where('sale_id','<>',null)->get();
        $setting = SystemSettings::with('currency') -> get()-> first() ;
        $html = view('admin.sales.payments',compact('payments' , 'setting'))->render();
        return $html;
    }

    public function addSalePayment($id){
        $sale = Sales::find($id);

        if($sale->net < 0){
            $sale->net = $sale->net*-1;
            $sale->paid = $sale->paid*-1;
        }

        $remain = $sale->net - $sale->paid;

        if($sale -> pos == 0 ){
            $html = view('admin.sales.add_payment',compact('remain','id'))->render(); 
        }else{
            $html = view('admin.sales.add_payment_pos',compact('remain','id'))->render();
        }
        
        return $html;
    }

    public function showSalePayment($remain){ 

        $html = view('admin.sales.add_payment',compact('remain'))->render(); 
        
        return $html;
    }

    public function storeSalePayment(StorePaymentRequest $request, $id){

        $sale = Sales::find($id);
        $amount = $request->amount;
        $net = $sale->net;

        if($sale->net < 0){
            $amount = $amount*-1;
            $net = $net*-1;
        }

        $payment = Payment::create([
            'date' => $request->date,
            'branch_id' => $sale-> branch_id,
            'purchase_id' => null,
            'sale_id' => $id,
            'company_id' => $sale->customer_id,
            'amount' => $amount,
            'paid_by' => $request->paid_by,
            'remain' => $net - $request->amount,
            'user_id' => Auth::user() ? Auth::id() : 0
        ]);


        $paid = $sale->paid + $amount;
        $sale->update([
            'paid' => $paid
        ]);

        $clientController = new ClientMoneyController();
        $clientController->syncMoney($sale->customer_id,0,$request->amount);

        $vendorMovementController = new VendorMovementController();
        $vendorMovementController->addSalePaymentMovement($payment->id);

        if($sale -> pos == 0 ){
            return redirect()->route('sales');
        } else {
            return redirect()->route('pos');
        }

    }

   
    public function MakeSalePayment(Request $request, $id){
    //public function MakeSalePayment(StorePaymentRequest $request, $id){   

        if($request -> cash > 0){
            $this -> MakePayment($request -> cash,'cash', $id);
        }

        if($request -> visa > 0){
            $this -> MakePayment($request -> visa, 'visa', $id);
        }

        if($request -> pos == 0 ){
            return redirect()->route('sales');
        } else {
            return redirect()->route('pos');
        }
       
    }

    public function MakePayment($money, $paid_by, $id){
        $sale = Sales::find($id);
        $amount = $money;
        $net = $sale->net;

        if($sale->net < 0){
            $amount = $amount*-1;
            $net = $net*-1;
        }

        $bill_number = $this -> getpaymentNo($sale-> branch_id);
        $payment = Payment::create([
            'date' => date("Y-m-d"),
            'doc_number' => $bill_number,
            'branch_id' => $sale-> branch_id,
            'purchase_id' => null,
            'sale_id' => $id,
            'company_id' => $sale->customer_id,
            'amount' => $amount,
            'paid_by' => $paid_by,
            'remain' => $net - $money,
            'based_on_bill_number'=>$sale-> invoice_no,
            'user_id' => Auth::user() -> id
        ]);

        $paid = $sale->paid + $amount;
        $sale->update([
            'paid' => $paid
        ]);

        $clientController = new ClientMoneyController();
        $clientController->syncMoney($sale->customer_id,0,$money);

        $vendorMovementController = new VendorMovementController();
        $vendorMovementController->addSalePaymentMovement($payment->id);

        $systemController = new SystemController();
        $systemController -> EnterMoneyAccounting($payment->id);
     
    }

    public function deleteSalePayment($id){
        $payment = Payment::find($id);
        $sale = Sales::find($payment->sale_id);

        Payment::destroy($id);

        $paid = $sale->paid - $payment->amount;

        $sale->update([
            'paid' => $paid
        ]);

        $clientController = new ClientMoneyController();
        $clientController->syncMoney($sale->customer_id,0,$payment->amount * -1);

        $vendorMovementController = new VendorMovementController();
        $vendorMovementController->removeSalePaymentMovement($id);

        return redirect()->route('sales');
    }


    public function getPurchasesPayments($id){
        $payments = Payment::where('purchase_id',$id)
            ->where('purchase_id','<>',null)->get();
        $setting = SystemSettings::with('currency') -> get()-> first() ;
        $html = view('admin.purchases.payments',compact('payments' , 'setting'))->render();
        return $html;
    }

    public function addPurchasesPayment($id){
        $sale = Purchase::find($id);

        if($sale->net < 0){
            $sale->net = $sale->net*-1;
            $sale->paid = $sale->paid*-1;
        }

        $remain = $sale->net - $sale->paid;

        $html = view('admin.purchases.add_payment',compact('remain','id'))->render();
        return $html;
    }

    public function storePurchasesPayment(StorePaymentRequest $request, $id){

        $purchase = Purchase::find($id);
        $amount = $request->amount;
        $net = $purchase->net;

        if($purchase->net < 0){
            $amount = $amount*-1;
            $net = $net*-1;
        }
        
        $bill_number = $this -> getpaymentNo($purchase-> branch_id);
        $payment = Payment::create([
            'date' => date("Y-m-d"),
            'doc_number' => $bill_number,
            'branch_id' => $purchase-> branch_id,
            'purchase_id' => $id,
            'sale_id' => null,
            'company_id' => $purchase->customer_id,
            'amount' => $amount,
            'paid_by' => $request->paid_by,
            'remain' => $net - $request->amount,
            'based_on_bill_number'=>$purchase-> invoice_no,
            'user_id' => Auth::user() -> id
        ]);

        $paid = $purchase->paid + $amount;
        $purchase->update([
            'paid' => $paid
        ]);

        $clientController = new ClientMoneyController();
        $clientController->syncMoney($purchase->customer_id,0, $request->amount * -1);

        $vendorMovementController = new VendorMovementController();
        $vendorMovementController->addPurchasePaymentMovement($payment->id); 

        $systemController = new SystemController();
        $systemController -> ExitMoneyAccounting($payment->id);

        return redirect()->route('purchases');
    }

    public function deletePurchasesPayment($id){
        $payment = Payment::find($id);
        $sale = Purchase::find($payment->purchase_id);

        Payment::destroy($id);

        $paid = $sale->paid - $payment->amount;

        $sale->update([
            'paid' => $paid
        ]);

        $clientController = new ClientMoneyController();
        $clientController->syncMoney($sale->customer_id,0,$payment->amount );

        $vendorMovementController = new VendorMovementController();
        $vendorMovementController->removePurchasePaymentMovement($id);

        return redirect()->route('purchases');
    }

    public function deleteAllPurchasePayments($id){

        $sale = Purchase::find($id);
        $payments = Payment::query()->where('purchase_id',$id);

        foreach ($payments as $payment){
            Payment::destroy($payment->id);

            $paid = $sale->paid - $payment->amount;

            $sale->update([
                'paid' => $paid
            ]);

            $clientController = new ClientMoneyController();
            $clientController->syncMoney($sale->customer_id,0,$payment->amount * -1);

            $vendorMovementController = new VendorMovementController();
            $vendorMovementController->removePurchasePaymentMovement($payment->id);
        }


    }

    public function getpaymentNo($branch_id){ 
        $bills = Payment::where('branch_id', $branch_id)  
            ->count(); 
            
        if($bills > 0){
            $id = $bills ;
        } else{
            $id = 0 ;
        }       
        $prefix = "ME-".$branch_id."-";
        $no = ($prefix . str_pad($id + 1, 6 , '0' , STR_PAD_LEFT)) ;
        return $no ;
    }

    public function money_entry_list(Request $request){

        $data = Payment::join('companies', 'payments.company_id','=', 'companies.id')
        ->join('branches', 'payments.branch_id','=', 'branches.id')
        ->join('sales', 'payments.sale_id', 'sales.id')   
        ->whereColumn('sales.branch_id','=','payments.branch_id')
        ->select('payments.*', 'companies.name as vendor_name', 'sales.invoice_no as invoice_number','branches.branch_name')
        ->orderBy('id', 'DESC')
        ->get();

        if (!empty(Auth::user()->branch_id)) { 
            $data = $data->where('branch_id', Auth::user()->branch_id);
        }  
    
        if ($request->ajax()) { 
    
            return Datatables::of($data)->addIndexColumn()
                ->addColumn('payment_method', function($row){
    
                    if($row->paid_by == 'cash'){
                        $span = 'كاش (نقدي)';
                    }else{
                        $span = 'فيزا (صراف)';
                    }
    
                    return $span; 
                }) 
                ->addColumn('based_on', function($row){ 
                    if(auth()->user()->can('عرض مبيعات')){  
                        $a = '<a href='.route('print.sales',$row->sale_id).' target="_blank">
                                   '.$row->based_on_bill_number.'</a>';
                    } 
                    return $a; 
                }) 
                ->addColumn('action', function($row){
                    if(auth()->user()->can('عرض مبيعات')){   
                        $btn = '<button type="button" class="btn btn-labeled btn-info preview"
                                   value="'.$row->id.'"><i class="fa fa-eye"></i>معاينة
                                </button>';
                    }
                   
                    return $btn; 
                }) 
                ->rawColumns(['payment_method','based_on','action']) 
                ->make(true);
        } 
    
        return view('admin.Money.Enter.index');

    }

    public function money_exit_list(Request $request){

        $data = Payment::join('companies', 'payments.company_id','=', 'companies.id')
        ->join('branches', 'payments.branch_id','=', 'branches.id')
        ->join('purchases', 'payments.purchase_id', 'purchases.id')   
        ->whereColumn('purchases.branch_id','=','payments.branch_id')
        ->select('payments.*', 'companies.name as vendor_name', 'purchases.invoice_no as invoice_number','branches.branch_name')
        ->orderBy('id', 'DESC')
        ->get();

        if (!empty(Auth::user()->branch_id)) { 
            $data = $data->where('branch_id', Auth::user()->branch_id);
        }  
    
        if ($request->ajax()) { 
    
            return Datatables::of($data)->addIndexColumn()
                ->addColumn('payment_method', function($row){
    
                    if($row->paid_by == 'cash'){
                        $span = 'كاش (نقدي)';
                    }else{
                        $span = 'فيزا (صراف)';
                    }
    
                    return $span; 
                }) 
                ->addColumn('based_on', function($row){ 
                    if(auth()->user()->can('عرض مشتريات')){  
                        $a = '<a href='.route('preview_purchase',$row->purchase_id).' target="_blank">
                                   '.$row->based_on_bill_number.'</a>';
                    } 
                    return $a; 
                }) 
                ->addColumn('action', function($row){
                    if(auth()->user()->can('عرض مشتريات')){   
                        $btn = '<button type="button" class="btn btn-labeled btn-info preview"
                                   value="'.$row->id.'"><i class="fa fa-eye"></i>معاينة
                                </button>';
                    }
                   
                    return $btn; 
                }) 
                ->rawColumns(['payment_method','based_on','action']) 
                ->make(true);
        } 
    
        return view('admin.Money.Exit.index');
      
    }
}

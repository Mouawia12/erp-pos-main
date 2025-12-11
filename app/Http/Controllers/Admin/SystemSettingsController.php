<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller; 
use App\Models\Branch;
use App\Models\Cashier;
use App\Models\Category;
use App\Models\Company;
use App\Models\Currency;
use App\Models\CustomerGroup;
use App\Models\PosSettings;
use App\Models\SystemSettings;
use App\Models\Warehouse;
use App\Models\ZatcaDocument;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class SystemSettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::guard('admin-web')->user();
        $subscriberId = $user?->subscriber_id;

        $setting = SystemSettings::query()
            ->when($subscriberId, fn ($q) => $q->where('subscriber_id', $subscriberId))
            ->first();
        $currencies = Currency::all();
        $groups = CustomerGroup::all();
        $branches = Warehouse::all();
        $cashiers = Cashier::all();
        $zatcaBranches = Branch::query()
            ->with('zatcaSetting')
            ->when($subscriberId, fn ($q) => $q->where('subscriber_id', $subscriberId))
            ->orderBy('branch_name')
            ->get();
        $zatcaDocuments = ZatcaDocument::query()
            ->with('sale')
            ->when($subscriberId, fn ($q) => $q->where('subscriber_id', $subscriberId))
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.settings.index' , [
            'setting' => $setting,
            'currencies' => $currencies ,
            'groups' => $groups,
            'branches' => $branches,
            'cashiers' => $cashiers,
            'zatcaDocuments' => $zatcaDocuments,
            'zatcaConfig' => config('zatca'),
            'zatcaBranches' => $zatcaBranches,
        ]);

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
        $subscriberId = Auth::guard('admin-web')->user()?->subscriber_id;
        if($request -> id == 0){
            try {
                $data = [
                    'company_name' => $request -> company_name,
                    'currency_id' => $request -> currency_id,
                    'email' => $request -> email,
                    'client_group_id' => $request -> client_group_id,
                    'nom_of_days_to_edit_bill' => $request -> nom_of_days_to_edit_bill,
                    'branch_id' => $request -> branch_id,
                    'cashier_id' => $request -> cashier_id,
                    'item_tax' => $request -> item_tax,
                    'item_expired' => $request -> item_expired,
                    'img_width' => $request -> img_width,
                    'img_height' => $request -> img_height,
                    'small_img_width' => $request -> small_img_width,
                    'small_img_height' => $request -> small_img_height,
                    'barcode_break' => $request -> barcode_break,
                    'sell_without_stock' => $request -> sell_without_stock,
                    'customize_refNumber' => $request -> customize_refNumber,
                    'item_serial' => $request -> item_serial,
                    'adding_item_method' => $request -> adding_item_method,
                    'payment_method' => $request -> payment_method,
                    'default_product_type' => $request->default_product_type ?? '1',
                    'default_invoice_type' => $request->default_invoice_type ?? 'tax_invoice',
                    'invoice_terms' => $request->invoice_terms,
                    'single_device_login' => $request->single_device_login ?? 0,
                    'per_user_sequence' => $request->per_user_sequence ? 1 : 0,
                    'sales_prefix' => $request -> sales_prefix,
                    'sales_return_prefix' => $request -> sales_return_prefix,
                    'payment_prefix' => $request -> payment_prefix,
                    'purchase_payment_prefix' => $request -> purchase_payment_prefix,
                    'deliver_prefix' => $request -> deliver_prefix,
                    'purchase_prefix' => $request -> purchase_prefix,
                    'purchase_return_prefix' => $request -> purchase_return_prefix,
                    'transaction_prefix' => $request -> transaction_prefix,
                    'expenses_prefix' => $request -> expenses_prefix,
                    'store_prefix' => $request -> store_prefix,
                    'quotation_prefix' => $request -> quotation_prefix,
                    'update_qnt_prefix' => $request -> update_qnt_prefix,
                    'fraction_number' => $request -> fraction_number,
                    'qnt_decimal_point' => $request -> qnt_decimal_point,
                    'decimal_type' => $request -> decimal_type,
                    'thousand_type' => $request -> thousand_type,
                    'show_currency' => $request -> show_currency,
                    'currency_label' => $request -> currency_label,
                    'a4_decimal_point' => $request -> a4_decimal_point,
                    'barcode_type' => $request -> barcode_type,
                    'barcode_length' => $request -> barcode_length,
                    'flag_character' => $request -> flag_character,
                    'barcode_start' => $request -> barcode_start,
                    'code_length' => $request -> code_length,
                    'weight_start' => $request -> weight_start,
                    'weight_length' => $request -> weight_length,
                    'weight_divider' => $request -> weight_divider,
                    'email_protocol' => $request -> email_protocol,
                    'email_host' => $request -> email_host,
                    'email_user' => $request -> email_user,
                    'email_password' => $request -> email_password,
                    'email_port' => $request -> email_port,
                    'email_encrypt' => $request -> email_encrypt,
                    'client_value' => $request -> client_value,
                    'client_points' => $request -> client_points,
                    'employee_value' => $request -> employee_value,
                    'employee_points' => $request -> employee_points,
                    'is_tobacco' => $request -> has('is_tobacco')? 1: 0,
                    'tobacco_tax' => $request -> tobacco_tax,
                ];

                $data['subscriber_id'] = $subscriberId;

                SystemSettings::create($data);
                return redirect()->route('system_settings')->with('success' , __('main.created'));
            } catch(QueryException $ex){

                return redirect()->route('system_settings')->with('error' ,  $ex->getMessage());
            }
        } else {
            return   $this -> update($request);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SystemSettings  $systemSettings
     * @return \Illuminate\Http\Response
     */
    public function show(SystemSettings $systemSettings)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\SystemSettings  $systemSettings
     * @return \Illuminate\Http\Response
     */
    public function edit(SystemSettings $systemSettings)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SystemSettings  $systemSettings
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $subscriberId = Auth::guard('admin-web')->user()?->subscriber_id;
        try {
            $data = [
                'company_name' => $request -> company_name,
                'currency_id' => $request -> currency_id,
                'email' => $request -> email,
                'client_group_id' => $request -> client_group_id,
                'nom_of_days_to_edit_bill' => $request -> nom_of_days_to_edit_bill,
                'branch_id' => $request -> branch_id,
                'cashier_id' => $request -> cashier_id,
                'item_tax' => $request -> item_tax,
                'item_expired' => $request -> item_expired,
                'img_width' => $request -> img_width,
                'img_height' => $request -> img_height,
                'small_img_width' => $request -> small_img_width,
                'small_img_height' => $request -> small_img_height,
                'barcode_break' => $request -> barcode_break,
                'sell_without_stock' => $request -> sell_without_stock,
                'customize_refNumber' => $request -> customize_refNumber,
                'item_serial' => $request -> item_serial,
                'adding_item_method' => $request -> adding_item_method,
                'payment_method' => $request -> payment_method,
                'default_product_type' => $request->default_product_type ?? '1',
                'default_invoice_type' => $request->default_invoice_type ?? 'tax_invoice',
                'invoice_terms' => $request->invoice_terms,
                'single_device_login' => $request->single_device_login ?? 0,
                'per_user_sequence' => $request->per_user_sequence ? 1 : 0,
                'sales_prefix' => $request -> sales_prefix,
                'sales_return_prefix' => $request -> sales_return_prefix,
                'payment_prefix' => $request -> payment_prefix,
                'purchase_payment_prefix' => $request -> purchase_payment_prefix,
                'deliver_prefix' => $request -> deliver_prefix,
                'purchase_prefix' => $request -> purchase_prefix,
                'purchase_return_prefix' => $request -> purchase_return_prefix,
                'transaction_prefix' => $request -> transaction_prefix,
                'expenses_prefix' => $request -> expenses_prefix,
                'store_prefix' => $request -> store_prefix,
                'quotation_prefix' => $request -> quotation_prefix,
                'update_qnt_prefix' => $request -> update_qnt_prefix,
                'fraction_number' => $request -> fraction_number,
                'qnt_decimal_point' => $request -> qnt_decimal_point,
                'decimal_type' => $request -> decimal_type,
                'thousand_type' => $request -> thousand_type,
                'show_currency' => $request -> show_currency,
                'currency_label' => $request -> currency_label,
                'a4_decimal_point' => $request -> a4_decimal_point,
                'barcode_type' => $request -> barcode_type,
                'barcode_length' => $request -> barcode_length,
                'flag_character' => $request -> flag_character,
                'barcode_start' => $request -> barcode_start,
                'code_length' => $request -> code_length,
                'weight_start' => $request -> weight_start,
                'weight_length' => $request -> weight_length,
                'weight_divider' => $request -> weight_divider,
                'email_protocol' => $request -> email_protocol,
                'email_host' => $request -> email_host,
                'email_user' => $request -> email_user,
                'email_password' => $request -> email_password,
                'email_port' => $request -> email_port,
                'email_encrypt' => $request -> email_encrypt,
                'client_value' => $request -> client_value,
                'client_points' => $request -> client_points,
                'employee_value' => $request -> employee_value,
                'employee_points' => $request -> employee_points,
                'is_tobacco' => $request -> has('is_tobacco')? 1: 0  ,
                'tobacco_tax' => $request -> tobacco_tax,
            ];

            SystemSettings::updateOrCreate(
                ['subscriber_id' => $subscriberId],
                array_merge($data, ['subscriber_id' => $subscriberId])
            );

            return redirect()->route('system_settings')->with('success' , __('main.updated'));
        } catch(QueryException $ex){

            return redirect()->route('system_settings')->with('error' ,  $ex->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SystemSettings  $systemSettings
     * @return \Illuminate\Http\Response
     */
    public function destroy(SystemSettings $systemSettings)
    {
        //
    }

    public function settings(){
        $settings = SystemSettings::all() -> first();
        echo json_encode($settings);
        exit();
    }

    public function enableNegativeStock(Request $request)
    {
        $user = Auth::guard('admin-web')->user() ?? Auth::user();
        if(!$user || !$user->can('تعديل الاعدادات')){
            return response()->json([
                'message' => __('main.enable_negative_stock_error')
            ], 403);
        }

        $subscriberId = $user?->subscriber_id;
        $query = SystemSettings::query();

        if($subscriberId && Schema::hasColumn('system_settings','subscriber_id')){
            $query->where('subscriber_id',$subscriberId);
        }

        $setting = $query->first();

        if(!$setting){
            $setting = new SystemSettings();
            if(Schema::hasColumn('system_settings','subscriber_id')){
                $setting->subscriber_id = $subscriberId;
            }
        }

        $setting->sell_without_stock = 2;
        $setting->save();

        return response()->json(['status' => 'ok']);
    }

}

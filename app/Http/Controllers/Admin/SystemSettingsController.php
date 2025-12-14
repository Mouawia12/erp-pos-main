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
use Illuminate\Support\Facades\Log;
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
                $data = $this->validatedSettingsData($request);
                $data['subscriber_id'] = $subscriberId;

                SystemSettings::create($data);
                return redirect()->route('system_settings')->with('success' , __('main.created'));
            } catch(QueryException|\Exception $ex){
                Log::error('system_settings_store_error', ['error' => $ex->getMessage()]);
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
            $data = $this->validatedSettingsData($request);

            SystemSettings::updateOrCreate(
                ['subscriber_id' => $subscriberId],
                array_merge($data, ['subscriber_id' => $subscriberId])
            );

            return redirect()->route('system_settings')->with('success' , __('main.updated'));
        } catch(QueryException|\Exception $ex){
            Log::error('system_settings_update_error', ['error' => $ex->getMessage()]);
            return redirect()->route('system_settings')->with('error' ,  $ex->getMessage());
        }
    }

    private function validatedSettingsData(Request $request): array
    {
        $validated = $request->validate([
            'company_name' => ['required','string','max:191'],
            'currency_id' => ['required','integer'],
            'email' => ['nullable','email','max:191'],
            'client_group_id' => ['nullable','integer'],
            'nom_of_days_to_edit_bill' => ['nullable','integer','min:0'],
            'branch_id' => ['nullable','integer'],
            'cashier_id' => ['nullable','integer'],
            'item_tax' => ['nullable','numeric','min:0'],
            'item_expired' => ['nullable','numeric','min:0'],
            'img_width' => ['nullable','numeric','min:0'],
            'img_height' => ['nullable','numeric','min:0'],
            'small_img_width' => ['nullable','numeric','min:0'],
            'small_img_height' => ['nullable','numeric','min:0'],
            'barcode_break' => ['nullable','integer','min:0'],
            'sell_without_stock' => ['nullable','integer'],
            'customize_refNumber' => ['nullable','integer'],
            'item_serial' => ['nullable','integer'],
            'adding_item_method' => ['nullable','integer'],
            'payment_method' => ['nullable','integer'],
            'default_product_type' => ['nullable','string'],
            'default_invoice_type' => ['nullable','string','max:191'],
            'invoice_terms' => ['nullable','string'],
            'single_device_login' => ['nullable','integer'],
            'per_user_sequence' => ['nullable'],
            'enable_vehicle_features' => ['nullable','boolean'],
            'sales_prefix' => ['nullable','string','max:191'],
            'sales_return_prefix' => ['nullable','string','max:191'],
            'payment_prefix' => ['nullable','string','max:191'],
            'purchase_payment_prefix' => ['nullable','string','max:191'],
            'deliver_prefix' => ['nullable','string','max:191'],
            'purchase_prefix' => ['nullable','string','max:191'],
            'purchase_return_prefix' => ['nullable','string','max:191'],
            'transaction_prefix' => ['nullable','string','max:191'],
            'expenses_prefix' => ['nullable','string','max:191'],
            'store_prefix' => ['nullable','string','max:191'],
            'quotation_prefix' => ['nullable','string','max:191'],
            'update_qnt_prefix' => ['nullable','string','max:191'],
            'fraction_number' => ['nullable','numeric','min:0'],
            'qnt_decimal_point' => ['nullable','numeric','min:0'],
            'decimal_type' => ['nullable','string','max:50'],
            'thousand_type' => ['nullable','string','max:50'],
            'show_currency' => ['nullable','integer'],
            'currency_label' => ['nullable','string','max:50'],
            'a4_decimal_point' => ['nullable','numeric','min:0'],
            'barcode_type' => ['nullable','string','max:50'],
            'barcode_length' => ['nullable','numeric','min:0'],
            'flag_character' => ['nullable','string','max:10'],
            'barcode_start' => ['nullable','string','max:10'],
            'code_length' => ['nullable','numeric','min:0'],
            'weight_start' => ['nullable','string','max:10'],
            'weight_length' => ['nullable','numeric','min:0'],
            'weight_divider' => ['nullable','numeric','min:0'],
            'email_protocol' => ['nullable','string','max:50'],
            'email_host' => ['nullable','string','max:191'],
            'email_user' => ['nullable','string','max:191'],
            'email_password' => ['nullable','string','max:191'],
            'email_port' => ['nullable','numeric','min:0'],
            'email_encrypt' => ['nullable','string','max:50'],
            'client_value' => ['nullable','numeric','min:0'],
            'client_points' => ['nullable','numeric','min:0'],
            'employee_value' => ['nullable','numeric','min:0'],
            'employee_points' => ['nullable','numeric','min:0'],
            'is_tobacco' => ['nullable'],
            'tobacco_tax' => ['nullable','numeric','min:0'],
            'tax_number' => ['nullable','string','max:191'],
        ]);

        $data = array_merge($validated, [
            'single_device_login' => $request->single_device_login ?? 0,
            'per_user_sequence' => $request->boolean('per_user_sequence'),
            'enable_vehicle_features' => $request->boolean('enable_vehicle_features'),
            'is_tobacco' => $request->has('is_tobacco') ? 1 : 0,
        ]);

        // احذف أي حقل غير موجود فعلياً في جدول system_settings لتجنب أخطاء الأعمدة
        foreach (array_keys($data) as $key) {
            if (!Schema::hasColumn('system_settings', $key)) {
                unset($data[$key]);
            }
        }

        return $data;
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

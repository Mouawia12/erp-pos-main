<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User; 
use App\Models\Branch;
use App\Models\Expenses;
use App\Models\Purchase;
use App\Models\Sales;
use App\Models\SystemSettings;
use App\Models\Company;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:admin-web');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable|\Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        $auth_id = Auth::user()->id;
        $user = User::findOrFail($auth_id);
        $roles = Role::where('guard_name', 'admin-web')->get();
        $day = date("Y-m-d"); 

        if(empty($user->branch_id)){
            $Admins = User::all();
            $branches = Branch::all();
            $sales = Sales::where('sale_id' , 0 )->whereDate('created_at',$day)->sum('net');
            $sales_return = Sales::where('sale_id' ,'>', 0 )->whereDate('created_at',$day)->sum('net');
            $purchases = Purchase::where('returned_bill_id' , 0 )->whereDate('created_at',$day)->sum('net');
            $purchases_return = Purchase::where('returned_bill_id' , '>' , 0 )->whereDate('created_at',$day)->sum('net');
            $settings = SystemSettings::first();
            $clients = Company::where('group_id',3)->get();
            $suppliers = Company::where('group_id',4)->get();
        }else{
            $Admins = User::where('branch_id',$user->branch_id)->get();
            $branches = Branch::where('id',$user->branch_id)->get();
            $sales = Sales::where('sale_id' , 0 )->where('branch_id',$user->branch_id)->whereDate('created_at',$day)->sum('net');
            $sales_return = Sales::where('sale_id' ,'>', 0 )->where('branch_id',$user->branch_id)->whereDate('created_at',$day)->sum('net');
            $purchases = Purchase::where('returned_bill_id' , 0 ) ->where('branch_id',$user->branch_id)->whereDate('created_at',$day)->sum('net');
            $purchases_return = Purchase::where('returned_bill_id' , '>' , 0 ) ->where('branch_id',$user->branch_id)->whereDate('created_at',$day)->sum('net');
            $settings = SystemSettings::first();
            $clients = Company::where('group_id',3)->get();
            $suppliers = Company::where('group_id',4)->get();
        }

        $remaining_days = null;
        if($settings && !empty($settings->valid_to)){
            $validTo = $settings->valid_to;
            $new_format = str_replace('/', '-', $validTo);
            $timestamp = strtotime($new_format);
            $currentDate = time();
            $datediff = $timestamp - $currentDate;
            $remaining_days = round($datediff / (60 * 60 * 24));
        }
 
         return view('admin.home' , compact('user','roles', 'Admins','branches'
                , 'sales', 'purchases','sales_return','purchases_return'
                ,'remaining_days','settings','clients','suppliers'));
 
	}

    public function lock_screen()
    {
        return view('admin.lockscreen');
    }
     
    
    

}

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
use App\Models\Subscriber;
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

        // لوحة المالك: إظهار بيانات المشتركين فقط
        if($user->hasRole('system_owner')){
            $subs = Subscriber::select('id','company_name','status','subscription_end')
                ->get();
            $stats = [
                'total' => $subs->count(),
                'active' => $subs->where('status','active')->count(),
                'near_expiry' => $subs->where('status','near_expiry')->count(),
                'expired' => $subs->where('status','expired')->count(),
            ];
            $nearExpiryList = $subs->where('status','near_expiry')->sortBy('subscription_end')->take(5);
            return view('admin.home_owner', compact('user','stats','nearExpiryList','roles'));
        }

        $subscriberId = $user->subscriber_id;
        // للمشتركين نعرض كل فروعه، فلا نفرض فلترة الفرع إلا إن كانت محددة بوضوح
        $branchFilter = $subscriberId ? null : ($user->branch_id ?: null);

        $Admins = User::when($branchFilter,function($q) use ($branchFilter){ return $q->where('branch_id',$branchFilter); })
                      ->when($subscriberId,function($q) use ($subscriberId){ return $q->where('subscriber_id',$subscriberId); })
                      ->get();

        $branches = Branch::when($branchFilter,function($q) use ($branchFilter){ return $q->where('id',$branchFilter); })
                          ->when($subscriberId,function($q) use ($subscriberId){ return $q->where('subscriber_id',$subscriberId); })
                          ->get();

        $sales = Sales::where('sale_id' , 0 )
            ->when($branchFilter,function($q) use ($branchFilter){ return $q->where('branch_id',$branchFilter); })
            ->when($subscriberId,function($q) use ($subscriberId){ return $q->where('subscriber_id',$subscriberId); })
            ->whereDate('date',$day)->sum('net');

        $sales_return = Sales::where('sale_id' ,'>', 0 )
            ->when($branchFilter,function($q) use ($branchFilter){ return $q->where('branch_id',$branchFilter); })
            ->when($subscriberId,function($q) use ($subscriberId){ return $q->where('subscriber_id',$subscriberId); })
            ->whereDate('date',$day)->sum('net');

        $purchases = Purchase::where('returned_bill_id' , 0 )
            ->when($branchFilter,function($q) use ($branchFilter){ return $q->where('branch_id',$branchFilter); })
            ->when($subscriberId,function($q) use ($subscriberId){ return $q->where('subscriber_id',$subscriberId); })
            ->whereDate('date',$day)->sum('net');

        $purchases_return = Purchase::where('returned_bill_id' , '>' , 0 )
            ->when($branchFilter,function($q) use ($branchFilter){ return $q->where('branch_id',$branchFilter); })
            ->when($subscriberId,function($q) use ($subscriberId){ return $q->where('subscriber_id',$subscriberId); })
            ->whereDate('date',$day)->sum('net');

        $settings = SystemSettings::first();
        $clients = Company::where('group_id',3)->when($subscriberId,function($q) use ($subscriberId){ return $q->where('subscriber_id',$subscriberId); })->get();
        $suppliers = Company::where('group_id',4)->when($subscriberId,function($q) use ($subscriberId){ return $q->where('subscriber_id',$subscriberId); })->get();

        // بيانات المخططات (آخر 7 أيام)
        $dates = collect(range(0,6))->map(fn($i)=>Carbon::today()->subDays($i))->sort();
        $salesChart = [];
        $purchaseChart = [];
        foreach($dates as $date){
            $salesChart[] = Sales::where('sale_id',0)
                ->when($branchFilter,function($q) use ($branchFilter){ return $q->where('branch_id',$branchFilter); })
                ->when($subscriberId,function($q) use ($subscriberId){ return $q->where('subscriber_id',$subscriberId); })
                ->whereDate('date',$date)->sum('net');
            $purchaseChart[] = Purchase::where('returned_bill_id',0)
                ->when($branchFilter,function($q) use ($branchFilter){ return $q->where('branch_id',$branchFilter); })
                ->when($subscriberId,function($q) use ($subscriberId){ return $q->where('subscriber_id',$subscriberId); })
                ->whereDate('date',$date)->sum('net');
        }
        // اجعل البيانات مصفوفات عادية لضمان توافق JSON مع Chart.js
        $chartLabels = $dates->map(fn($d)=>$d->format('m-d'))->values()->all();
        $salesChart = array_values($salesChart);
        $purchaseChart = array_values($purchaseChart);

        $remaining_days = null;
        if($settings && !empty($settings->valid_to)){
            $validTo = $settings->valid_to;
            $new_format = str_replace('/', '-', $validTo);
            $timestamp = strtotime($new_format);
            $currentDate = time();
            $datediff = $timestamp - $currentDate;
            $remaining_days = round($datediff / (60 * 60 * 24));
        }
 
         return view('admin.home' , compact(
             'user',
             'roles',
             'Admins',
             'branches',
             'sales',
             'purchases',
             'sales_return',
             'purchases_return',
             'remaining_days',
             'settings',
             'clients',
             'suppliers',
             'salesChart',
             'purchaseChart',
             'chartLabels'
         ));
 
	}

    public function lock_screen()
    {
        return view('admin.lockscreen');
    }
     
    
    

}

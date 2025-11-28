<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller; 
use App\Models\Brand;
use App\Models\Warehouse;
use App\Models\Branch;
use App\Http\Requests\StoreWarehouseRequest;
use App\Http\Requests\UpdateWarehouseRequest;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class WarehouseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        $subscriberId = $user->subscriber_id;
        $userBranchId = $user->branch_id;

        $warehouses = Warehouse::query()
            ->when($subscriberId, fn($q) => $q->where('subscriber_id', $subscriberId))
            ->when(!$subscriberId && $userBranchId, fn($q) => $q->where('branch_id', $userBranchId))
            ->get();

        $branches = Branch::query()
            ->where('status', 1)
            ->when($subscriberId, fn($q) => $q->where('subscriber_id', $subscriberId))
            ->when(!$subscriberId && $userBranchId, fn($q) => $q->where('id', $userBranchId))
            ->get();

        return view('admin.warehouse.index', [
            'warehouses' => $warehouses,
            'branches' => $branches,
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
     * @param  \App\Http\Requests\StoreWarehouseRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $user = Auth::user();
        $subscriberId = $user->subscriber_id;

        if($request -> id == 0){

            $request['code'] = Warehouse::count() + 1;
            $validated = $request->validate([
                'code' => 'required|unique:warehouses',
                'name' => 'required',
                'branch_id' => 'required|integer'
            ]);

            $branch = Branch::query()
                ->where('id', $request->branch_id)
                ->when($subscriberId, fn($q) => $q->where('subscriber_id', $subscriberId))
                ->when(!$subscriberId && $user->branch_id, fn($q) => $q->where('id', $user->branch_id))
                ->firstOrFail();
            try {
                Warehouse::create([
                    'code' => $request->code,
                    'name' => $request->name,
                    'phone' => $request->phone ? $request->phone : ' ' ,
                    'email' => $request->email ? $request->email : ' ',
                    'address' => $request->address ? $request->address : ' ',
                    'tax_number' => $request->tax_number ?? ' ',
                    'commercial_registration' => $request->commercial_registration ??  ' ',
                    'serial_prefix' => $request->serial_prefix ?? ' ',
                    'branch_id' => $branch->id,
                    'subscriber_id' => $subscriberId,
                    'status' => 1,
                    'user_id' => $user->id
                ]);
                return redirect()->route('warehouses')->with('success' , __('main.created'));
            } catch(QueryException $ex){

                return redirect()->route('warehouses')->with('error' ,  $ex->getMessage());
            }
           
        } else { 
            return  $this -> update($request);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Warehouse  $warehouse
     * @return \Illuminate\Http\Response
     */
    public function show(Warehouse $warehouse)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Warehouse  $warehouse
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $warehouse = Warehouse::find($id );
        echo json_encode ($warehouse);
        exit;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateWarehouseRequest  $request
     * @param  \App\Models\Warehouse  $warehouse
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $warehouse = Warehouse::find($request -> id);
        $user = Auth::user();
        $subscriberId = $user->subscriber_id;

        if($warehouse){
            $validated = $request->validate([
                'name' => 'required',
                'branch_id' => 'required|integer'
            ]);

            $branch = Branch::query()
                ->where('id', $request->branch_id)
                ->when($subscriberId, fn($q) => $q->where('subscriber_id', $subscriberId))
                ->when(!$subscriberId && $user->branch_id, fn($q) => $q->where('id', $user->branch_id))
                ->firstOrFail();
            try {
            $warehouse -> update([ 
                'name' => $request->name,
                'phone' => $request->phone ? $request->phone : ' ' ,
                'email' => $request->email ? $request->email : ' ',
                'address' => $request->address ? $request->address : ' ',
                'tax_number' => $request->tax_number ?? ' ',
                'commercial_registration' => $request->commercial_registration ??  ' ',
                'serial_prefix' => $request->serial_prefix ?? ' ',
                'branch_id' => $branch->id, 
                'subscriber_id' => $subscriberId,
                'user_id' => $user->id
            ]);
                return redirect()->route('warehouses')->with('success' , __('main.updated'));
            } catch(QueryException $ex){ 
                return redirect()->route('warehouses')->with('error' ,  $ex->getMessage());
            }
        }
    }

    public function get_warehouses_branches($id){  
        $warehouses = Warehouse::where('branch_id', $id)->get();
        echo json_encode($warehouses);
        exit(); 
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Warehouse  $warehouse
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $warehouse = Warehouse::find($id );
        if($warehouse){
            $warehouse -> delete();
            return redirect()->route('warehouses')->with('success' , __('main.deleted'));
        }
    }
}

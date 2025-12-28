<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Company; 
use App\Models\CompanyInfo; 
use App\Models\Product; 
use App\Models\Warehouse; 
use App\Models\WarehouseProducts; 
use App\Models\Inventory; 
use App\Models\InventoryDetails; 
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class InventoryController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    { 
        $inventorys = Inventory::orderBy('id', 'desc')
                        ->where('state',1)
                        ->get();    

        if(!empty(Auth::user()->branch_id)) {
            $inventorys = $inventorys->where('branch_id', Auth::user()->branch_id); 
        }  

        return view('admin.inventory.index' , compact('inventorys'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
 
        if(!empty(Auth::user()->branch_id)) { 
            $warehouses = Warehouse::where('branch_id',Auth::user()->branch_id)->where('status',1)->get(); 
            $inventorys = Inventory::where('branch_id',Auth::user()->branch_id)->where('state',0) -> first();
        } else{
            $warehouses = Warehouse::where('status',1)->get(); 
            $inventorys = Inventory::where('state',0) -> first();
        } 

        if(!isset($inventorys)){
            $defaultWarehouse = $warehouses->first();
            $branchId = Auth::user()->branch_id ?? ($defaultWarehouse->branch_id ?? 0);
            $warehouseId = $defaultWarehouse->id ?? 0;

            $inventorys = Inventory::create([
                'branch_id' => $branchId,
                'warehouse_id' => $warehouseId,
                'date' => date('Y-m-d'), 
                'state' => 0,
                'user_id' => Auth::user() -> id
            ]);
        }  

        return view('admin.inventory.create', compact('inventorys','warehouses'));
    } 

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Item $item
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $item = Product::find($id);
        if ($item) {
            echo json_encode($item);
            exit;
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Item $item
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $products = Product::all();
        $warehouses = Warehouse::where('status',1)->get();
        $warehouse_products = WarehouseProducts::all(); 
        $inventory = Inventory::where('id',$id) -> first();
        $inventory_details = InventoryDetails::where('inventory_id',$id) -> get(); 

        return view('admin.inventory.edite' , compact('inventory','inventory_details','products','warehouses','warehouse_products'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Item $item
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
       
    }

    public function inventory_state($id)
    {    
        $inventory = Inventory::find($id);
        $inventory_details = InventoryDetails::select('item_id')->where('inventory_id', $id);

        $items = Product::with('units') 
            ->Join('warehouse_products','products.id','=','warehouse_products.product_id')  
            ->where('warehouse_products.warehouse_id',$inventory->warehouse_id) 
            ->whereNotIn('products.id', $inventory_details)
            ->where('status', 1)
            ->get();
  
        echo json_encode($items);
        exit;
    }

    public function inventory_weight_item(Request $request)
    {   

        $item = Product::where('id',$request->id)
            ->first();
        $inventorys = Inventory::where('state',0) -> first();   
            
        if(isset($item))  { 

            InventoryDetails::create([ 
                'inventory_id' =>  $request->inventory_id ?? 0, 
                'unit' => $request->unit ?? 0,
                'item_id' => $request->id ?? 0,
                'quantity' => $request->quantity ?? 0,
                'batch_no' => $request->batch_no ?? null,
                'production_date' => $request->production_date ?? null,
                'expiry_date' => $request->expiry_date ?? null,
                'new_quantity' => 0,
                'is_counted' => 0,
                'state' => 1, 
                'user_id' => Auth::user() -> id
            ]);

            if($inventorys){
                
                $siteController = new SystemController();
                $inventorys->update([ 
                    'warehouse_id' => $request->warehouse_id, 
                    'branch_id' => $request->branch_id ?? $siteController->getWarehouseById($request->warehouse_id)->branch_id,
                    'state' => 1 
                ]);

            }
        }          

    }

    public function update_weight_item(Request $request)
    {
        $item = Product::where('id',$request->id)
            ->first();
        $inventory_details = InventoryDetails::where('inventory_id',$request->inventory_id)
            ->where('item_id',$request->id)
            ->first();
        if(isset($item) && $inventory_details && $request->new_quantity !== null && $request->new_quantity !== '') { 
            $inventory_details->update([
                'new_quantity' => $request->new_quantity,
                'batch_no' => $request->batch_no ?? $inventory_details->batch_no,
                'production_date' => $request->production_date ?? $inventory_details->production_date,
                'expiry_date' => $request->expiry_date ?? $inventory_details->expiry_date,
                'is_counted' => 1,
            ]);
        }          

    }

    public function match_inventory(Request $request)
    {
        $inventory = Inventory::findOrFail($request->inventory_id);

        if (!empty(Auth::user()->branch_id) && $inventory->branch_id != Auth::user()->branch_id) {
            return redirect()->back()->with('error', __('main.unauthorized'));
        }

        if ($inventory->is_matched) {
            return redirect()->back()->with('success', __('main.inventory_already_matched'));
        }

        $details = InventoryDetails::where('inventory_id', $inventory->id)
            ->where('is_counted', 1)
            ->get();

        foreach ($details as $detail) {
            $warehouseProduct = WarehouseProducts::firstOrNew([
                'warehouse_id' => $inventory->warehouse_id,
                'product_id' => $detail->item_id,
            ]);
            $warehouseProduct->quantity = $detail->new_quantity;
            $warehouseProduct->save();
        }

        $inventory->update([
            'is_matched' => 1,
        ]);

        return redirect()->back()->with('success', __('main.inventory_matched'));
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Item $item
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $inventorys = Inventory::where('id',$request->inventory_id) -> first();
        $inventory_details = InventoryDetails::where('inventory_id',$request->inventory_id)->where('new_quantity',0) -> get();
 
        if($inventorys){ 
            
            if($inventory_details){
                foreach ($inventory_details as $inventory_detail) {
                    $detail = InventoryDetails::FindOrFail($inventory_detail->id);
                    $detail->delete();
                }
            }

            $inventorys->delete(); 

            return redirect()->route('admin.inventory.index')
                ->with('success', 'تم حذف الجرد بنجاح');
        } 
   
    }
 
 
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller; 
use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\ProductUnit;
use App\Models\PurchaseDetails;
use App\Models\SaleDetails;
use App\Models\SystemSettings;
use App\Models\UpdateQuntityDetails;
use App\Models\WarehouseProducts;
use App\Models\Warehouse;
use App\Models\CompanyInfo;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use DataTables;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
    */

    public function index(Request $request)
    { 
      
        $data = DB::table('products') 
            ->leftJoin('categories','products.category_id','=','categories.id')
            ->leftJoin('units','products.unit','=','units.id')
            ->leftJoin('brands','products.brand','=','brands.id')  
            ->select('products.*','units.name as unitName','brands.name as brandName',
                    'categories.name as category_name_ar') 
            ->get(); 
            
        if ($request->ajax()) { 
            return Datatables::of($data)->addIndexColumn()
                ->addColumn('action', function($row){   
                    if(auth()->user()->can('تعديل صنف')){  
                        $btn ='<a href='.route('editProduct',$row->id).' class="btn btn-labeled btn-info" value="'.$row->id.'"  role="button"><i class="fa fa-pen"></i> </a>';
                    }
                    if(auth()->user()->can('حذف صنف')){  
                        $btn = $btn.'<a href="#deleteModal" class="btn btn-labeled btn-danger deleteBtn " id="'.$row->id.'" data-toggle="modal" role="button"> <i class="fa fa-trash"></i></a>';
                    }
                    
                    if(empty($btn)){
                        $btn ='';
                    }
                    
                    return $btn; 
                }) 
                ->rawColumns(['action']) 
                ->make(true);
        } 

        return view('admin.products.index');
   
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $systemController = new SystemController(); 
        $brands = $systemController->getAllBrands();
        $units = $systemController->getAllUnits();
        $categories = $systemController->getAllMainCategories();
        $taxRages = $systemController->getAllTaxRates();
        $taxTypes = $systemController->getAllTaxTypes();

        return view('admin.products.create',compact('brands','categories','taxRages','taxTypes','units'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreProductRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request['code'] =  $request->code ? $request->code : $this->getItemCode();

        $validated = $request->validate([
            'code' => 'required|unique:products',
            'name' => 'required|unique:products',
            'unit' => 'required',
            'cost' => 'required',
            'price' => 'required',
            'category_id' => 'required',
            'tax_rate' => 'required',
            'tax_method' => 'required',
            'type' => 'required',
            'brand' => 'required',
            //'slug' => 'required',
        ]);
        $siteController = new SystemController();
        $tax_excise = $siteController->getCategoryById($request->category_id)->tax_excise;

        if($request->has('img')) {
            if($request->file('img')->getSize() / 1000 > 2000) {
                return redirect()->route('items')->with('error', __('main.img_big'));
            }
            $imageName = time() . '.' . $request->img->extension();
            $request->img->move(('uploads/items/images'), $imageName);
        } else {
            $imageName = '';
        }
       
        try
        {
       
            $product = Product::create([
                'code' => $request->code,
                'name' => $request->name,
                'unit' => $request->unit,
                'cost' => $request->cost,
                'price' => $request->price,
                'lista' => $request->lista ?? 0,
                'alert_quantity' => $request->alert_quantity ?? 0,
                'category_id' => $request->category_id,
                'subcategory_id' => $request->subcategory_id ?? 0,
                'quantity' => $request->quantity ?? 0,
                'tax' => $request->tax ?? 0,
                'tax_rate' => $request->tax_rate,
                'track_quantity' => $request->track_quantity ?? 0,
                'tax_method' => $request->tax_method,
                'tax_excise' => $tax_excise > 0 ? $tax_excise:0,
                'type' => $request->type,
                'brand' => $request->brand,
                'slug' => $request->slug ?? 0,
                'featured' => $request->featured ?? 0, 
                'city_tax' => $request->city_tax ?? 0,
                'max_order' => $request->max_order ?? 0,
                'img' => $imageName,
                'branch_id' => $request -> branch_id ?? 1,
                'user_id' => Auth::user() -> id,
                'status' => $request->status ?? 1,
            ]);
            if($product){ 
                $unitsTable = $request->product_units;
                if($unitsTable){
                    foreach ($unitsTable as $row){
                        ProductUnit::create([
                            'product_id' => $product->id,
                            'unit_id' => $row['unit'],
                            'price' => $row['price'],
                            'conversion_factor' => $row['conversion_factor'] ?? 1,
                            'barcode' => $row['barcode'] ?? null,
                        ]);
                    }
                }else{
                    ProductUnit::create([
                        'product_id' => $product->id,
                        'unit_id' => $product->unit,
                        'price' => $product->price,
                        'conversion_factor' => 1,
                    ]);
                }

                $systemController = new SystemController();
                $warehouses = $systemController->getAllWarehouses();
                foreach ($warehouses as $warehouse){
                    WarehouseProducts::create([
                        'warehouse_id' => $warehouse->id,
                        'product_id' => $product->id,
                        'cost' => $product->cost,
                        'quantity' => 0
                    ]);
                }

                return redirect()->route('products');
            }
         
        } catch(QueryException $ex){ 
            return redirect()->route('products')->with('error' ,  $ex->getMessage());
        } 
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product = Product::find($id);
        if($product){
            $systemController = new SystemController();
            $brands = $systemController->getAllBrands();
            $units = $systemController->getAllUnits();
            $categories = $systemController->getAllMainCategories();
            $taxRages = $systemController->getAllTaxRates();
            $taxTypes = $systemController->getAllTaxTypes();

            return view('admin.products.update',
                compact('product','brands','categories','taxRages','taxTypes','units'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateProductRequest  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
 
        $product = Product::find($id);
        if($product){
            $validated = $request->validate([
                'code' => ['required' , Rule::unique('products')->ignore($id)],
                'name' => ['required' , Rule::unique('products')->ignore($id)],
                'unit' => 'required',
                'cost' => 'required',
                'price' => 'required',
                'category_id' => 'required',
                'tax_rate' => 'required',
                'tax_method' => 'required',
                'type' => 'required',
                'brand' => 'required', 
                'img' => 'max:2000',
            ]);

            $siteController = new SystemController();
            $tax_excise = $siteController->getCategoryById($request->category_id)->tax_excise;

            if($request->has('img')) {
                if($request->file('img')->getSize() / 1000 > 2000) {
                    return redirect()->route('items')->with('error', __('main.img_big'));
                }
                $imageName = time() . '.' . $request->img->extension();
                $request->img->move(('uploads/items/images'), $imageName);
            } else {
                $imageName = '';
            }

            try {
                $product -> update ([
                    'code' => $request->code,
                    'name' => $request->name,
                    'unit' => $request->unit,
                    'cost' => $request->cost,
                    'price' => $request->price,
                    'lista' => $request->lista ?? 0,
                    'alert_quantity' => $request->alert_quantity ?? 0,
                    'category_id' => $request->category_id,
                    'subcategory_id' => $request->subcategory_id ?? 0,
                    'quantity' => $request->quantity ?? 0,
                    'tax' => $request->tax ?? 0,
                    'tax_rate' => $request->tax_rate,
                    'track_quantity' => $request->track_quantity ?? 0,
                    'tax_method' => $request->tax_method,
                    'tax_excise' => $tax_excise > 0 ? $tax_excise:0,
                    'type' => $request->type,
                    'brand' => $request->brand,
                    'slug' => $request->slug,
                    'featured' => $request->featured ?? 0, 
                    'city_tax' => $request->city_tax ?? 0,
                    'max_order' => $request->max_order ?? 0,
                    'img' => $imageName,
                    'branch_id' => $request -> branch_id ?? 1,
                    'user_id' => Auth::user() -> id,
                    'status' => $request->status ?? 1

                ]);

                $units = ProductUnit::where('product_id' , '=' , $product -> id) -> get();
                foreach ($units as $unit){
                    $unit -> delete();
                }
                $unitsTable = $request->product_units;
                if($unitsTable){
                    foreach ($unitsTable as $row){
                        ProductUnit::create([
                            'product_id' => $product->id,
                            'unit_id' => $row['unit'],
                            'price' => $row['price'],
                            'conversion_factor' => $row['conversion_factor'] ?? 1,
                            'barcode' => $row['barcode'] ?? null,
                        ]);
                    }
                }else{
                    ProductUnit::create([
                        'product_id' => $product->id,
                        'unit_id' => $product->unit,
                        'price' => $product->price,
                        'conversion_factor' => 1,
                    ]);
                }


//                $systemController = new SystemController();
//                $warehouses = $systemController->getAllWarehouses();
//                foreach ($warehouses as $warehouse){
//                    WarehouseProducts::create([
//                        'warehouse_id' => $warehouse->id,
//                        'product_id' => $product->id,
//                        'cost' => $product->cost,
//                        'quantity' => 0
//                    ]);
//                }
                return redirect()->route('products')->with('success' ,  __('main.updated'));
            }catch(QueryException $ex){ 
                return redirect()->route('products')->with('error' ,  $ex->getMessage());
            } 
        } 
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
 
        $product = Product::find($request->id) ;
     
        if($product) { 
            $sales = SaleDetails::where('product_id', '=', $request->id)->get();
            $purchases = PurchaseDetails::where('product_id', '=', $request->id)->get(); 
            if (count($sales) == 0 && count($purchases) == 0 ) {
                    $unites = ProductUnit::where('product_id', '=', $request->id)->get();
                    $warehouseProducts = WarehouseProducts::where('product_id', '=', $request->id)->get();
                    foreach ($unites as $unit){
                        $unit -> delete();
                    }
                    foreach ($warehouseProducts as $wpro){
                        $wpro -> delete();
                    }
                    $product -> delete();
                    return redirect()->route('products')->with('success', __('main.deleted'));
            } else {
                    // can not delete
                    return redirect()->route('products')->with('success', __('main.can_not_delete'));
            } 
        }
        
    }

    public function getProduct($code)
    {
        $single = $this->getSingleProduct($code);

        if($single){ 
            echo json_encode([$single]);
            exit;
        }else{
            $product = Product::where('code' , 'like' , '%'.$code.'%')
                ->orWhere('name','like' , '%'.$code.'%')
                ->limit(5)
                ->get()
                ->map(function($p){ return $this->appendProductMeta($p); });
            echo json_encode ($product);
            exit;
        } 
    }

    private function getSingleProduct($code){ 
        $product = Product::where('code' , '=' , $code)
            ->orWhere('name','=' , $code)
            ->first(); 

        return $this->appendProductMeta($product);
    }
    
    private function appendProductMeta($product){
        if(!$product){
            return null;
        }

        $product->last_sale_price = $this->getLastSalePrice($product->id);
        $product->units_options = $this->getUnitsForProduct($product->id,$product);

        return $product;
    }

    private function getLastSalePrice($productId){
        return SaleDetails::where('product_id',$productId)
            ->where('quantity','>',0)
            ->orderByDesc('id')
            ->value('price_unit') ?? 0;
    }

    private function getUnitsForProduct($productId,$product = null){
        $units = ProductUnit::query()
            ->join('units','units.id','=','product_units.unit_id')
            ->where('product_id',$productId)
            ->select('product_units.*','units.name as unit_name')
            ->get();

        if($units->isEmpty() && $product){
            return [[
                'unit_id' => $product->unit,
                'unit_name' => optional($product->units)->name,
                'price' => $product->price,
                'conversion_factor' => 1,
                'barcode' => null
            ]];
        }

        return $units->map(function($u){
            return [
                'unit_id' => $u->unit_id,
                'unit_name' => $u->unit_name,
                'price' => $u->price,
                'conversion_factor' => $u->conversion_factor ?? 1,
                'barcode' => $u->barcode
            ];
        });
    }

    public function get_product_warehouse($warehouse_id,$code)
    {
        $single = $this->getSingleProductWarehouse($warehouse_id,$code);

        if($single){ 
            echo json_encode([ $this->appendProductMeta($single) ]);
            exit;
        }else{ 
            $product = Product::with('units')
                ->Join('warehouse_products','products.id','=','warehouse_products.product_id')
                ->select('products.*' , 'warehouse_products.quantity as qty')
                ->where('products.code' , 'like' , '%'.$code.'%')
                ->where('warehouse_products.warehouse_id',$warehouse_id) 
                ->where('products.status', 1) 
                ->orWhere(function($query)use ($code,$warehouse_id) {
                    $query->where('products.name', 'like', '%' . $code . '%')
                          ->where('warehouse_products.warehouse_id', $warehouse_id);
                }) 
                ->limit(5)
                ->get()
                ->map(function($product){
                    return $this->appendProductMeta($product);
                });

            echo json_encode ($product);
            exit;
        }

    }

    private function getSingleProductWarehouse($warehouse_id,$code){
        
        $product = Product::with('units')
            ->Join('warehouse_products','products.id','=','warehouse_products.product_id')  
            ->select('products.*' , 'warehouse_products.quantity as qty')
            ->where('products.code', '=', $code)
            ->where('warehouse_products.warehouse_id',$warehouse_id) 
            ->where('products.status', 1) 
            ->orWhere(function($query)use ($warehouse_id,$code) {
                $query->where('products.name', '=', $code)
                ->where('warehouse_products.warehouse_id', $warehouse_id);
            }) 
            ->first();

        return $this->appendProductMeta($product);
    }

    public function print_barcode(){
        $company = CompanyInfo::first();  
        return view('admin.products.print_barcode', compact('company'));
    }

    public function do_print_barcode(Request $request){

        $data = [];
        foreach ($request->product_id as $index=>$id){
            $product = Product::find($id);
            $settings = SystemSettings::get()->first();
            $qnt = $request->qnt[$index];
            $item = [
                'quantity' => $qnt,
                'site' => $request->company_name == 1 ? $settings == null ? '' : $settings->company_name : false,
                'name' => $request->product_name == 1 ? $product->name : false,
                'price' => $request->sale_Price == 1 ? $product->price : false,
                'currency' => $request->currencies == 1 ? 'ر.س' : false,
                'include_tax' => $request->include_tax == 1 ? true : false,
                'barcode' => $product->code,
            ];

            $data[] = $item;
        }

        return view('admin.products.print_barcode',compact('data'));
    }


    public function print_qr(){ 
        $company = CompanyInfo::first();  
        return view('admin.products.print_qr', compact('company'));
    }

    public function do_print_qr(Request $request){

        $data = [];
        foreach ($request->product_id as $index=>$id){
            $product = Product::find($id);
            $settings = SystemSettings::get()->first();
            $qnt = $request->qnt[$index];

            $text = $request->company_name == 1 ? $settings == null ? '' : $settings->company_name."\n" : '';
            $text.= $request->company_name == 1 ? $product->name."\n" : '';
            $text.= $request->sale_Price == 1 ? $product->price."\n" : '';
            $text.= $product->code;

            $item = [
                'quantity' => $qnt,
                'data' => $text,
                'name' => $product->name
            ];

            $data[] = $item;
        }

        return view('admin.products.print_qr',compact('data'));
    }

    public function getItemCode()
    {
        $items = Product::orderBy('id', 'ASC')->get();
        if (count($items) > 0) {
            $id = (int)$items[count($items) - 1]->code;
        } else{
            $id = 0;
        }
            
        $no = str_pad($id + 1, 6, '0', STR_PAD_LEFT);
        return $no; 
    }

    
    public function pos_product_list_img(Request $request)
    {
        if(!empty(Auth::user()->branch_id)) {
            $warehouses = Warehouse::select('id')->where('branch_id', Auth::user()->branch_id);
        } else{
            $warehouses = Warehouse::select('id')->where('status', 1);
        }

        $data =  Product::Join('warehouse_products','products.id','=','warehouse_products.product_id')  
            ->select('products.id','products.name','products.code','products.img')
            ->whereIn('warehouse_products.warehouse_id',$warehouses)
            ->where('products.img','<>','')
            ->get();

            if ($request->ajax()) { 
                return Datatables::of($data)->addIndexColumn()
                    ->addColumn('img', function($row){     
                        $btn = '<a href="#" class="col-md-3" id ="select-product-img"
                        value="'.$row->code.'" rol="button">
                            <img src='.env('APP_URL').'/uploads/items/images/'.$row->img.' width="100%" class="mCS_img_loaded">
                        </a>';
                        
                        return $btn; 
                    }) 
                    ->rawColumns(['img']) 
                    ->make(true);
            } 
    
    }

    public function get_product_list_img($warehouse_id)
    {
        /*
        if(!empty(Auth::user()->branch_id)) {
            $warehouses = Warehouse::select('id')->where('branch_id', Auth::user()->branch_id);
        } else{
            $warehouses = Warehouse::select('id')->where('status', 1);
        }
        */

        $data =  Product::Join('warehouse_products','products.id','=','warehouse_products.product_id')  
            ->select('products.*')
            //->whereIn('warehouse_products.warehouse_id',$warehouses)
            ->where('warehouse_products.warehouse_id',$warehouse_id)
            ->where('products.img','<>','')
            ->get();
        
        echo json_encode ($data);
        exit;
    
    }
}

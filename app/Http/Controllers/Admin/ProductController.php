<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller; 
use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\ProductUnit;
use App\Models\ProductVariant;
use App\Models\ProductTax;
use App\Models\PromotionItem;
use App\Models\PurchaseDetails;
use App\Models\SaleDetails;
use App\Models\SystemSettings;
use App\Models\Category;
use App\Models\UpdateQuntityDetails;
use App\Models\Warehouse;
use App\Models\CompanyInfo;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use DataTables;
use App\Models\WarehouseProducts as WarehouseProductModel;
use App\Models\WarehouseProducts;

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
            ->leftJoin('categories as subcategories','products.subcategory_id','=','subcategories.id')
            ->leftJoin('units','products.unit','=','units.id')
            ->leftJoin('brands','products.brand','=','brands.id')  
            ->select('products.*','units.name as unitName','brands.name as brandName',
                    'categories.name as category_name', 'subcategories.name as subcategory_name') 
            ->when(Auth::user()->subscriber_id ?? null, function($q,$sub){
                $q->where('products.subscriber_id',$sub);
            })
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
                ->addColumn('category_path', function ($row) {
                    if ($row->subcategory_name) {
                        return $row->category_name . ' > ' . $row->subcategory_name;
                    }
                    return $row->category_name;
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
        $subCategories = Category::where('parent_id', '<>', 0)
            ->orderBy('name')
            ->get()
            ->groupBy('parent_id');
        $taxRages = $systemController->getAllTaxRates();
        $taxTypes = $systemController->getAllTaxTypes();
        $settings = SystemSettings::first();
        $defaultCode = $this->getItemCode();

        return view('admin.products.create',compact('brands','categories','subCategories','taxRages','taxTypes','units','settings','defaultCode'));
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

        $validated = $request->validate(
            $this->productValidationRules(),
            $this->productValidationMessages(),
            $this->productValidationAttributes()
        );
        $siteController = new SystemController();
        $category = Category::find($request->category_id);
        $tax_excise = $category->tax_excise ?? 0;
        if ($request->filled('subcategory_id')) {
            $sub = Category::find($request->subcategory_id);
            if ($sub && $sub->tax_excise !== null) {
                $tax_excise = $sub->tax_excise;
            }
        }
        if ($request->filled('tax_excise')) {
            $tax_excise = (float) $request->tax_excise;
        }

        if($request->hasFile('img')) {
            $imageName = time() . '.' . $request->img->extension();
            $request->img->move(('uploads/items/images'), $imageName);
        } else {
            $imageName = '';
        }
       
        try
        {
            $priceLevels = [];
            for($i=1;$i<=6;$i++){
                $priceLevels['price_level_'.$i] = $request->input('price_level_'.$i,$request->price);
            }
       
            $product = Product::create([
                'code' => $request->code,
                'barcode' => $request->barcode ? trim($request->barcode) : null,
                'name' => $request->name,
                'unit' => $request->unit,
                'cost' => $request->cost,
                'price' => $request->price,
                ...$priceLevels,
                'lista' => $request->lista ?? 0,
                'alert_quantity' => $request->alert_quantity ?? 0,
                'category_id' => $request->category_id,
                'subcategory_id' => $request->subcategory_id ?? 0,
                'quantity' => $request->quantity ?? 0,
                'tax' => $request->tax ?? 0,
                'tax_rate' => $request->tax_rate,
                'track_quantity' => $request->track_quantity ?? 0,
                'tax_method' => $request->tax_method,
                'price_includes_tax' => $request->boolean('price_includes_tax'),
                'profit_margin' => $request->profit_margin,
                'tax_excise' => max($tax_excise, 0),
                'type' => $request->type,
                'brand' => $request->brand,
                'slug' => $request->slug ?? 0,
                'featured' => $request->featured ?? 0, 
                'city_tax' => $request->city_tax ?? 0,
                'max_order' => $request->max_order ?? 0,
                'img' => $imageName,
                'user_id' => Auth::user() -> id,
                'status' => $request->status ?? 1,
            ]);
            if($product){ 
                $this->syncProductTaxes($product->id, $request->input('tax_rates_multi', []));
                $this->syncProductVariants($product->id, $request->input('product_variants', []));
                $unitsTable = $request->product_units;
                if($unitsTable && is_array($unitsTable)){
                    foreach ($unitsTable as $row){
                        if(empty($row['unit']) || $row['price']===''){
                            continue;
                        }
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
                    WarehouseProductModel::create([
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
            $subCategories = Category::where('parent_id', '<>', 0)
                ->orderBy('name')
                ->get()
                ->groupBy('parent_id');
            $taxRages = $systemController->getAllTaxRates();
            $taxTypes = $systemController->getAllTaxTypes();
            $productUnits = ProductUnit::where('product_id',$id)->get();
            $productTaxes = ProductTax::where('product_id',$id)->pluck('tax_rate_id')->toArray();
            $productVariants = ProductVariant::where('product_id',$id)->get();

            return view('admin.products.update',
            compact('product','brands','categories','subCategories','taxRages','taxTypes','units','productUnits','productTaxes','productVariants'));
        }
        }

    private function syncProductTaxes(int $productId, array $taxIds): void
    {
        ProductTax::where('product_id', $productId)->delete();
        $taxIds = array_filter($taxIds);
        $rows = [];
        foreach ($taxIds as $taxId) {
            $rows[] = [
                'product_id' => $productId,
                'tax_rate_id' => $taxId,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        if (!empty($rows)) {
            ProductTax::insert($rows);
        }
    }

    private function syncProductVariants(int $productId, array $variants = []): void
    {
        ProductVariant::where('product_id', $productId)->delete();
        $variants = array_filter($variants ?? [], function ($row) {
            return !empty($row['color']) || !empty($row['size']) || !empty($row['sku']) || !empty($row['barcode']);
        });

        $totalQty = 0;
        $rows = [];
        foreach ($variants as $row) {
            $qty = isset($row['quantity']) ? (float)$row['quantity'] : 0;
            $totalQty += $qty;
            $rows[] = [
                'product_id' => $productId,
                'sku' => $row['sku'] ?? null,
                'color' => $row['color'] ?? null,
                'size' => $row['size'] ?? null,
                'barcode' => $row['barcode'] ?? null,
                'price' => $row['price'] ?? null,
                'quantity' => $qty,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (!empty($rows)) {
            ProductVariant::insert($rows);
            Product::where('id', $productId)->update(['quantity' => $totalQty]);
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
            $validated = $request->validate(
                $this->productValidationRules($product->id),
                $this->productValidationMessages(),
                $this->productValidationAttributes()
            );

            $category = Category::find($request->category_id);
            $tax_excise = $category->tax_excise ?? 0;
            if (!empty($request->subcategory_id)) {
                $subCategory = Category::find($request->subcategory_id);
                if ($subCategory && $subCategory->tax_excise !== null) {
                    $tax_excise = $subCategory->tax_excise;
                }
            }
            if ($request->filled('tax_excise')) {
                $tax_excise = (float) $request->tax_excise;
            }

            if($request->hasFile('img')) {
                $imageName = time() . '.' . $request->img->extension();
                $request->img->move(('uploads/items/images'), $imageName);
            } else {
                $imageName = $product->img;
            }

            try {
                $priceLevels = [];
                for($i=1;$i<=6;$i++){
                    $priceLevels['price_level_'.$i] = $request->input('price_level_'.$i,$request->price);
                }
                $product -> update ([
                    'code' => $request->code,
                    'barcode' => $request->barcode ? trim($request->barcode) : null,
                    'name' => $request->name,
                    'unit' => $request->unit,
                    'cost' => $request->cost,
                    'price' => $request->price,
                    ...$priceLevels,
                    'lista' => $request->lista ?? 0,
                    'alert_quantity' => $request->alert_quantity ?? 0,
                    'category_id' => $request->category_id,
                    'subcategory_id' => $request->subcategory_id ?? 0,
                    'quantity' => $request->quantity ?? 0,
                    'tax' => $request->tax ?? 0,
                    'tax_rate' => $request->tax_rate,
                    'track_quantity' => $request->track_quantity ?? 0,
                    'tax_method' => $request->tax_method,
                    'price_includes_tax' => $request->boolean('price_includes_tax'),
                    'profit_margin' => $request->profit_margin,
                    'tax_excise' => max($tax_excise, 0),
                    'type' => $request->type,
                    'brand' => $request->brand,
                    'slug' => $request->slug,
                    'featured' => $request->featured ?? 0, 
                    'city_tax' => $request->city_tax ?? 0,
                    'max_order' => $request->max_order ?? 0,
                    'img' => $imageName,
                    'user_id' => Auth::user() -> id,
                    'status' => $request->status ?? 1

                ]);
                $this->syncProductTaxes($product->id, $request->input('tax_rates_multi', []));
                $this->syncProductVariants($product->id, $request->input('product_variants', []));

                $units = ProductUnit::where('product_id' , '=' , $product -> id) -> get();
                foreach ($units as $unit){
                    $unit -> delete();
                }
                $unitsTable = $request->product_units;
                if($unitsTable && is_array($unitsTable)){
                    foreach ($unitsTable as $row){
                        if(empty($row['unit']) || $row['price']===''){
                            continue;
                        }
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
//                    WarehouseProductModel::create([
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

    private function productValidationRules(int $productId = null): array
    {
        $subscriberId = Auth::user()->subscriber_id;

        $codeRule = Rule::unique('products', 'code');
        $nameRule = Rule::unique('products', 'name');
        $barcodeRule = Rule::unique('products', 'barcode');

        if ($subscriberId) {
            $codeRule = $codeRule->where(fn ($q) => $q->where('subscriber_id', $subscriberId));
            $nameRule = $nameRule->where(fn ($q) => $q->where('subscriber_id', $subscriberId));
            $barcodeRule = $barcodeRule->where(fn ($q) => $q->where('subscriber_id', $subscriberId));
        }

        if ($productId) {
            $codeRule = $codeRule->ignore($productId);
            $nameRule = $nameRule->ignore($productId);
            $barcodeRule = $barcodeRule->ignore($productId);
        }

        $rules = [
            'code' => ['required', 'string', 'max:191', $codeRule],
            'barcode' => ['nullable', 'string', 'max:191', $barcodeRule],
            'name' => ['required', 'string', 'max:191', $nameRule],
            'unit' => ['required', 'exists:units,id'],
            'brand' => ['required', 'exists:brands,id'],
            'category_id' => ['required', 'exists:categories,id'],
            'subcategory_id' => ['nullable', 'exists:categories,id'],
            'cost' => ['required', 'numeric', 'min:0'],
            'price' => ['required', 'numeric', 'min:0'],
            'quantity' => ['nullable', 'numeric', 'min:0'],
            'tax' => ['nullable', 'numeric', 'min:0'],
            'tax_rate' => ['required', 'exists:tax_rates,id'],
            'tax_rates_multi' => ['nullable', 'array'],
            'tax_rates_multi.*' => ['integer', 'exists:tax_rates,id'],
            'tax_method' => ['required', 'in:1,2'],
            'price_includes_tax' => ['nullable', 'in:0,1'],
            'profit_margin' => ['nullable', 'numeric', 'min:0'],
            'type' => ['required', 'in:1,2,3'],
            'lista' => ['nullable', 'numeric', 'min:0'],
            'alert_quantity' => ['nullable', 'numeric', 'min:0'],
            'max_order' => ['nullable', 'numeric', 'min:0'],
            'track_quantity' => ['nullable', 'in:0,1'],
            'tax_excise' => ['nullable', 'numeric', 'min:0'],
            'slug' => ['required', 'string', 'max:191'],
            'featured' => ['nullable', 'in:0,1'],
            'city_tax' => ['nullable', 'numeric', 'min:0'],
            'status' => ['nullable', 'in:0,1'],
            'img' => ['nullable', 'image', 'max:2048'],
            'product_variants' => ['nullable', 'array'],
            'product_variants.*.sku' => ['nullable', 'string', 'max:191'],
            'product_variants.*.color' => ['nullable', 'string', 'max:191'],
            'product_variants.*.size' => ['nullable', 'string', 'max:191'],
            'product_variants.*.barcode' => ['nullable', 'string', 'max:191'],
            'product_variants.*.price' => ['nullable', 'numeric', 'min:0'],
            'product_variants.*.quantity' => ['nullable', 'numeric', 'min:0'],
            'product_units' => ['nullable', 'array'],
            'product_units.*.unit' => ['nullable', 'exists:units,id'],
            'product_units.*.price' => ['nullable', 'numeric', 'min:0'],
            'product_units.*.conversion_factor' => ['nullable', 'numeric', 'min:0.0001'],
            'product_units.*.barcode' => ['nullable', 'string', 'max:191'],
        ];

        for ($i = 1; $i <= 6; $i++) {
            $rules['price_level_' . $i] = ['nullable', 'numeric', 'min:0'];
        }

        return $rules;
    }

    private function productValidationMessages(): array
    {
        $messages = [
            'code.required' => 'يرجى إدخال كود الصنف.',
            'code.unique' => 'كود الصنف مستخدم مسبقاً.',
            'barcode.unique' => 'الباركود مستخدم مسبقاً.',
            'name.required' => 'يرجى إدخال اسم الصنف.',
            'name.unique' => 'اسم الصنف مستخدم مسبقاً.',
            'unit.exists' => 'الوحدة الأساسية المختارة غير صحيحة.',
            'brand.exists' => 'الماركة المحددة غير صالحة.',
            'category_id.exists' => 'التصنيف المختار غير صحيح.',
            'cost.numeric' => 'قيمة التكلفة يجب أن تكون رقمية.',
            'cost.min' => 'قيمة التكلفة يجب ألا تكون أقل من صفر.',
            'price.numeric' => 'سعر البيع يجب أن يكون رقمياً.',
            'price.min' => 'سعر البيع يجب ألا يكون سالباً.',
            'tax_rate.exists' => 'الضريبة المحددة غير متاحة.',
            'tax_method.in' => 'نوع الضريبة المحدد غير صحيح.',
            'type.in' => 'نوع الصنف المحدد غير صحيح.',
            'slug.required' => 'يرجى إدخال الاسم المختصر للصنف.',
            'img.image' => 'ملف الصورة يجب أن يكون من نوع صورة (JPG, PNG ...).',
            'img.max' => 'حجم الصورة يجب ألا يتجاوز 2 ميغابايت.',
            'tax_rates_multi.*.exists' => 'إحدى الضرائب الإضافية المختارة غير متاحة.',
            'product_units.*.price.numeric' => 'سعر الوحدة الإضافية يجب أن يكون رقمياً.',
            'product_units.*.conversion_factor.min' => 'معامل التحويل للوحدة الإضافية يجب أن يكون أكبر من صفر.',
            'product_units.*.unit.exists' => 'الوحدة الإضافية المختارة غير صحيحة.',
            'product_variants.*.price.numeric' => 'سعر المتغير يجب أن يكون رقمياً.',
            'product_variants.*.quantity.numeric' => 'كمية المتغير يجب أن تكون رقمية.',
        ];

        for ($i = 1; $i <= 6; $i++) {
            $messages['price_level_' . $i . '.numeric'] = 'سعر المستوى ' . $i . ' يجب أن يكون رقمياً.';
        }

        return $messages;
    }

    private function productValidationAttributes(): array
    {
        $attributes = [
            'code' => 'كود الصنف',
            'barcode' => 'باركود الصنف',
            'name' => 'اسم الصنف',
            'unit' => 'الوحدة الأساسية',
            'brand' => 'الماركة',
            'category_id' => 'التصنيف الرئيسي',
            'subcategory_id' => 'التصنيف الفرعي',
            'cost' => 'التكلفة',
            'price' => 'سعر البيع',
            'quantity' => 'الكمية',
            'tax' => 'نسبة الضريبة',
            'tax_rate' => 'ضريبة المنتج',
            'tax_method' => 'نوع الضريبة',
            'price_includes_tax' => 'سعر يشمل الضريبة',
            'profit_margin' => 'هامش الربح',
            'type' => 'نوع الصنف',
            'lista' => 'ليستا',
            'alert_quantity' => 'الكمية الحرجة',
            'max_order' => 'الحد الأعلى للطلب',
            'track_quantity' => 'متابعة الكمية',
            'slug' => 'الاسم المختصر',
            'city_tax' => 'ضريبة المدينة',
            'img' => 'صورة الصنف',
            'tax_rates_multi' => 'الضرائب الإضافية',
            'tax_rates_multi.*' => 'الضرائب الإضافية',
            'product_units.*.unit' => 'الوحدة الإضافية',
            'product_units.*.price' => 'سعر الوحدة الإضافية',
            'product_units.*.conversion_factor' => 'معامل التحويل للوحدة الإضافية',
            'product_units.*.barcode' => 'باركود الوحدة الإضافية',
            'product_variants.*.sku' => 'SKU المتغير',
            'product_variants.*.color' => 'لون المتغير',
            'product_variants.*.size' => 'مقاس المتغير',
            'product_variants.*.barcode' => 'باركود المتغير',
            'product_variants.*.price' => 'سعر المتغير',
            'product_variants.*.quantity' => 'كمية المتغير',
        ];

        for ($i = 1; $i <= 6; $i++) {
            $attributes['price_level_' . $i] = 'سعر المستوى ' . $i;
        }

        return $attributes;
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
                    $warehouseProducts = WarehouseProductModel::where('product_id', '=', $request->id)->get();
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
                ->orWhere('barcode','like','%'.$code.'%')
                ->orWhere('name','like' , '%'.$code.'%')
                ->limit(5)
                ->get()
                ->map(function($p){ return $this->appendProductMeta($p); });
            echo json_encode ($product);
            exit;
        } 
    }

    private function getSingleProduct($code){ 
        $product = Product::where(function($query) use ($code){
                $query->where('code','=',$code)
                    ->orWhere('barcode','=',$code)
                    ->orWhere('name','=',$code);
            })
            ->first(); 

        if(!$product){
            $variant = ProductVariant::where('barcode',$code)->orWhere('sku',$code)->first();
            if($variant){
                $product = Product::find($variant->product_id);
                $product = $this->appendProductMeta($product);
                $product->selected_variant = $variant;
                if(!empty($variant->price)){
                    $product->price = $variant->price;
                }
                $product->variant_color = $variant->color;
                $product->variant_size = $variant->size;
                return $product;
            }

            // special barcode for promotions
            $promoItem = PromotionItem::where('special_barcode',$code)->first();
            if($promoItem){
                $product = Product::find($promoItem->product_id);
                $product = $this->appendProductMeta($product);
                $product->selected_variant = $promoItem->variant_id ? ProductVariant::find($promoItem->variant_id) : null;
                $product->variant_color = $promoItem->variant_color;
                $product->variant_size = $promoItem->variant_size;
                return $product;
            }
        }

        return $this->appendProductMeta($product);
    }
    
    private function appendProductMeta($product){
        if(!$product){
            return null;
        }

        $product->last_sale_price = $this->getLastSalePrice($product->id);
        $product->units_options = $this->getUnitsForProduct($product->id,$product);
        $product->variants = ProductVariant::where('product_id',$product->id)->get();
        $product->total_tax_rate = $product->totalTaxRate();
        $product->promo_discount_unit = $this->getPromotionDiscountPreview($product);

        return $product;
    }

    private function getPromotionDiscountPreview($product): float
    {
        $today = now()->toDateString();
        $branchId = auth()->user()->branch_id ?? null;
        $promoItem = PromotionItem::query()
            ->join('promotions','promotions.id','=','promotion_items.promotion_id')
            ->where('promotions.status','active')
            ->where(function($q) use ($today){
                $q->whereNull('promotions.start_date')->orWhere('promotions.start_date','<=',$today);
            })
            ->where(function($q) use ($today){
                $q->whereNull('promotions.end_date')->orWhere('promotions.end_date','>=',$today);
            })
            ->when($branchId,function($q) use ($branchId){
                $q->where(function($qq) use ($branchId){
                    $qq->whereNull('promotions.branch_id')->orWhere('promotions.branch_id',$branchId);
                });
            })
            ->where('promotion_items.product_id',$product->id)
            ->first(['promotion_items.*']);

        if(!$promoItem){
            return 0;
        }

        if($promoItem->discount_type === 'amount'){
            return (float)$promoItem->discount_value;
        }

        return $product->price * ($promoItem->discount_value/100);
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
            $baseUnitName = optional($product->units)->name;
            if(empty($baseUnitName) && $product->unit){
                $baseUnitName = optional(\App\Models\Unit::find($product->unit))->name;
            }
            return [[
                'unit_id' => $product->unit,
                'unit_name' => $baseUnitName ?: '',
                'price' => $product->price,
                'conversion_factor' => 1,
                'barcode' => null
            ]];
        }

        return $units->map(function($u){
            $unitName = $u->unit_name;
            if(empty($unitName)){
                $unitName = optional(\App\Models\Unit::find($u->unit_id))->name;
            }
            return [
                'unit_id' => $u->unit_id,
                'unit_name' => $unitName ?: '',
                'price' => $u->price,
                'conversion_factor' => $u->conversion_factor ?? 1,
                'barcode' => $u->barcode
            ];
        });
    }

    public function get_product_warehouse($warehouse_id,$code)
    {
        $results = $this->searchWarehouseProducts($warehouse_id, $code, true);

        if ($results->isEmpty()) {
            $results = $this->searchWarehouseProducts($warehouse_id, $code, false, 5);
        }

        if ($results->isEmpty()) {
            $results = $this->searchProductsWithoutWarehouse($code, true);
        }

        echo json_encode($results->values());
        exit;
    }

    private function searchWarehouseProducts($warehouseId, $code, bool $exact = true, ?int $limit = null)
    {
        $query = Product::with('units')
            ->join('warehouse_products', 'products.id', '=', 'warehouse_products.product_id')
            ->select('products.*', 'warehouse_products.quantity as qty')
            ->where('warehouse_products.warehouse_id', $warehouseId)
            ->where('products.status', 1);

        $this->applyProductSearchFilters($query, $code, $exact);

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get()->map(function ($product) {
            return $this->appendProductMeta($product);
        })->filter();
    }

    private function searchProductsWithoutWarehouse($code, bool $exact = true, ?int $limit = null)
    {
        $query = Product::with('units')
            ->where('products.status', 1);

        $this->applyProductSearchFilters($query, $code, $exact);

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get()->map(function ($product) {
            $product->qty = $product->quantity ?? 0;
            return $this->appendProductMeta($product);
        })->filter();
    }

    private function applyProductSearchFilters($query, $code, bool $exact): void
    {
        $value = $exact ? $code : '%' . $code . '%';

        $query->where(function ($q) use ($value, $exact, $code) {
            if ($exact) {
                $q->where('products.code', '=', $value)
                    ->orWhere('products.barcode', '=', $value)
                    ->orWhere('products.name', '=', $value);
            } else {
                $q->where('products.code', 'like', $value)
                    ->orWhere('products.barcode', 'like', $value)
                    ->orWhere('products.name', 'like', $value);
            }

            $q->orWhereExists(function ($sub) use ($value, $exact) {
                $sub->select(DB::raw(1))
                    ->from('product_units')
                    ->whereColumn('product_units.product_id', 'products.id');

                if ($exact) {
                    $sub->where('product_units.barcode', '=', $value);
                } else {
                    $sub->where('product_units.barcode', 'like', $value);
                }
            })
            ->orWhereExists(function ($sub) use ($value, $exact) {
                $sub->select(DB::raw(1))
                    ->from('product_variants')
                    ->whereColumn('product_variants.product_id', 'products.id')
                    ->where(function ($inner) use ($value, $exact) {
                        if ($exact) {
                            $inner->where('product_variants.barcode', '=', $value)
                                  ->orWhere('product_variants.sku', '=', $value);
                        } else {
                            $inner->where('product_variants.barcode', 'like', $value)
                                  ->orWhere('product_variants.sku', 'like', $value);
                        }
                    });
            })
            ->orWhereExists(function ($sub) use ($code) {
                $sub->select(DB::raw(1))
                    ->from('promotion_items')
                    ->whereColumn('promotion_items.product_id', 'products.id')
                    ->where('promotion_items.special_barcode', '=', $code);
            });
        });
    }

    public function print_barcode(){
        $company = CompanyInfo::first();  
        return view('admin.products.print_barcode', compact('company'));
    }

    public function do_print_barcode(Request $request){

        $data = [];
        $settings = SystemSettings::query()->first();
        $company = CompanyInfo::first();
        $currencyLabel = optional($settings)->currency_label ?? 'ر.س';
        $companyName = optional($settings)->company_name ?? '';

        foreach ((array) $request->product_id as $index => $identifier){
            $product = $this->resolvePrintableProduct($identifier);
            if (! $product) {
                continue;
            }

            $quantity = max(1, (int) ($request->qnt[$index] ?? 1));
            $barcodeValue = $this->resolveBarcodeValue($product, $identifier);

            $item = [
                'quantity' => $quantity,
                'site' => $request->company_name == 1 ? $companyName : false,
                'name' => $request->product_name == 1 ? $product->name : false,
                'price' => $request->sale_Price == 1 ? $product->price : false,
                'currency' => $request->currencies == 1 ? $currencyLabel : false,
                'include_tax' => (bool) ($request->include_tax ?? false),
                'barcode' => $barcodeValue,
            ];

            $data[] = $item;
        }

        return view('admin.products.print_barcode',compact('data','company'));
    }


    public function print_qr(){ 
        $company = CompanyInfo::first();  
        return view('admin.products.print_qr', compact('company'));
    }

    public function do_print_qr(Request $request){

        $data = [];
        $settings = SystemSettings::query()->first();
        $company = CompanyInfo::first();
        $companyName = optional($settings)->company_name ?? '';
        $currencyLabel = optional($settings)->currency_label ?? 'ر.س';

        foreach ((array) $request->product_id as $index => $identifier){
            $product = $this->resolvePrintableProduct($identifier);
            if (! $product) {
                continue;
            }

            $quantity = max(1, (int) ($request->qnt[$index] ?? 1));
            $codeValue = $this->resolveBarcodeValue($product, $identifier);

            $text = $codeValue . "\n";
            if ($request->company_name == 1 && $companyName) {
                $text .= $companyName . "\n";
            }
            if ($request->product_name == 1) {
                $text .= $product->name . "\n";
            }
            if ($request->sale_Price == 1) {
                $text .= $request->currencies == 1
                    ? 'السعر: ' . $product->price . ' ' . $currencyLabel
                    : 'السعر: ' . $product->price;
            }

            $item = [
                'quantity' => $quantity,
                'data' => trim($text),
                'name' => $product->name,
                'code' => $codeValue,
                'price' => $product->price,
                'currency' => $currencyLabel,
            ];

            $data[] = $item;
        }

        return view('admin.products.print_qr',compact('data','company'));
    }

    private function resolvePrintableProduct($identifier): ?Product
    {
        if ($identifier === null || $identifier === '') {
            return null;
        }

        $product = Product::find($identifier);

        if (! $product) {
            $product = Product::where('code', $identifier)->first();
        }

        if ($product) {
            return $product;
        }

        $variant = ProductVariant::where('barcode', $identifier)
            ->orWhere('sku', $identifier)
            ->first();

        return $variant ? Product::find($variant->product_id) : null;
    }

    private function resolveBarcodeValue(Product $product, $identifier): string
    {
        if ($identifier !== null && $identifier !== '' && (string) $product->code !== (string) $identifier) {
            return (string) $identifier;
        }

        return (string) $product->code;
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

    public function locations($id)
    {
        $product = Product::findOrFail($id);
        $salesLast = SaleDetails::join('sales','sales.id','=','sale_details.sale_id')
            ->where('sale_details.product_id',$id)
            ->select('sale_details.price_unit','sales.warehouse_id')
            ->orderByDesc('sale_details.id')
            ->get()
            ->groupBy('warehouse_id')
            ->map(function($group){ return $group->first()->price_unit; });

        $locations = WarehouseProductModel::query()
            ->join('warehouses','warehouses.id','=','warehouse_products.warehouse_id')
            ->select('warehouses.name as warehouse_name','warehouse_products.quantity','warehouse_products.cost','warehouse_products.warehouse_id')
            ->where('warehouse_products.product_id',$id)
            ->get()
            ->map(function($row) use ($salesLast){
                $row->last_sale_price = $salesLast[$row->warehouse_id] ?? 0;
                return $row;
            });

        return response()->json([
            'product' => $product->only(['id','name','code']),
            'locations' => $locations,
        ]);
    }
}

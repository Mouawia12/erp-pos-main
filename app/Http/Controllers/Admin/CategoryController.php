<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller; 
use App\Models\Brand;
use App\Models\Category;
use App\Models\TaxExcise;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Category::all();
        $cats = Category::where('status',1) -> get();
        $tax_excises = TaxExcise::where('status',1) -> get();
        return view ('admin.Category.index' , ['categories' => $categories , 'cats' => $cats,'tax_excises' => $tax_excises]);
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
     * @param  \App\Http\Requests\StoreCategoryRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       if($request -> id == 0){
           if($request -> image_url){
               $imageName = time().'.'.$request->image_url->extension();
               $request->image_url->move(('uploads/images/Category'), $imageName);
           } else {
               $imageName = '' ;
           }
           $validated = $request->validate([ 
               'name' => 'required',
           ]);
           try {
               Category::create([
                   'name' => $request -> name ,
                   'code' => $request -> code ?? '' ,
                   'slug' => $request -> slug ?? '' ,
                   'description' => $request -> description ?? '',
                   'image_url' => $imageName ,
                   'parent_id' => $request -> parent_id,
                   'tax_excise' => $request->tax_excise ?? 0,
                   'user_id' => Auth::user() -> id
               ]);
               return redirect()->route('categories')->with('success' , __('main.created'));
           } catch (QueryException $ex){
               return redirect()->route('categories')->with('error' ,  $ex->getMessage());
           }


       } else {
          return $this -> update($request);
       }


    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $category = Category::find($id);
        echo json_encode ($category);
        exit;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCategoryRequest  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $category = Category::find($request -> id);
        if($category){
            if($request -> image_url){
                $imageName = time().'.'.$request->image_url->extension();
                $request->image_url->move(('uploads/images/Category'), $imageName);
            } else {
                $imageName = $category ->  image_url;
            }
            $validated = $request->validate([ 
                'name' => 'required',
            ]);

            try {
                $category -> update([
                    'name' => $request -> name ,
                    'code' => $request -> code ?? '',
                    'slug' => $request -> slug ?? '' ,
                    'description' => $request -> description ?? '',
                    'image_url' => $imageName ?? '' ,
                    'parent_id' => $request -> parent_id,
                    'tax_excise' => $request->tax_excise ?? 0,
                    'user_id' => Auth::user() -> id
                ]);
                return redirect()->route('categories')->with('success' , __('main.updated'));
            } catch (QueryException $ex){
                return redirect()->route('categories')->with('error' ,  $ex->getMessage());
            }
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $category = Category::find($id);
        if($category){
            $category -> delete();
            return redirect()->route('categories')->with('success' , __('main.deleted'));
        }
    }
}

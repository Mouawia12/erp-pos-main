<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompanyInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CompanyInfoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $info = CompanyInfo::all() -> first();
        if(isset($info)){
            return view('admin.CompanyInfo.CompanyInfo' , compact('info' ));
        } else {
            $info = null ;
            return view('admin.CompanyInfo.CompanyInfo' , compact('info' ));
        }
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
        if($request -> id == 0) {
            if ($request->image_url) {
                $imageName = time() . '.' . $request->image_url->extension();
                $request->image_url->move(('uploads/profiles/'), $imageName);
            } else {
                $imageName = '';
            }

            CompanyInfo::create([
                'name_ar' => $request -> name_ar,
                'name_en'=> $request -> name_en,
                'faild_ar' => $request -> faild_ar,
                'faild_en'=> $request -> faild_en,
                'phone' => $request -> phone,
                'phone2' => $request -> phone2,
                'fax' => $request -> fax,
                'email' => $request -> email,
                'website' => $request -> website,
                'taxNumber' => $request -> taxNumber,
                'registrationNumber' => $request -> registrationNumber,
                'address' => $request -> address,
                'currency_ar' => $request -> currency_ar,
                'currency_en'=> $request -> currency_en,
                'currency_label' => $request -> currency_label,
                'currency_label_en' => $request -> currency_label_en,
                'logo' => $imageName,
                'user_id' => Auth::user() -> id
            ]);

            return redirect() -> route('companyInfo') ->with('success' , __('main.created'));
        } else {
            return   $this->update($request);
        }



    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CompanyInfo  $companyInfo
     * @return \Illuminate\Http\Response
     */
    public function show(CompanyInfo $companyInfo)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CompanyInfo  $companyInfo
     * @return \Illuminate\Http\Response
     */
    public function edit(CompanyInfo $companyInfo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CompanyInfo  $companyInfo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $info = CompanyInfo::find($request -> id);
        if($info){
            if($request -> image_url){
                $imageName = time().'.'.$request->image_url->extension();
                $request->image_url->move(('uploads/profiles/'), $imageName);
            } else {
                $imageName = $info ->  image_url;
            }
            $info -> update ([
                'name_ar' => $request -> name_ar,
                'name_en'=> $request -> name_en,
                'phone' => $request -> phone,
                'phone2' => $request -> phone2,
                'fax' => $request -> fax,
                'email' => $request -> email,
                'website' => $request -> website,
                'taxNumber' => $request -> taxNumber,
                'registrationNumber' => $request -> registrationNumber,
                'address' => $request -> address,
                'currency_ar' => $request -> currency_ar,
                'currency_en'=> $request -> currency_en,
                'currency_label' => $request -> currency_label,
                'currency_label_en' => $request -> currency_label_en,
                'logo' => $imageName,
                'user_id' => Auth::user() -> id
            ]);
            return redirect() -> route('companyInfo') ->with('success' , __('main.updated'));


        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CompanyInfo  $companyInfo
     * @return \Illuminate\Http\Response
     */
    public function destroy(CompanyInfo $companyInfo)
    {
        //
    }

    public function testBar(){
        return view('Item.test');
    }
}

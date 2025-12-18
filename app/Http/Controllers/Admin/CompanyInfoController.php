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
        $rules = [
            'name_ar' => ['required', 'string', 'max:191'],
            'name_en' => ['required', 'string', 'max:191'],
            'faild_ar' => ['required', 'string', 'max:191'],
            'faild_en' => ['required', 'string', 'max:191'],
            'phone' => ['required', 'string', 'max:50'],
            'phone2' => ['nullable', 'string', 'max:50'],
            'fax' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:191'],
            'website' => ['nullable', 'string', 'max:191'],
            'taxNumber' => ['nullable', 'string', 'max:191'],
            'registrationNumber' => ['nullable', 'string', 'max:191'],
            'address' => ['nullable', 'string'],
            'currency_ar' => ['required', 'string', 'max:50'],
            'currency_en' => ['required', 'string', 'max:50'],
            'currency_label' => ['required', 'string', 'max:50'],
            'currency_label_en' => ['required', 'string', 'max:50'],
            'image_url' => ['nullable', 'image', 'max:2048'],
            'id' => ['nullable', 'integer'],
        ];

        $validated = $request->validate($rules);

        if($request -> id == 0) {
            $imageName = '';
            if ($request->hasFile('image_url')) {
                $imageName = time() . '.' . $request->image_url->extension();
                $request->image_url->move(('uploads/profiles/'), $imageName);
            }

            CompanyInfo::create([
                'name_ar' => $validated['name_ar'],
                'name_en'=> $validated['name_en'],
                'faild_ar' => $validated['faild_ar'],
                'faild_en'=> $validated['faild_en'],
                'phone' => $validated['phone'],
                'phone2' => $validated['phone2'] ?? null,
                'fax' => $validated['fax'] ?? null,
                'email' => $validated['email'] ?? null,
                'website' => $validated['website'] ?? null,
                'taxNumber' => $validated['taxNumber'] ?? null,
                'registrationNumber' => $validated['registrationNumber'] ?? null,
                'address' => $validated['address'] ?? null,
                'currency_ar' => $validated['currency_ar'],
                'currency_en'=> $validated['currency_en'],
                'currency_label' => $validated['currency_label'],
                'currency_label_en' => $validated['currency_label_en'],
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
        $rules = [
            'name_ar' => ['required', 'string', 'max:191'],
            'name_en' => ['required', 'string', 'max:191'],
            'faild_ar' => ['required', 'string', 'max:191'],
            'faild_en' => ['required', 'string', 'max:191'],
            'phone' => ['required', 'string', 'max:50'],
            'phone2' => ['nullable', 'string', 'max:50'],
            'fax' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:191'],
            'website' => ['nullable', 'string', 'max:191'],
            'taxNumber' => ['nullable', 'string', 'max:191'],
            'registrationNumber' => ['nullable', 'string', 'max:191'],
            'address' => ['nullable', 'string'],
            'currency_ar' => ['required', 'string', 'max:50'],
            'currency_en' => ['required', 'string', 'max:50'],
            'currency_label' => ['required', 'string', 'max:50'],
            'currency_label_en' => ['required', 'string', 'max:50'],
            'image_url' => ['nullable', 'image', 'max:2048'],
            'id' => ['required', 'integer', 'exists:company_infos,id'],
        ];

        $validated = $request->validate($rules);

        $info = CompanyInfo::find($request -> id);
        if($info){
            $imageName = $info->image_url ?? $info->logo ?? '';
            if($request -> image_url){
                $imageName = time().'.'.$request->image_url->extension();
                $request->image_url->move(('uploads/profiles/'), $imageName);
            }
            $info -> update ([
                'name_ar' => $validated['name_ar'],
                'name_en'=> $validated['name_en'],
                'faild_ar' => $validated['faild_ar'],
                'faild_en'=> $validated['faild_en'],
                'phone' => $validated['phone'],
                'phone2' => $validated['phone2'] ?? null,
                'fax' => $validated['fax'] ?? null,
                'email' => $validated['email'] ?? null,
                'website' => $validated['website'] ?? null,
                'taxNumber' => $validated['taxNumber'] ?? null,
                'registrationNumber' => $validated['registrationNumber'] ?? null,
                'address' => $validated['address'] ?? null,
                'currency_ar' => $validated['currency_ar'],
                'currency_en'=> $validated['currency_en'],
                'currency_label' => $validated['currency_label'],
                'currency_label_en' => $validated['currency_label_en'],
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

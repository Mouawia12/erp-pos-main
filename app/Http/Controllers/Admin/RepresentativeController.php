<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller; 
use App\Models\Company;
use App\Models\Representative;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class RepresentativeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $representatives = Representative::all();
        return view('representatives.index' , compact('representatives'));
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

        $subscriberId = Auth::user()->subscriber_id;

        if ($request -> id == 0) {
            $validated = $request->validate([
                'code' => 'required',
                'name' => 'required',
                'user_name' => [
                    'required',
                    Rule::unique('representatives', 'user_name')->where(function ($query) use ($subscriberId) {
                        if ($subscriberId) {
                            $query->where('subscriber_id', $subscriberId);
                        }
                    }),
                ],
                'password' => 'required',
                'document_name' => 'nullable|string|max:191',
                'document_number' => 'nullable|string|max:191',
                'document_expiry_date' => 'nullable|date',
            ]);
            try {
                Representative::create([
                    'code' => $request -> code,
                    'name' => $request -> name,
                    'user_name' => $request -> user_name,
                    'password' => $request -> password,
                    'notes' => '',
                    'active' => 1,
                    'document_name' => $request->document_name,
                    'document_number' => $request->document_number,
                    'document_expiry_date' => $request->document_expiry_date,
                    'subscriber_id' => $subscriberId,
                ]);
                return redirect()->route('representatives')->with('success' , __('main.created'));
            } catch(QueryException $ex){

                return redirect()->route('representatives')->with('error' ,  $ex->getMessage());
            }
        } else {
            return  $this -> update($request);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Representative  $representative
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $clients = Company::all();
        echo json_encode($clients);
        exit();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Representative  $representative
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $subscriberId = Auth::user()->subscriber_id;
        $user = Representative::query()
            ->when($subscriberId, fn($q) => $q->where('subscriber_id', $subscriberId))
            ->findOrFail($request -> id);
        if($user){
            $validated = $request->validate([
                'code' => 'required',
                'name' => 'required',
                'user_name' => [
                    'required',
                    Rule::unique('representatives', 'user_name')
                        ->ignore($request -> id)
                        ->where(function ($query) use ($subscriberId) {
                            if ($subscriberId) {
                                $query->where('subscriber_id', $subscriberId);
                            }
                        }),
                ],
                'password' => 'required',
                'document_name' => 'nullable|string|max:191',
                'document_number' => 'nullable|string|max:191',
                'document_expiry_date' => 'nullable|date',
            ]);
            try {
                $user -> update([
                    'code' => $request -> code,
                    'name' => $request -> name,
                    'user_name' => $request -> user_name,
                    'password' => $request -> password,
                    'notes' => '',
                    'active' => 1,
                    'document_name' => $request->document_name,
                    'document_number' => $request->document_number,
                    'document_expiry_date' => $request->document_expiry_date,
                ]);
                return redirect()->route('representatives')->with('success' , __('main.created'));
            } catch(QueryException $ex){

                return redirect()->route('representatives')->with('error' ,  $ex->getMessage());
            }


        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Representative  $representative
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $subscriberId = Auth::user()->subscriber_id;
        $user = Representative::query()
            ->when($subscriberId, fn($q) => $q->where('subscriber_id', $subscriberId))
            ->findOrFail($id);
        echo json_encode($user);
        exit();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Representative  $representative
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $subscriberId = Auth::user()->subscriber_id;
        $rep = Representative::query()
            ->when($subscriberId, fn($q) => $q->where('subscriber_id', $subscriberId))
            ->findOrFail($id);
        if($rep) {
            $clients = Company::where('representative_id_', '=', $id)->get();
            foreach ($clients as $client) {
                $client->representative_id_ = 0;
                $client->update();
            }
            $rep -> delete();
            return redirect()->route('representatives')->with('success' , __('main.deleted'));
        }



    }
    public function connect_to_client(Request  $request){
        $subscriberId = Auth::user()->subscriber_id;
        $client = Company::query()
            ->when($subscriberId, fn($q) => $q->where('subscriber_id', $subscriberId))
            ->find($request -> client);
        $rep = Representative::query()
            ->when($subscriberId, fn($q) => $q->where('subscriber_id', $subscriberId))
            ->find($request -> rep);
        if($client && $rep){
            $client -> representative_id_ =  $rep -> id ;
            $client -> update();
            return redirect()->route('representatives')->with('success' , __('main.done'));
        }
    }
    public function disconnectClientRep($id){
        $subscriberId = Auth::user()->subscriber_id;
        $client = Company::query()
            ->when($subscriberId, fn($q) => $q->where('subscriber_id', $subscriberId))
            ->find($id);
        if($client){
            $client -> representative_id_ =  0 ;
            $client -> update();
            return redirect()->route('representatives')->with('success' , __('main.done'));
        }
    }
}

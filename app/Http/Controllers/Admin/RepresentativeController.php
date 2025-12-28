<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller; 
use App\Models\Company;
use App\Models\Representative;
use App\Models\RepresentativeDocument;
use App\Models\Warehouse;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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
        $subscriberId = Auth::user()->subscriber_id;
        $representatives = Representative::query()
            ->with(['documents', 'warehouse'])
            ->when($subscriberId, fn($q) => $q->where('subscriber_id', $subscriberId))
            ->get();
        $warehouses = Warehouse::query()
            ->when(Auth::user()->branch_id ?? null, fn($q,$v) => $q->where('branch_id', $v))
            ->orderBy('name')
            ->get();
        return view('representatives.index' , compact('representatives', 'warehouses'));
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
                'warehouse_id' => 'nullable|exists:warehouses,id',
                'price_level_id' => 'nullable|integer|min:1|max:6',
                'profit_margin' => 'nullable|numeric|min:0',
                'discount_percent' => 'nullable|numeric|min:0|max:100',
                'create_warehouse' => 'nullable|in:0,1',
            ]);
            try {
                $warehouseId = $request->warehouse_id;
                if (!$warehouseId && $request->boolean('create_warehouse')) {
                    $warehouse = Warehouse::create([
                        'code' => $request->code,
                        'name' => 'مستودع - ' . $request->name,
                        'phone' => '',
                        'email' => '',
                        'address' => '',
                        'tax_number' => '',
                        'commercial_registration' => '',
                        'serial_prefix' => null,
                        'branch_id' => Auth::user()->branch_id ?? 0,
                        'user_id' => Auth::id(),
                        'status' => 1,
                        'subscriber_id' => $subscriberId,
                    ]);
                    $warehouseId = $warehouse->id;
                }

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
                    'warehouse_id' => $warehouseId,
                    'price_level_id' => $request->price_level_id,
                    'profit_margin' => $request->profit_margin,
                    'discount_percent' => $request->discount_percent,
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
                'warehouse_id' => 'nullable|exists:warehouses,id',
                'price_level_id' => 'nullable|integer|min:1|max:6',
                'profit_margin' => 'nullable|numeric|min:0',
                'discount_percent' => 'nullable|numeric|min:0|max:100',
                'create_warehouse' => 'nullable|in:0,1',
            ]);
            try {
                $warehouseId = $request->warehouse_id;
                if (!$warehouseId && $request->boolean('create_warehouse')) {
                    $warehouse = Warehouse::create([
                        'code' => $request->code,
                        'name' => 'مستودع - ' . $request->name,
                        'phone' => '',
                        'email' => '',
                        'address' => '',
                        'tax_number' => '',
                        'commercial_registration' => '',
                        'serial_prefix' => null,
                        'branch_id' => Auth::user()->branch_id ?? 0,
                        'user_id' => Auth::id(),
                        'status' => 1,
                        'subscriber_id' => $subscriberId,
                    ]);
                    $warehouseId = $warehouse->id;
                }

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
                    'warehouse_id' => $warehouseId,
                    'price_level_id' => $request->price_level_id,
                    'profit_margin' => $request->profit_margin,
                    'discount_percent' => $request->discount_percent,
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

    public function storeDocument(Request $request, Representative $representative)
    {
        $subscriberId = Auth::user()->subscriber_id;
        if ($subscriberId && $representative->subscriber_id !== $subscriberId) {
            return redirect()->route('representatives')->with('error', __('main.could_not_find_record'));
        }

        $validated = $request->validate([
            'document' => ['required', 'file', 'max:4096'],
            'title' => ['nullable', 'string', 'max:191'],
            'document_type' => ['nullable', 'string', 'max:191'],
            'expiry_date' => ['nullable', 'date'],
        ]);

        $path = $request->file('document')->store('representatives', 'public');
        RepresentativeDocument::create([
            'representative_id' => $representative->id,
            'title' => $validated['title'] ?? $request->file('document')->getClientOriginalName(),
            'document_type' => $validated['document_type'] ?? null,
            'expiry_date' => $validated['expiry_date'] ?? null,
            'file_path' => $path,
            'uploaded_by' => Auth::id(),
        ]);

        return redirect()->route('representatives')->with('success', __('main.created'));
    }

    public function deleteDocument(RepresentativeDocument $document)
    {
        $subscriberId = Auth::user()->subscriber_id;
        if ($subscriberId && optional($document->representative)->subscriber_id !== $subscriberId) {
            return redirect()->route('representatives')->with('error', __('main.could_not_find_record'));
        }

        if ($document->file_path) {
            Storage::disk('public')->delete($document->file_path);
        }
        $document->delete();

        return redirect()->route('representatives')->with('success', __('main.deleted'));
    }
}

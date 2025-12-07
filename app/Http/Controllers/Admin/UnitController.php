<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller; 
use App\Models\Unit;
use App\Http\Requests\StoreUnitRequest;
use App\Http\Requests\UpdateUnitRequest;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $units = Unit::all(); 
        return view ('admin.Units.index' , ['units' => $units] );
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
     * @param  \App\Http\Requests\StoreUnitRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $expectsJson = $request->expectsJson();
        if($request -> id == 0){
            $subscriberId = Auth::user()->subscriber_id ?? null;
            $code = $request->input('code') ?: $this->nextUnitCode($subscriberId);
            $request->merge(['code' => $code]);

            $codeRule = Rule::unique('units', 'code');
            if ($subscriberId) {
                $codeRule = $codeRule->where(fn($query) => $query->where('subscriber_id', $subscriberId));
            }

            $validated = $request->validate([
                'code' => ['required', 'string', $codeRule],
                'name_ar' => 'required|string|max:255',
                'name_en' => 'required|string|max:255',
                'name' => 'nullable|string|max:255',
            ]);
            try {
                $unit = Unit::create([
                    'code' => $request->code,
                    'name' => $request->name ?? $request->name_ar ?? $request->name_en,
                    'name_ar' => $request->name_ar,
                    'name_en' => $request->name_en,
                ]);
                if ($expectsJson) {
                    return response()->json([
                        'message' => __('main.created'),
                        'unit' => $unit,
                    ], 201);
                }
                return redirect()->route('units')->with('success' , __('main.created'));
            } catch(QueryException $ex){ 
                if ($expectsJson) {
                    return response()->json([
                        'message' => $ex->getMessage(),
                    ], 422);
                }
                return redirect()->route('units')->with('error' ,  $ex->getMessage());
            }
        } else {
            return  $this -> update($request);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Unit  $unit
     * @return \Illuminate\Http\Response
     */
    public function show(Unit $unit)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Unit  $unit
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $unit = Unit::find($id);
        echo json_encode ($unit);
        exit;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateUnitRequest  $request
     * @param  \App\Models\Unit  $unit
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $expectsJson = $request->expectsJson();
        $unit = Unit::find($request -> id);
        if($unit) {
            $subscriberId = Auth::user()->subscriber_id ?? null;
            $codeRule = Rule::unique('units', 'code')->ignore($unit->id);
            if ($subscriberId) {
                $codeRule = $codeRule->where(fn($query) => $query->where('subscriber_id', $subscriberId));
            }

            if (! $request->input('code')) {
                $request->merge(['code' => $unit->code]);
            }

            $validated = $request->validate([
                'code' => ['required', 'string', $codeRule],
                'name_ar' => 'required|string|max:255',
                'name_en' => 'required|string|max:255',
                'name' => 'nullable|string|max:255',
            ]);
            try {
                $unit -> update([ 
                    'code' => $request->code,
                    'name' => $request->name ?? $request->name_ar ?? $request->name_en,
                    'name_ar' => $request->name_ar,
                    'name_en' => $request->name_en,
                ]);
                if ($expectsJson) {
                    return response()->json([
                        'message' => __('main.updated'),
                        'unit' => $unit,
                    ]);
                }
                return redirect()->route('units' , $request -> isGold)->with('success', __('main.updated'));
            } catch (QueryException $ex) { 
                if ($expectsJson) {
                    return response()->json([
                        'message' => $ex->getMessage(),
                    ], 422);
                }
                return redirect()->route('units' , $request -> isGold)->with('error', $ex->getMessage());
            }
        } elseif ($expectsJson) {
            return response()->json([
                'message' => __('main.not_found'),
            ], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Unit  $unit
     * @return \Illuminate\Http\Response
     */
    public function destroy( $id)
    {
        $unit = Unit::find($id);
        if($unit){
            $unit -> delete();
            return redirect()->route('units' , $unit -> isGold)->with('success', __('main.deleted'));
        }
    }

    private function nextUnitCode(?int $subscriberId): int
    {
        $query = Unit::query();
        if ($subscriberId) {
            $query->where('subscriber_id', $subscriberId);
        }

        $maxCode = $query->max(DB::raw('CAST(code AS UNSIGNED)'));

        return ((int) $maxCode) + 1;
    }
}

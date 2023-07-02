<?php

namespace App\Http\Controllers;

use App\Http\Requests\KriteriaRequest;
use App\Models\Kriteria;
use Illuminate\Http\Request;

class KriteriaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $data = Kriteria::all(['id', 'nama_kriteria', 'tipe', 'bobot']);
        $total_bobot_exist = (float)Kriteria::all()->sum('bobot');
        return response()->json([
            "status" => 200,
            "total_bobot_exist" => round($total_bobot_exist, 2),
            "data" => $data
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
        $kriteria_exist = Kriteria::all(['id', 'nama_kriteria', 'tipe', 'bobot'])->where('id', $id)->first();
        if (!$kriteria_exist) {
            return response()->json([
                "status" => 400,
                "messages" => "Kriteria doesn't exist"
            ], 400);
        }
        $total_bobot_exist = Kriteria::all()->sum('bobot');
        $kriteria_exist['max_bobot'] = $total_bobot_exist != 1 ? round(1 - (float)Kriteria::whereNot("id", $id)->sum('bobot'), 2) : $kriteria_exist->bobot;
        return response()->json([
            "status" => 200,
            "data" => $kriteria_exist
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id, KriteriaRequest $kriteriaRequestt)
    {
        //
        $kriteria_exist = Kriteria::where("id", $id)->first();
        if (!$kriteria_exist) {
            return response()->json([
                "status" => 400,
                "messages" => "Criteria doesn't exist!"
            ], 400);
        }

        $validated = $kriteriaRequestt->validated();

        $updated = $kriteria_exist->update($validated);
        if (!$updated) {
            return response()->json([
                "status" => 400,
                "messages" => "There is problem in updating process"
            ], 400);
        }
        return response()->json([
            "status" => 200,
            "messages" => "Criteria $kriteria_exist->nama_kriteria has been updated!"
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kriteria $kriteria)
    {
        //
    }
}

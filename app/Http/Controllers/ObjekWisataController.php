<?php

namespace App\Http\Controllers;

use App\Http\Requests\ObjekWisataRequest;
use App\Models\NilaiObjekWisata;
use App\Models\ObjekWisata;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ObjekWisataController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $data = NilaiObjekWisataController::moora_calculate();
        $cleaning_data = [];
        foreach ($data as $d) {
            $cleaning_data[] = $d;
        }
        return response()->json([
            "status" => 200,
            "data" => $cleaning_data,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ObjekWisataRequest $objekWisataRequest)
    {
        $filename = '';
        $validated = $objekWisataRequest->validated();
        $wisata_exist = ObjekWisata::where('nama', $validated['nama'])->where('kab_kota', $validated['kab_kota'])->where('provinsi', $validated['provinsi'])->first();

        if ($wisata_exist) {
            return response()->json([
                "status" => 400,
                "messages" => "Objek wisata has already taken",
            ], 400);
        }

        foreach ($objekWisataRequest['foto'] as $file) {
            $filename .= Storage::disk('objek_wisata')->put('', $file);
            if (array_search($file, $objekWisataRequest['foto']) != count($objekWisataRequest['foto']) - 1) {
                $filename .= ' | ';
            }
        }

        $validated['foto'] = $filename;

        ObjekWisata::create($validated);
        return response()->json([
            "status" => 201,
            "messages" => "Objek wisata " . $validated['nama'] . " berhasil ditambah",
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $wisata = ObjekWisata::all(['id', 'nama', 'deskripsi', 'alamat_lengkap', 'kab_kota', 'provinsi', 'fasilitas', 'foto'])->where('id', $id)->first();
        $wisata['foto'] = explode(' | ', $wisata['foto']);
        if (!$wisata) {
            return response()->json([
                "status" => 404,
                "message" => "Objek wisata not found",
            ], 404);
        }
        return response()->json([
            "status" => 200,
            "message" => "Detail objek wisata",
            "data" => $wisata,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id, ObjekWisataRequest $objekWisataRequest)
    {
        //
        $filename = "";
        $validated = $objekWisataRequest->validated();
        $wisata_exist = ObjekWisata::where('id', $id)->first();
        if (!$wisata_exist) {
            return response()->json([
                "status" => 400,
                "messages" => "Objek wisata not found",
            ], 400);
        }

        $another_wisata_exist = ObjekWisata::where('nama', $validated['nama'])->where('kab_kota', $validated['kab_kota'])->where('provinsi', $validated['provinsi'])->whereNot('id', $id)->first();

        if ($another_wisata_exist) {
            return response()->json([
                "status" => 400,
                "messages" => "Objek wisata has already been taken",
            ], 400);
        }

        $files_exist = explode(' | ', $wisata_exist['foto']);
        foreach ($files_exist as $file) {
            Storage::disk('objek_wisata')->delete($file);
        }

        foreach ($objekWisataRequest['foto'] as $file) {
            $filename .= Storage::disk('objek_wisata')->put('', $file);
            if (array_search($file, $objekWisataRequest['foto']) != count($objekWisataRequest['foto']) - 1) {
                $filename .= ' | ';
            }
        }

        $validated['foto'] = $filename;
        $wisata_exist->update($validated);
        return response()->json([
            "status" => 201,
            "messages" => "Objek wisata $wisata_exist->nama berhasil diupdate",
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
        $wisata_exist = ObjekWisata::where('id', $id)->first();
        if (!$wisata_exist) {
            return response()->json([
                "status" => 400,
                "messages" => "Objek wisata not found",
            ], 400);
        }

        $files_exist = explode(' | ', $wisata_exist['foto']);
        foreach ($files_exist as $file) {
            Storage::disk('objek_wisata')->delete($file);
        }

        $deleted = ObjekWisata::where('id', $wisata_exist->id)->delete();
        if (!$deleted) {
            return response()->json([
                "status" => 400,
                "messages" => "There is problems!"
            ], 400);
        }

        return response()->json([
            "status" => 200,
            "messages" => "Objek wisata $wisata_exist->nama berhasil dihapus",
        ], 200);
    }
}

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

    public function show($id)
    {
        $wisata = ObjekWisata::all(['id', 'nama', 'deskripsi', 'alamat_lengkap', 'kab_kota', 'provinsi', 'fasilitas', 'foto', 'jumlah_like', 'jumlah_komen'])->where('id', $id)->first();
        $wisata['foto'] = explode(' | ', $wisata['foto']);
        $wisata['comments'] = $wisata->comments;
        $wisata['fasilitas'] = explode(',', $wisata['fasilitas']);
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

    public function update()
    {
    }
}

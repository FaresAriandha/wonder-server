<?php

namespace App\Http\Controllers;

use App\Models\ObjekWisata;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\ObjekWisataRequest;

class DashboardObjekWisata extends Controller {
    /**
     * Display a listing of the resource.
     */
    public function index() {
        //
        $data = ObjekWisata::All();
        return response()->json([
            "status" => 200,
            "data" => $data,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ObjekWisataRequest $objekWisataRequest) {
        $filename = '';
        $validated = $objekWisataRequest->validated();
        $wisata_exist = ObjekWisata::where('nama', $validated['nama'])->where('kab_kota', $validated['kab_kota'])->where('provinsi', $validated['provinsi'])->first();

        if ($wisata_exist) {
            return response()->json([
                "status" => 400,
                "message" => "Objek wisata sudah ada!",
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
            "status" => 200,
            "message" => "Objek wisata " . $validated['nama'] . " berhasil ditambah",
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show($id) {
        $wisata = ObjekWisata::all(['id', 'nama', 'deskripsi', 'alamat_lengkap', 'kab_kota', 'provinsi', 'lingkup', 'negara', 'fasilitas', 'foto', 'konten_blog', 'jumlah_like', 'jumlah_komen'])->where('id', $id)->first();
        if (!$wisata) {
            return response()->json([
                "status" => 404,
                "message" => "Objek wisata not found",
            ], 404);
        }

        if (request()->get('token_id')) {
            $id_user = DB::table('personal_access_tokens')->addSelect('tokenable_id')->where('token', hash('sha256', request()->get('token_id')))->first();
            $wisata['is_like_user'] = LikeObjekWisata::where('id_user', $id_user->tokenable_id)->where('id_objek_wisata', $id)->first() ? true : false;
        }


        $wisata['foto'] = explode(' | ', $wisata['foto']);
        $wisata['fasilitas'] = explode(',', $wisata['fasilitas']);


        return response()->json([
            "status" => 200,
            "message" => "Detail objek wisata",
            "data" => $wisata,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id, ObjekWisataRequest $objekWisataRequest) {
        //
        $filename = "";
        $validated = $objekWisataRequest->validated();
        $wisata_exist = ObjekWisata::where('id', $id)->first();
        if (!$wisata_exist) {
            return response()->json([
                "status" => 400,
                "message" => "Objek wisata tidak ditemukan",
            ], 400);
        }

        $another_wisata_exist = ObjekWisata::where('nama', $validated['nama'])->where('kab_kota', $validated['kab_kota'])->where('provinsi', $validated['provinsi'])->whereNot('id', $id)->first();

        if ($another_wisata_exist) {
            return response()->json([
                "status" => 400,
                "message" => "Objek wisata sudah ada!",
            ], 400);
        }

        if (isset($validated["foto"])) {

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
        }

        $wisata_exist->update($validated);
        return response()->json([
            "status" => 201,
            "message" => "Objek wisata $wisata_exist->nama berhasil diubah!",
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id) {
        //
        $wisata_exist = ObjekWisata::where('id', $id)->first();
        if (!$wisata_exist) {
            return response()->json([
                "status" => 400,
                "message" => "Objek wisata not found",
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
                "message" => "There is problems!"
            ], 400);
        }

        return response()->json([
            "status" => 200,
            "message" => "Objek wisata $wisata_exist->nama berhasil dihapus",
        ], 200);
    }
}

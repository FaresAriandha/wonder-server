<?php

namespace App\Http\Controllers;

use App\Models\User;
use Mockery\Undefined;
use App\Models\ObjekWisata;
use Illuminate\Http\Request;
use App\Models\LikeObjekWisata;
use App\Models\NilaiObjekWisata;
use Illuminate\Support\Facades\DB;
use App\Models\KomentarObjekWisata;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\ObjekWisataRequest;
use Illuminate\Contracts\Session\Session;
use App\Http\Requests\LikeAndCommentRequest;

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
        $wisata = ObjekWisata::all(['id', 'nama', 'deskripsi', 'alamat_lengkap', 'kab_kota', 'provinsi', 'negara', 'lingkup', 'fasilitas', 'foto', 'jumlah_like', 'jumlah_komen'])->where('id', $id)->first();
        $wisata['foto'] = explode(' | ', $wisata['foto']);
        $wisata['comments'] = $wisata->comments;
        $wisata['fasilitas'] = explode(',', $wisata['fasilitas']);
        $id_user = DB::table('personal_access_tokens')->addSelect('tokenable_id')->where('token', hash('sha256', request()->get('token_id')))->first();
        $wisata['is_like_user'] = LikeObjekWisata::where('id_user', $id_user->tokenable_id)->where('id_objek_wisata', $id)->first() ? true : false;
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

    public function show2($id)
    {
        return response()->json($id);
    }

    public function update($id, LikeAndCommentRequest $likeAndCommentRequest)
    {
        $wisata = ObjekWisata::where('id', $id)->first();
        if (!$wisata) {
            return response()->json([
                "status" => 404,
                "message" => "Objek wisata not found",
            ], 404);
        }

        $validated = $likeAndCommentRequest->validated();
        $data = [
            "id_user" => Auth::user()->id,
            "id_objek_wisata" => $id
        ];

        if (isset($validated['comment_user'])) {
            $data['komentar'] = $validated['comment_user'];
            $created_comment = KomentarObjekWisata::create($data);
            $updateObjekWisata = $wisata->update(["jumlah_komen" => $wisata->jumlah_komen + 1]);
            if (!$created_comment || !$updateObjekWisata) {
                return response()->json([
                    "status" => 400,
                    "messages" => "There is problem for updating process",
                ], 400);
            }
        }

        if (isset($validated['like_user'])) {
            if (!$validated['like_user']) {
                $update_like = LikeObjekWisata::where('id_user', $data['id_user'])->where('id_objek_wisata', $id)->delete();
                $updateObjekWisata = $wisata->update(["jumlah_like" => $wisata->jumlah_like - 1]);
            } else {
                $update_like = LikeObjekWisata::create($data);
                $updateObjekWisata = $wisata->update(["jumlah_like" => $wisata->jumlah_like + 1]);
            }

            if (!$update_like) {
                return response()->json([
                    "status" => 400,
                    "messages" => "There is problem for updating process",
                ], 400);
            }
        }

        if (!isset($validated['like_user']) && !isset($validated['comment_user'])) {
            return response()->json([
                "status" => 400,
                "messages" => "No likes and comments request",
            ], 400);
        }

        return response()->json([
            "status" => 200,
            "messages" => "$wisata->nama has been added like or comments",
        ], 200);
    }
}

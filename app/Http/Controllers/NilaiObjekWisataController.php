<?php

namespace App\Http\Controllers;

use App\Models\ObjekWisata;
use Illuminate\Http\Request;
use App\Models\NilaiObjekWisata;
use App\Http\Requests\NilaiObjekWisataRequest;
use App\Models\Kriteria;

class NilaiObjekWisataController extends Controller {
    /**
     * Display a listing of the resource.
     */
    static public $kriteria = [];
    static public $req = [];
    static public $arrMoora = [];

    static private function loadCriteria() {
        NilaiObjekWisataController::$kriteria = Kriteria::all(['nama_kriteria']);
        foreach (NilaiObjekWisataController::$kriteria as $kriteria) {
            NilaiObjekWisataController::$req[] = str_replace(' ', '_', strtolower($kriteria->nama_kriteria));
        }
    }

    public function __construct() {
        NilaiObjekWisataController::loadCriteria();
    }

    public function index() {
        //
        $data = [];
        $nilaiObjekWisata = NilaiObjekWisata::all()->groupBy('id_objek_wisata');
        $jumlahKriteria = count(Kriteria::all());
        foreach ($nilaiObjekWisata as $nilai) {
            $temp = [];
            for ($i = 0; $i < $jumlahKriteria; $i++) {
                if ($i == 0) {
                    $temp["id_objek_wisata"] = $nilai[$i]->objek_wisata->id;
                    $temp["nama_wisata"] = $nilai[$i]->objek_wisata->nama;
                }
                $temp[NilaiObjekWisataController::$req[$i]] = $nilai[$i]->nilai;
            }
            $data[] = $temp;
        }

        // dd($data);
        return response()->json([
            "status" => 200,
            "data" => $data,
        ], 200);
    }

    public function create() {
        return response()->json([
            "status" => 200,
            "criteria_name" => NilaiObjekWisataController::$req
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(NilaiObjekWisataRequest $nilaiObjekWisataRequest) {
        //
        $validated = $nilaiObjekWisataRequest->validated();
        $wisata_exist = ObjekWisata::where('id', $validated['id_objek_wisata'])->first();
        if (!$wisata_exist) {
            return response()->json([
                "status" => 400,
                "message" => "Objek wisata not found",
            ], 400);
        }

        $nilai_exist = NilaiObjekWisata::where("id_objek_wisata", $validated['id_objek_wisata'])->first();
        if ($nilai_exist) {
            return response()->json([
                "status" => 400,
                "message" => "Assessment already exists",
            ], 400);
        }

        for ($i = 0; $i < count(NilaiObjekWisataController::$kriteria); $i++) {
            $id_kriteria = Kriteria::where('nama_kriteria', NilaiObjekWisataController::$kriteria[$i]->nama_kriteria)->first()->id;
            $data = [
                "id_objek_wisata" => $validated['id_objek_wisata'],
                "id_kriteria" => $id_kriteria,
                "nilai" => $validated[NilaiObjekWisataController::$req[$i]]
            ];
            NilaiObjekWisata::create($data);
        }


        return response()->json([
            "status" => 201,
            "message" => "Assessment for $wisata_exist->nama has been added",
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id) {
        //
        $data = [];
        $nilai_exist = NilaiObjekWisata::with('kriteria:id,nama_kriteria')->where("id_objek_wisata", $id)->get();
        if (count($nilai_exist) == 0) {
            return response()->json([
                "status" => 400,
                "message" => "Assessment does not exist yet",
            ], 400);
        }

        for ($i = 0; $i < count(NilaiObjekWisataController::$req); $i++) {
            if ($i == 0) {
                $data['nama_wisata'] = $nilai_exist->first()->objek_wisata->nama;
                $data['id_objek_wisata'] = $nilai_exist->first()->id_objek_wisata;
            }
            $data[NilaiObjekWisataController::$req[$i]] = $nilai_exist->where('id_kriteria', $i + 1)->first()->nilai;
        }

        return response()->json([
            "status" => 200,
            "data" => $data,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id, NilaiObjekWisataRequest $nilaiObjekWisataRequest) {
        $validated = $nilaiObjekWisataRequest->validated();
        $wisata_exist = ObjekWisata::where('id', $id)->first();
        if (!$wisata_exist) {
            return response()->json([
                "status" => 400,
                "message" => "Objek wisata not found",
            ], 400);
        }

        $nilai_exist = NilaiObjekWisata::where("id_objek_wisata", $id)->first();
        if (!$nilai_exist) {
            return response()->json([
                "status" => 400,
                "message" => "Assessment does not exist yet",
            ], 400);
        }

        for ($i = 0; $i < count(NilaiObjekWisataController::$kriteria); $i++) {
            $id_kriteria = Kriteria::where('nama_kriteria', NilaiObjekWisataController::$kriteria[$i]->nama_kriteria)->first()->id;
            $row_nilai = NilaiObjekWisata::where('id_objek_wisata', $id)->where('id_kriteria', $id_kriteria)->first();
            $data = [
                "nilai" => $validated[NilaiObjekWisataController::$req[$i]]
            ];
            $row_nilai->update($data);
        }


        $nama_objek_wisata = $nilai_exist->objek_wisata->nama;
        return response()->json([
            "status" => 201,
            "message" => "Assessment for $nama_objek_wisata has been updated",
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id) {
        //
        $nilai_exist = NilaiObjekWisata::where("id_objek_wisata", $id)->first();
        if (!$nilai_exist) {
            return response()->json([
                "status" => 400,
                "message" => "Assessment does not exist yet",
            ], 400);
        }
        $deleted = NilaiObjekWisata::where("id_objek_wisata", $id)->delete();
        if (!$deleted) {
            return response()->json([
                "status" => 400,
                "message" => "There is problem for deleting process",
            ], 400);
        }
        $nama_wisata = $nilai_exist->objek_wisata->nama;
        return response()->json([
            "status" => 200,
            "message" => "Assessment for $nama_wisata has been deleted",
        ], 200);
    }


    static function moora_calculate() {
        NilaiObjekWisataController::loadCriteria();
        $data = [];
        $nilaiObjekWisata = NilaiObjekWisata::all()->groupBy('id_objek_wisata');
        $jumlahKriteria = count(Kriteria::all());


        foreach ($nilaiObjekWisata as $nilai) {
            $temp = [];
            for ($i = 0; $i < $jumlahKriteria; $i++) {
                if ($i == 0) {
                    $temp["objek_wisata"] = [
                        "id" => $nilai[$i]->objek_wisata->id,
                        "nama" => $nilai[$i]->objek_wisata->nama,
                        "deskripsi" => $nilai[$i]->objek_wisata->deskripsi,
                        "kab_kota" => $nilai[$i]->objek_wisata->kab_kota,
                        "provinsi" => $nilai[$i]->objek_wisata->provinsi,
                        "negara" => $nilai[$i]->objek_wisata->negara,
                        "lingkup" => $nilai[$i]->objek_wisata->lingkup,
                        "foto" => explode(" | ", $nilai[$i]->objek_wisata->foto),
                        "like" => $nilai[$i]->objek_wisata->jumlah_like,
                        "komen" => $nilai[$i]->objek_wisata->jumlah_komen,
                    ];
                }
                $temp[NilaiObjekWisataController::$req[$i]] = $nilai[$i]->nilai;
            }
            $data[] = $temp;
        }

        $data = NilaiObjekWisataController::matriks_ternormalisasi($data);
        $data = NilaiObjekWisataController::matriks_ternormalisasi_terbobot($data);
        $data = collect(NilaiObjekWisataController::matriks_optimisasi($data))->sort(function ($a, $b) {
            return $a['nilai_akhir'] < $b['nilai_akhir'];
        });

        return $data;
    }

    static private function matriks_ternormalisasi($data) {
        $arrPenyebut = [];
        for ($i = 0; $i < count(NilaiObjekWisataController::$req); $i++) {
            $penyebut = 0;
            $idKriteria = Kriteria::where('nama_kriteria', NilaiObjekWisataController::$kriteria[$i]->nama_kriteria)->first();
            $columns = NilaiObjekWisata::all()->where('id_kriteria', $idKriteria['id']);
            foreach ($columns as $column) {
                $penyebut += pow((int)$column->nilai, 2);
            }
            $arrPenyebut[] = sqrt($penyebut);
        }

        for ($i = 0; $i < count($data); $i++) {
            for ($j = 0; $j < count(NilaiObjekWisataController::$req); $j++) {
                $data[$i][NilaiObjekWisataController::$req[$j]] = round((int)$data[$i][NilaiObjekWisataController::$req[$j]] / $arrPenyebut[$j], 4);
            }
        }

        return $data;
    }

    static private function matriks_ternormalisasi_terbobot($data) {
        $bobot_nilai = Kriteria::all(['nama_kriteria', 'bobot']);
        for ($i = 0; $i < count($data); $i++) {
            for ($j = 0; $j < count($bobot_nilai); $j++) {
                $data[$i][NilaiObjekWisataController::$req[$j]] = round((float)$data[$i][NilaiObjekWisataController::$req[$j]] * (float)$bobot_nilai[$j]["bobot"], 4);
            }
        }
        return $data;
    }

    static private function matriks_optimisasi($data) {
        $costs = [];
        $criteria_cost = Kriteria::where('tipe', 'cost')->get();
        foreach ($criteria_cost as $criteria) {
            $costs[] = str_replace(' ', '_', strtolower($criteria->nama_kriteria));
        }
        for ($i = 0; $i < count($data); $i++) {
            $nilai_benefit = 0;
            $nilai_cost = 0;
            $benefits = collect(NilaiObjekWisataController::$req)->diff($costs);
            foreach ($benefits as $benefit) {
                $nilai_benefit += $data[$i][$benefit];
            }
            foreach ($costs as $cost) {
                $nilai_cost += $data[$i][$cost];
            }
            $bobot_like = 0.1 * $data[$i]['objek_wisata']['like'];
            $bobot_komen = 0.1 * $data[$i]['objek_wisata']['komen'];
            $data[$i]['nilai_akhir'] = round($nilai_benefit - $nilai_cost + ($bobot_like + $bobot_komen), 4);
        }

        return $data;
    }
}

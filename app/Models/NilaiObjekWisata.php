<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NilaiObjekWisata extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = "nilai_objek_wisata";

    public function objek_wisata()
    {
        return $this->belongsTo(ObjekWisata::class, 'id_objek_wisata');
    }

    public function kriteria()
    {
        return $this->belongsTo(Kriteria::class, 'id_kriteria');
    }
}

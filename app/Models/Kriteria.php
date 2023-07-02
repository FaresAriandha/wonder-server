<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kriteria extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $table = "kriteria";

    public function nilai_objek_wisata()
    {
        return $this->hasMany(NilaiObjekWisata::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ObjekWisata extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $table = "objek_wisata";

    public function nilai_objek_wisata()
    {
        return $this->hasMany(NilaiObjekWisata::class, "id_objek_wisata");
    }

    public function like()
    {
        return $this->hasMany(LikeObjekWisata::class, "id_objek_wisata");
    }

    public function comments()
    {
        return $this->hasMany(KomentarObjekWisata::class, "id_objek_wisata");
    }
}

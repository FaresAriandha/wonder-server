<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ObjekWisata extends Model {
    use HasFactory;
    protected $guarded = ['id'];
    protected $table = "objek_wisata";

    public function nilai_objek_wisata() {
        return $this->hasMany(NilaiObjekWisata::class, "id_objek_wisata");
    }

    public function like() {
        return $this->hasMany(LikeObjekWisata::class, "id_objek_wisata");
    }

    public function comments() {
        return $this->hasMany(KomentarObjekWisata::class, "id_objek_wisata")->join('users', 'komentar_objek_wisata.id_user', '=', 'users.id')->select('username', 'komentar', 'komentar_objek_wisata.created_at', 'users.foto');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model {
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = "articles";

    public function like() {
        return $this->hasMany(LikeObjekWisata::class, "id_objek_wisata");
    }

    public function comments() {
        return $this->hasMany(KomentarObjekWisata::class, "id_objek_wisata");
    }
}

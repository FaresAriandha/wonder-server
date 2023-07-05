<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LikeObjekWisata extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $table = "like_objek_wisata";

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function objek_wisata()
    {
        return $this->belongsTo(ObjekWisata::class, 'id_objek_wisata');
    }
}

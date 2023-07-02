<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KredensialAdmin extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'kredensial_admins';

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}

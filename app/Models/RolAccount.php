<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Rol;

class RolAccount extends Model
{
    use HasFactory;

    protected $table = 'rol_account';

    protected $fillable = ['id_rol', 'id_account'];

    public $incrementing = false;
    public $timestamps = false;

    public function rol() {
        return $this->hasOne(Rol::class, 'id', 'id_rol');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RolPermissions extends Model
{
    use HasFactory;

    protected $table = 'rol_permissions';

    protected $fillable = ['id_rol', 'id_permissions'];

    public $incrementing = false;
    public $timestamps = false;

    public function rol() {
        return $this->hasOne(Rol::class, 'id', 'id_rol');
    }

    public function permissions() {
        return $this->hasMany(Permissions::class, 'id', 'id_permissions');
    }
}

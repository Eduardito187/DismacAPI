<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RolPermissions extends Model
{
    use HasFactory;

    protected $table = 'rol_permissions';
    public $incrementing = false;
    public $timestamps = false;
}

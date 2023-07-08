<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\RolAccount;

class Rol extends Model
{
    use HasFactory;

    protected $table = 'rol';

    protected $fillable = ['name', 'code', 'created_at', 'updated_at'];

    protected $hidden = ['created_at', 'updated_at'];

    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'integer';
    public $timestamps = false;
}

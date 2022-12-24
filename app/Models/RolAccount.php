<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RolAccount extends Model
{
    use HasFactory;

    protected $table = 'rol_account';
    public $incrementing = false;
    public $timestamps = false;
}

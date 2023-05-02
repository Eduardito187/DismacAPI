<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MiniCuota extends Model
{
    use HasFactory;

    protected $table = 'mini_cuotas';

    protected $fillable = ['meses', 'cuotas', 'monto', 'created_at', 'updated_at'];
    
    protected $hidden = ['id', 'created_at', 'updated_at'];

    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'integer';
    public $timestamps = false;
}

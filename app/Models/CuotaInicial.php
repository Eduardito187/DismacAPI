<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CuotaInicial extends Model
{
    use HasFactory;

    protected $table = 'cuota_inicial';

    protected $fillable = ['inicial', 'id_store', 'id_product', 'created_at', 'updated_at'];

    public $incrementing = true;
    protected $keyType = 'integer';
    public $timestamps = false;
}

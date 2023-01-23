<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedidasComerciales extends Model
{
    use HasFactory;

    protected $table = 'medidas_comerciales';

    protected $fillable = ['longitud', 'ancho', 'altura', 'volumen', 'peso', 'created_at', 'updated_at'];

    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'integer';
    public $timestamps = false;
}

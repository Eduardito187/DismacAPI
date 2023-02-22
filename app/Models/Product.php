<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'product';

    protected $fillable = ['name', 'sku', 'stock', 'id_brand', 'id_clacom', 'id_metadata', 'created_at', 'updated_at', 'id_description', 'id_type', 'id_medidas_comerciales', 'id_cuota_inicial', 'id_partner'];

    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'integer';
    public $timestamps = false;
}

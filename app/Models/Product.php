<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProductAttribute;
use App\Models\Brand;
use App\Models\Clacom;

class Product extends Model
{
    use HasFactory;

    protected $table = 'product';

    protected $fillable = ['name', 'sku', 'stock', 'id_brand', 'id_clacom', 'id_metadata', 'created_at', 'updated_at', 'id_description', 'id_type', 'id_medidas_comerciales', 'id_cuota_inicial', 'id_partner'];

    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'integer';
    public $timestamps = false;
    
    public function Attributes(){
        return $this->hasMany(ProductAttribute::class, 'id', 'id_product');
    }

    public function Brand(){
        return $this->hasOne(Brand::class, 'id_brand', 'id');
    }

    public function Clacom(){
        return $this->hasOne(Clacom::class, 'id_clacom', 'id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Store;
use App\Models\Product;
use App\Models\MiniCuota;

class ProductMinicuotaStore extends Model
{
    use HasFactory;

    protected $table = 'product_minicuota_store';

    protected $fillable = ['id_store', 'id_product', 'id_minicuota'];

    public $incrementing = false;
    public $timestamps = false;
    
    public function Store(){
        return $this->hasOne(Store::class, 'id', 'id_store');
    }
    
    public function Product(){
        return $this->hasOne(Product::class, 'id', 'id_product');
    }
    
    public function MiniCuota(){
        return $this->hasOne(MiniCuota::class, 'id', 'id_minicuota');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
use App\Models\Store;

class ProductStoreStatus extends Model
{
    use HasFactory;

    protected $table = 'product_store_status';

    protected $fillable = ['id_product', 'id_store', 'status'];

    public $incrementing = false;
    public $timestamps = false;

    public function Product(){
        return $this->hasOne(Product::class, 'id', 'id_product');
    }

    public function Store(){
        return $this->hasOne(Store::class, 'id', 'id_store');
    }
}

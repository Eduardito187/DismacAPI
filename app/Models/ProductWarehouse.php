<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\Store;

class ProductWarehouse extends Model
{
    use HasFactory;

    protected $table = 'product_warehouse';

    protected $fillable = ['id_product', 'id_warehouse', 'id_store', 'stock', 'created_at', 'updated_at'];

    protected $hidden = ['created_at', 'updated_at'];

    public $incrementing = false;
    public $timestamps = false;

    public function Product(){
        return $this->hasOne(Product::class, 'id', 'id_product');
    }

    public function Warehouse(){
        return $this->hasOne(Warehouse::class, 'id', 'id_warehouse');
    }

    public function Store(){
        return $this->hasOne(Store::class, 'id', 'id_store');
    }
}

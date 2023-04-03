<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Price;
use App\Models\Store;
use App\Models\Product;

class ProductPriceStore extends Model
{
    use HasFactory;

    protected $table = 'product_price_store';

    protected $fillable = ['id_price', 'id_store', 'id_product'];

    public $incrementing = false;
    public $timestamps = false;
    
    public function Store(){
        return $this->hasOne(Store::class, 'id', 'id_store');
    }
    
    public function Product(){
        return $this->hasOne(Product::class, 'id', 'id_product');
    }
    
    public function Price(){
        return $this->hasOne(Price::class, 'id', 'id_price');
    }
}

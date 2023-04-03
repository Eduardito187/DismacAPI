<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
use App\Models\Store;
use App\Models\Category;
use App\Models\Catalog;

class ProductCategory extends Model
{
    use HasFactory;

    protected $table = 'product_category';

    protected $fillable = ['id_product', 'id_store', 'id_category', 'id_catalog'];

    public $incrementing = false;
    public $timestamps = false;

    public function Product(){
        return $this->hasOne(Product::class, 'id', 'id_product');
    }
    
    public function Store(){
        return $this->hasOne(Store::class, 'id', 'id_store');
    }
    
    public function Category(){
        return $this->hasOne(Category::class, 'id', 'id_category');
    }
    
    public function Catalog(){
        return $this->hasOne(Catalog::class, 'id', 'id_catalog');
    }
}

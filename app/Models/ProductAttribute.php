<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
use App\Models\Attribute;

class ProductAttribute extends Model
{
    use HasFactory;

    protected $table = 'product_attribute';

    protected $fillable = ['value', 'id_product', 'id_attribute', 'created_at', 'updated_at'];

    protected $hidden = ['id_product', 'created_at', 'updated_at'];

    public $incrementing = false;
    public $timestamps = false;
    
    public function Attibute(){
        return $this->hasOne(Attribute::class, 'id_attribute', 'id');
    }
    
    public function Product(){
        return $this->hasOne(Product::class, 'id_product', 'id');
    }
}

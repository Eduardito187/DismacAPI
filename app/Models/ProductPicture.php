<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Picture;
use App\Models\Product;

class ProductPicture extends Model
{
    use HasFactory;

    protected $table = 'product_picture';

    protected $fillable = ['id_product', 'id_picture'];

    public $incrementing = false;
    public $timestamps = false;

    public function Picture(){
        return $this->hasOne(Picture::class, 'id', 'id_picture');
    }

    public function Product(){
        return $this->hasOne(Product::class, 'id', 'id_product');
    }
}

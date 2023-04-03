<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
use App\Models\DataSheet;

class ProductSheet extends Model
{
    use HasFactory;

    protected $table = 'product_sheet';

    protected $fillable = ['id_product', 'id_sheet'];

    public $incrementing = false;
    public $timestamps = false;

    public function Product(){
        return $this->hasOne(Product::class, 'id', 'id_product');
    }

    public function DataSheet(){
        return $this->hasOne(DataSheet::class, 'id', 'id_sheet');
    }
}

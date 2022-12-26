<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductPriceStore extends Model
{
    use HasFactory;

    protected $table = 'product_price_store';

    protected $fillable = ['id_price', 'id_store', 'id_product'];

    public $incrementing = false;
    public $timestamps = false;
}

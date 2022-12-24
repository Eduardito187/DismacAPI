<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductPriceStore extends Model
{
    use HasFactory;

    protected $table = 'product_price_store';
    public $incrementing = false;
    public $timestamps = false;
}

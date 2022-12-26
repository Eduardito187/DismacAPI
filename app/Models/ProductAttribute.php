<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductAttribute extends Model
{
    use HasFactory;

    protected $table = 'product_attribute';

    protected $fillable = ['value', 'id_product', 'id_attribute', 'created_at', 'updated_at'];

    public $incrementing = false;
    public $timestamps = false;
}

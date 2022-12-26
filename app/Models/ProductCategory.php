<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    use HasFactory;

    protected $table = 'product_category';

    protected $fillable = ['id_product', 'id_store', 'id_category', 'created_at', 'updated_at'];

    public $incrementing = false;
    public $timestamps = false;
}

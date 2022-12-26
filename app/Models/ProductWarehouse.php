<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductWarehouse extends Model
{
    use HasFactory;

    protected $table = 'product_warehouse';

    protected $fillable = ['id_product', 'id_warehouse', 'stock', 'created_at', 'updated_at'];

    public $incrementing = false;
    public $timestamps = false;
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductStoreStatus extends Model
{
    use HasFactory;

    protected $table = 'product_store_status';

    protected $fillable = ['id_product', 'id_store', 'status'];

    public $incrementing = false;
    public $timestamps = false;
}

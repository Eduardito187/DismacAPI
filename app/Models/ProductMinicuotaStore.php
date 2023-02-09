<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductMinicuotaStore extends Model
{
    use HasFactory;

    protected $table = 'product_minicuota_store';

    protected $fillable = ['id_store', 'id_product', 'id_minicuota'];

    public $incrementing = false;
    public $timestamps = false;
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSheet extends Model
{
    use HasFactory;

    protected $table = 'product_sheet';

    protected $fillable = ['id_product', 'id_sheet'];

    public $incrementing = false;
    public $timestamps = false;
}

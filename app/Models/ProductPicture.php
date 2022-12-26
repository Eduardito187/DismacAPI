<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductPicture extends Model
{
    use HasFactory;

    protected $table = 'product_picture';

    protected $fillable = ['id_product', 'id_picture'];

    public $incrementing = false;
    public $timestamps = false;
}

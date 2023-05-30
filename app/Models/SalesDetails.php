<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Sales;
use App\Models\Product;

class SalesDetails extends Model
{
    use HasFactory;

    protected $table = 'sales_details';

    protected $fillable = ['sales', 'product', 'qty', 'discount', 'subtotal', 'total', 'created_at', 'updated_at'];

    protected $hidden = ['created_at', 'updated_at'];

    public $incrementing = false;
    public $timestamps = false;

    public function Sales(){
        return $this->hasOne(Sales::class, 'id', 'sales');
    }

    public function Product(){
        return $this->hasOne(Product::class, 'id', 'product');
    }
}
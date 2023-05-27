<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Sales;
use App\Models\Coupon;
use App\Models\Product;

class DetailDiscount extends Model
{
    use HasFactory;

    protected $table = 'detail_discount';

    protected $fillable = ['sales', 'product', 'id_coupon', 'monto', 'porcentaje', 'created_at', 'updated_at'];

    protected $hidden = ['created_at', 'updated_at'];

    public $incrementing = false;
    public $timestamps = false;

    public function Sales(){
        return $this->hasOne(Sales::class, 'id', 'sales');
    }

    public function Product(){
        return $this->hasOne(Product::class, 'id', 'product');
    }

    public function Coupon(){
        return $this->hasOne(Coupon::class, 'id', 'id_coupon');
    }
}
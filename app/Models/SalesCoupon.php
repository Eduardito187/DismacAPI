<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Sales;
use App\Models\Coupon;

class SalesCoupon extends Model
{
    use HasFactory;

    protected $table = 'sales_coupon';

    protected $fillable = ['sales', 'coupon', 'created_at', 'updated_at'];

    protected $hidden = ['created_at', 'updated_at'];

    public $incrementing = false;
    public $timestamps = false;

    public function Sales(){
        return $this->hasOne(Sales::class, 'id', 'sales');
    }

    public function Coupon(){
        return $this->hasOne(Coupon::class, 'id', 'coupon');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SalesCoupon;

class Coupon extends Model
{
    use HasFactory;

    protected $table = 'coupon';

    protected $fillable = ['name', 'description', 'coupon_code', 'type_discount', 'limit_client', 'limit_usage', 'status', 'from_date', 'to_date', 'created_at', 'updated_at'];

    protected $hidden = ['created_at', 'updated_at'];

    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'integer';
    public $timestamps = false;

    public function SalesCoupon(){
        return $this->hasMany(SalesCoupon::class, 'coupon', 'id');
    }
}
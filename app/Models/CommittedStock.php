<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Sales;
use App\Models\Product;
use App\Models\Warehouse;

class CommittedStock extends Model
{
    use HasFactory;

    protected $table = 'committed_stock';

    protected $fillable = ['sales', 'product', 'warehouse', 'qty', 'status', 'date_limit', 'store', 'created_at', 'updated_at'];

    protected $hidden = ['created_at', 'updated_at'];

    public $incrementing = false;
    public $timestamps = false;

    public function Sales(){
        return $this->hasOne(Sales::class, 'id', 'sales');
    }

    public function Product(){
        return $this->hasOne(Product::class, 'id', 'product');
    }

    public function Warehouse(){
        return $this->hasOne(Warehouse::class, 'id', 'warehouse');
    }
}
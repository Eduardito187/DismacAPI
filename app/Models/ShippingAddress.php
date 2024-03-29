<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Customer;
use App\Models\Address;
use App\Models\Sales;

class ShippingAddress extends Model
{
    use HasFactory;

    protected $table = 'shipping_address';

    protected $fillable = ['customer', 'address',  'sale'];

    public $incrementing = false;
    public $timestamps = false;
    
    public function Customer(){
        return $this->hasOne(Customer::class, 'id', 'customer');
    }
    
    public function Address(){
        return $this->hasOne(Address::class, 'id', 'address');
    }
    
    public function Sales(){
        return $this->hasOne(Sales::class, 'id', 'sale');
    }
}
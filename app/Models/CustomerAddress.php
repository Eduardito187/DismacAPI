<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Customer;
use App\Models\Address;

class CustomerAddress extends Model
{
    use HasFactory;

    protected $table = 'customer_address';

    protected $fillable = ['customer', 'address'];

    public $incrementing = false;
    public $timestamps = false;
    
    public function Customer(){
        return $this->hasOne(Customer::class, 'id', 'customer');
    }
    
    public function Address(){
        return $this->hasOne(Address::class, 'id', 'address');
    }
}
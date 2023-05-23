<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Account;
use App\Models\StatusOrder;
use App\Models\ShippingAddress;
use App\Models\SalesDetails;

class Sales extends Model
{
    use HasFactory;

    protected $table = 'sales';

    protected $fillable = ['products', 'account', 'status', 'shipping_address', 'discount', 'subtotal', 'total', 'nro_factura', 'nro_proforma', 'nro_control', 'ip_client', 'created_at', 'updated_at'];

    protected $hidden = ['created_at', 'updated_at'];

    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'integer';
    public $timestamps = false;

    public function Account(){
        return $this->hasOne(Account::class, 'id', 'account');
    }

    public function StatusOrder(){
        return $this->hasOne(StatusOrder::class, 'id', 'status');
    }

    public function ShippingAddress(){
        return $this->hasOne(ShippingAddress::class, 'id', 'shipping_address');
    }

    public function SalesDetails(){
        return $this->hasmany(SalesDetails::class, 'sales', 'id');
    }
}
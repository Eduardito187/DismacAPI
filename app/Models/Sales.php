<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Partner;
use App\Models\StatusOrder;
use App\Models\ShippingAddress;
use App\Models\SalesDetails;

class Sales extends Model
{
    use HasFactory;

    protected $table = 'sales';

    protected $fillable = ['id_partner', 'products', 'status', 'discount', 'subtotal', 'total', 'nro_factura', 'nro_proforma', 'nro_control', 'ip_client', 'created_at', 'updated_at'];

    protected $hidden = ['created_at', 'updated_at'];

    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'integer';
    public $timestamps = false;

    public function Partner(){
        return $this->hasOne(Partner::class, 'id', 'id_partner');
    }

    public function StatusOrder(){
        return $this->hasOne(StatusOrder::class, 'id', 'status');
    }

    public function ShippingAddress(){
        return $this->hasMany(ShippingAddress::class, 'sale', 'id');
    }

    public function SalesDetails(){
        return $this->hasmany(SalesDetails::class, 'sales', 'id');
    }
}
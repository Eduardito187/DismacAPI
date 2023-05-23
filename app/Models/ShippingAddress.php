<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\TipoDocumento;
use App\Models\Localization;
use App\Models\Country;
use App\Models\City;
use App\Models\Municipality;

class ShippingAddress extends Model
{
    use HasFactory;

    protected $table = 'shipping_address';

    protected $fillable = ['nombre', 'apellido_paterno',  'apellido_materno',  'email', 'num_telefono', 'tipo_documento', 'num_documento', 'country', 'city', 'municipality', 'direccion', 'direccion_extra', 'localization', 'fecha_entrega', 'created_at', 'updated_at'];

    protected $hidden = ['created_at', 'updated_at'];

    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'integer';
    public $timestamps = false;
    
    public function TipoDocumento(){
        return $this->hasOne(TipoDocumento::class, 'id', 'tipo_documento');
    }
    
    public function Localization(){
        return $this->hasOne(Localization::class, 'id', 'localization');
    }
    
    public function Country(){
        return $this->hasOne(Country::class, 'id', 'country');
    }
    
    public function City(){
        return $this->hasOne(City::class, 'id', 'city');
    }
    
    public function Municipality(){
        return $this->hasOne(Municipality::class, 'id', 'municipality');
    }
}
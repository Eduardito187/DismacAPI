<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProductAttribute;
use App\Models\Brand;
use App\Models\Clacom;
use App\Models\Metadata;
use App\Models\ProductType;
use App\Models\MedidasComerciales;
use App\Models\CuotaInicial;
use App\Models\Partner;
use App\Models\ProductPriceStore;
use App\Models\ProductMinicuotaStore;
use App\Models\ProductCategory;
use App\Models\ProductStoreStatus;
use App\Models\ProductSheet;
use App\Models\ProductWarehouse;
use App\Models\ProductPicture;
use App\Models\Families;

class Product extends Model
{
    use HasFactory;

    protected $table = 'product';

    protected $fillable = [
        'name', 'sku', 'stock', 'id_brand', 'id_clacom', 'id_metadata', 'created_at', 'updated_at', 'id_description', 'id_type', 
        'id_medidas_comerciales', 'id_partner', 'id_family'
    ];

    protected $hidden = [
        'stock', 'id_brand', 'id_clacom', 'id_metadata', 'created_at', 'updated_at', 'id_description', 'id_type', 
        'id_medidas_comerciales', 'id_partner', 'id_family'
    ];

    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'integer';
    public $timestamps = false;
    
    public function Status(){
        return $this->hasMany(ProductStoreStatus::class, 'id_product', 'id');
    }
    
    public function Categorys(){
        return $this->hasMany(ProductCategory::class, 'id_product', 'id');
    }

    public function Attributes(){
        return $this->hasMany(ProductAttribute::class, 'id_product', 'id');
    }

    public function Brand(){
        return $this->hasOne(Brand::class, 'id', 'id_brand');
    }

    public function Clacom(){
        return $this->hasOne(Clacom::class, 'id', 'id_clacom');
    }

    public function Metadata(){
        return $this->hasOne(Metadata::class, 'id', 'id_metadata');
    }

    public function Description(){
        return $this->hasOne(ProductDescription::class, 'id', 'id_description');
    }

    public function Type(){
        return $this->hasOne(ProductType::class, 'id', 'id_type');
    }

    public function MedidasComerciales(){
        return $this->hasOne(MedidasComerciales::class, 'id', 'id_medidas_comerciales');
    }

    public function CuotaInicial(){
        return $this->hasMany(CuotaInicial::class, 'id_product', 'id');
    }

    public function Partner(){
        return $this->hasOne(Partner::class, 'id', 'id_partner');
    }

    public function PriceStore(){
        return $this->hasMany(ProductPriceStore::class, 'id_product', 'id');
    }

    public function MinicuotaStore(){
        return $this->hasMany(ProductMinicuotaStore::class, 'id_product', 'id');
    }

    public function Sheet(){
        return $this->hasMany(ProductSheet::class, 'id_product', 'id');
    }

    public function Warehouse(){
        return $this->hasMany(ProductWarehouse::class, 'id_product', 'id');
    }

    public function Pictures(){
        return $this->hasMany(ProductPicture::class, 'id_product', 'id');
    }

    public function Family(){
        return $this->hasOne(Families::class, 'id', 'id_family');
    }
}

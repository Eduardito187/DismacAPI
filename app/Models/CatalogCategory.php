<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category;
use App\Models\Catalog;
use App\Models\Account;
use App\Models\Store;
use App\Models\ProductCategory;

class CatalogCategory extends Model
{
    use HasFactory;

    protected $table = 'catalog_category';

    protected $fillable = ['id_category', 'id_catalog', 'id_account', 'id_store', 'created_at', 'updated_at'];

    protected $hidden = ['id_catalog', 'id_account', 'created_at', 'updated_at'];

    public $incrementing = false;
    public $timestamps = false;

    public function Category(){
        return $this->hasOne(Category::class, 'id', 'id_category');
    }
    
    public function Catalog(){
        return $this->hasOne(Catalog::class, 'id', 'id_catalog');
    }
    
    public function Account(){
        return $this->hasOne(Account::class, 'id', 'id_account');
    }
    
    public function Store(){
        return $this->hasOne(Store::class, 'id', 'id_store');
    }

    public function GetCounProduct(){
        return $this->id_catalog;
        /*$this->hasMayne(ProductCategory::class, 'id_catalog', 'id_catalog');
        ->join('amenity_master','amenity_icon_url','=','image_url')
        ->where('amenity_master.status',1)
        ->where('outlet_amenities.status',1);
        */
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category;
use App\Models\Catalog;
use App\Models\Account;
use App\Models\Store;

class CatalogCategory extends Model
{
    use HasFactory;

    protected $table = 'catalog_category';

    protected $fillable = ['id_category', 'id_catalog', 'id_account', 'id_store', 'created_at', 'updated_at'];

    protected $hidden = ['id_catalog', 'id_account', 'id_store', 'created_at', 'updated_at'];

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
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CatalogCategory;

class Category extends Model
{
    use HasFactory;

    protected $table = 'category';

    protected $fillable = ['name', 'name_pos', 'code', 'inheritance', 'status', 'in_menu', 'id_info_category', 'created_at', 'updated_at', 'id_metadata'];

    protected $hidden = ['created_at', 'updated_at'];

    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'integer';
    public $timestamps = false;
    
    public function CatalogCategory(){
        return $this->hasMany(CatalogCategory::class, 'id_category', 'id');
    }
}

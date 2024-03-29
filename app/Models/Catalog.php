<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CatalogCategory;

class Catalog extends Model
{
    use HasFactory;

    protected $table = 'catalog';

    protected $fillable = ['name', 'code', 'created_at', 'updated_at'];

    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'integer';
    public $timestamps = false;

    public function Categorias(){
        return $this->hasMany(CatalogCategory::class, 'id_catalog', 'id');
    }
}

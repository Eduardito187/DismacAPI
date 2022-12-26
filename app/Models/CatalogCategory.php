<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatalogCategory extends Model
{
    use HasFactory;

    protected $table = 'catalog_category';

    protected $fillable = ['id_category', 'id_catalog', 'id_account', 'id_store', 'created_at', 'updated_at'];

    public $incrementing = false;
    public $timestamps = false;
}

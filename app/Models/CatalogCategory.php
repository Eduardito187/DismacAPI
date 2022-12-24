<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatalogCategory extends Model
{
    use HasFactory;

    protected $table = 'catalog_category';
    public $incrementing = false;
    public $timestamps = false;
}

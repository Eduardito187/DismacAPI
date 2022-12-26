<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatalogStore extends Model
{
    use HasFactory;

    protected $table = 'catalog_store';

    protected $fillable = ['id_catalog', 'id_store', 'id_account', 'created_at', 'updated_at'];

    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'integer';
    public $timestamps = false;
}

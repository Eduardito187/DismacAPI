<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\MunicipalityPos;

class Warehouse extends Model
{
    use HasFactory;

    protected $table = 'warehouses';

    protected $fillable = ['name', 'code', 'base', 'almacen', 'created_at', 'updated_at', 'id_municipality_pos', 'status'];

    protected $hidden = ['created_at', 'updated_at'];

    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'integer';
    public $timestamps = false;

    public function MunicipalityPos()
    {
        return $this->hasOne(MunicipalityPos::class, 'id', 'id_municipality_pos');
    }
}
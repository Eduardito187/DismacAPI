<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Store;

class MunicipalityPos extends Model
{
    use HasFactory;

    protected $table = 'municipality_pos';

    protected $fillable = ['id_store', 'name', 'status', 'created_at', 'updated_at'];
    
    protected $hidden = ['created_at', 'updated_at'];

    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'integer';
    public $timestamps = false;
    
    public function Store(){
        return $this->hasOne(Store::class, 'id', 'id_store');
    }
}
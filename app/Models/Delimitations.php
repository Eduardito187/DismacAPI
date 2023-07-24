<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Store;
use App\Models\Localization;

class Delimitations extends Model
{
    use HasFactory;

    protected $table = 'delimitations';

    protected $fillable = ['id_store', 'id_localization', 'status', 'created_at', 'updated_at'];
    
    protected $hidden = ['created_at', 'updated_at'];

    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'integer';
    public $timestamps = false;

    public function Store(){
        return $this->hasOne(Store::class, 'id', 'id_store');
    }

    public function Localization(){
        return $this->hasOne(Localization::class, 'id', 'id_localization');
    }
}
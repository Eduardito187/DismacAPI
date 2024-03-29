<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\TypeAttribute;

class Attribute extends Model
{
    use HasFactory;

    protected $table = 'attributes';

    protected $fillable = ['name', 'code', 'label', 'id_type', 'created_at', 'updated_at'];

    protected $hidden = ['created_at', 'updated_at'];

    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'integer';
    public $timestamps = false;

    public function Type(){
        return $this->hasOne(TypeAttribute::class, 'id', 'id_type');
    }
}

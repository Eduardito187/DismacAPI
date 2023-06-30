<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\FamiliesAttribute;

class Families extends Model
{
    use HasFactory;

    protected $table = 'families';

    protected $fillable = ['name', 'code', 'created_at', 'updated_at'];

    protected $hidden = ['id', 'created_at', 'updated_at'];

    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'integer';
    public $timestamps = false;

    public function Attributes(){
        return $this->hasMany(FamiliesAttribute::class, 'id_family', 'id');
    }
}
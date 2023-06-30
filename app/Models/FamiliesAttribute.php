<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Attribute;
use App\Models\Families;

class FamiliesAttribute extends Model
{
    use HasFactory;

    protected $table = 'families_attributes';

    protected $fillable = ['id_attribute', 'id_family', 'created_at', 'updated_at'];

    protected $hidden = ['id_attribute', 'id_family', 'created_at', 'updated_at'];

    public $incrementing = false;
    public $timestamps = false;
    
    public function Attribute(){
        return $this->hasOne(Attribute::class, 'id', 'id_attribute');
    }
    
    public function Family(){
        return $this->hasOne(Families::class, 'id', 'id_family');
    }
}
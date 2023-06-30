<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FamiliesAttribute extends Model
{
    use HasFactory;

    protected $table = 'families_attributes';

    protected $fillable = ['id_attribute', 'id_family', 'created_at', 'updated_at'];

    protected $hidden = ['id_attribute', 'id_family', 'created_at', 'updated_at'];

    public $incrementing = false;
    public $timestamps = false;
}
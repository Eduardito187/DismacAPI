<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FamiliesProduct extends Model
{
    use HasFactory;

    protected $table = 'families_product';

    protected $fillable = ['id_product', 'id_family', 'created_at', 'updated_at'];

    protected $hidden = ['id_product', 'id_family', 'created_at', 'updated_at'];

    public $incrementing = false;
    public $timestamps = false;
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoCupon extends Model
{
    use HasFactory;

    protected $table = 'tipo_coupon';

    protected $fillable = ['type', 'created_at', 'updated_at'];

    protected $hidden = ['created_at', 'updated_at'];

    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'integer';
    public $timestamps = false;
}
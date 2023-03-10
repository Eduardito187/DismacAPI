<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dimensions extends Model
{
    use HasFactory;

    protected $table = 'dimensions';

    protected $fillable = ['width', 'high', 'long', 'volume', 'height'];

    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'integer';
    public $timestamps = false;
}

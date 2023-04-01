<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clacom extends Model
{
    use HasFactory;

    protected $table = 'clacom';

    protected $fillable = ['label', 'code', 'id_picture', 'created_at', 'updated_at'];

    protected $hidden = ['id', 'id_picture', 'created_at', 'updated_at'];

    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'integer';
    public $timestamps = false;
}

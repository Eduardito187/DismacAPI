<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PictureProperty extends Model
{
    use HasFactory;

    protected $table = 'picture_property';

    protected $fillable = ['format', 'id_picture', 'id_dimensions', 'created_at', 'updated_at'];

    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'integer';
    public $timestamps = false;
}

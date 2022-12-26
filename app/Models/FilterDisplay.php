<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FilterDisplay extends Model
{
    use HasFactory;

    protected $table = 'filter_display';

    protected $fillable = ['status', 'navigation', 'position', 'id_display_info', 'id_info_filter', 'created_at', 'updated_at'];

    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'integer';
    public $timestamps = false;
}

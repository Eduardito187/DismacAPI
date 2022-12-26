<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FilterHide extends Model
{
    use HasFactory;

    protected $table = 'filter_hide';

    protected $fillable = ['id_category', 'id_filter', 'status', 'created_at', 'updated_at'];

    public $incrementing = false;
    public $timestamps = false;
}

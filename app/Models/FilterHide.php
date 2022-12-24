<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FilterHide extends Model
{
    use HasFactory;

    protected $table = 'filter_hide';
    public $incrementing = false;
    public $timestamps = false;
}

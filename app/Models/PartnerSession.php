<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartnerSession extends Model
{
    use HasFactory;

    protected $table = 'partner_session';
    public $incrementing = false;
    public $timestamps = false;
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartnerSession extends Model
{
    use HasFactory;

    protected $table = 'partner_session';

    protected $fillable = ['id_partner', 'id_session', 'status'];

    public $incrementing = false;
    public $timestamps = false;
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartnerAddress extends Model
{
    use HasFactory;

    protected $table = 'partner_address';

    protected $fillable = ['id_partner', 'id_address'];

    public $incrementing = false;
    public $timestamps = false;
}

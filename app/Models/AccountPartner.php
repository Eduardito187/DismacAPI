<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountPartner extends Model
{
    use HasFactory;

    protected $table = 'account_partner';

    protected $fillable = ['id_partner', 'id_account', 'status'];

    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'integer';
    public $timestamps = false;
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountStatus extends Model
{
    use HasFactory;

    protected $table = 'account_login';

    protected $visible = ['status'];

    protected $fillable = ['username', 'password', 'status', 'id_account', 'created_at', 'updated_at'];

    protected $hidden = ['username', 'password', 'id_account', 'created_at', 'updated_at'];

    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'integer';
    public $timestamps = false;
}

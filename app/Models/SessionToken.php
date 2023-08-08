<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Account;

class SessionToken extends Model{
    use HasFactory;

    protected $table = 'session_token';

    protected $fillable = ['token', 'status', 'id_account', 'created_at', 'updated_at'];

    protected $hidden = ['created_at', 'updated_at'];

    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'integer';
    public $timestamps = false;

    public function Account() {
        return $this->hasOne(Account::class, 'id', 'id_account');
    }
}
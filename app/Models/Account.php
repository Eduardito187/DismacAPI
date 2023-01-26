<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\AccountPartner;
use App\Models\AccountLogin;
use App\Models\AccountStatus;
use App\Models\RolAccount;

class Account extends Model
{
    use HasFactory;

    protected $table = 'account';

    protected $fillable = ['name', 'email', 'token', 'created_at', 'updated_at'];

    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'integer';
    public $timestamps = false;

    public function accountPartner() {
        return $this->hasOne(AccountPartner::class, 'id_account', 'id');
    }

    public function accountLogin() {
        return $this->hasOne(AccountLogin::class, 'id_account', 'id');
    }

    public function accountStatus() {
        return $this->hasOne(AccountStatus::class, 'id_account', 'id');
    }

    public function rolAccount() {
        return $this->belongsToMany(RolAccount::class, 'id_account', 'id');
    }
}

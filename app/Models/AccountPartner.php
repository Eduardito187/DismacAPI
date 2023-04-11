<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Partner;
use App\Models\Account;

class AccountPartner extends Model
{
    use HasFactory;

    protected $table = 'account_partner';

    protected $fillable = ['id_partner', 'id_account', 'status'];

    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'integer';
    public $timestamps = false;

    public function Partner() {
        return $this->hasOne(Partner::class, 'id', 'id_partner');
    }

    public function Account() {
        return $this->hasOne(Account::class, 'id', 'id_account');
    }
}

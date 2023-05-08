<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Account;
use App\Models\Partner;

class SupportTechnical extends Model
{
    use HasFactory;

    protected $table = 'support_technical';

    protected $fillable = ['id_account', 'id_partner', 'title', 'description', 'status'];
    
    protected $hidden = ['id', 'created_at', 'updated_at'];

    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'integer';
    public $timestamps = false;

    public function Account(){
        return $this->hasOne(Account::class, 'id', 'id_account');
    }

    public function Partner(){
        return $this->hasOne(Partner::class, 'id', 'id_partner');
    }
}

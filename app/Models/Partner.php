<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Picture;
use App\Models\Address;
use App\Models\AccountPartner;
use App\Models\StorePartner;
use App\Models\Campaign;

class Partner extends Model
{
    use HasFactory;

    protected $table = 'partner';

    protected $fillable = [
        'name', 'domain', 'email', 'token', 'nit', 'razon_social', 'status', 'legal_representative', 'picture_profile', 'picture_front', 
        'id_address', 'created_at', 'updated_at'
    ];

    protected $hidden = ['id', 'token', 'id_address', 'picture_profile', 'picture_front', 'created_at', 'updated_at'];

    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'integer';
    public $timestamps = false;

    public function AccountPartner(){
        return $this->hasOne(AccountPartner::class, 'id_partner', 'id');
    }

    public function Profile(){
        return $this->hasOne(Picture::class, 'id', 'picture_profile');
    }

    public function Front(){
        return $this->hasOne(Picture::class, 'id', 'picture_front');
    }

    public function Address(){
        return $this->hasOne(Address::class, 'id', 'id_address');
    }

    public function Stores(){
        return $this->hasMany(StorePartner::class, 'id_partner', 'id');
    }

    public function Campaigns(){
        return $this->hasMany(Campaign::class, 'id_partner', 'id');
    }
}

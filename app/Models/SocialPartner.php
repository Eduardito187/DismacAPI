<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SocialNetwork;
use App\Models\Partner;

class SocialPartner extends Model
{
    use HasFactory;

    protected $table = 'social_partner';

    protected $fillable = ['id_social_network', 'id_partner', 'url'];

    public $incrementing = false;
    public $timestamps = false;
    
    public function Social(){
        return $this->hasOne(SocialNetwork::class, 'id', 'id_social_network');
    }
    
    public function Partner(){
        return $this->hasOne(Partner::class, 'id', 'id_partner');
    }
}

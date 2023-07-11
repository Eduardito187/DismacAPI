<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SocialNetwork;
use App\Models\Campaign;

class SocialCampaings extends Model
{
    use HasFactory;

    protected $table = 'social_campaings';

    protected $fillable = ['id_social_network', 'id_campaign', 'url'];

    protected $hidden = ['id_social_network', 'id_campaign'];

    public $incrementing = false;
    public $timestamps = false;

    public function SocialNetwork(){
        return $this->hasOne(SocialNetwork::class, 'id', 'id_social_network');
    }

    public function Campaign(){
        return $this->hasOne(Campaign::class, 'id', 'id_campaign');
    }
}

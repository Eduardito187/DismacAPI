<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SocialNetwork;
use App\Models\Partner;
use App\Models\Category;

class Campaign extends Model
{
    use HasFactory;

    protected $table = 'campaign';

    protected $fillable = ['id_social_network', 'id_partner', 'id_category', 'name', 'url', 'status', 'products', 'created_at', 'updated_at', 'from_at', 'to_at'];

    protected $hidden = ['id_social_network', 'id_partner', 'id_category', 'created_at', 'updated_at', 'id_rol', 'id_account'];

    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'integer';
    public $timestamps = false;

    public function SocialNewtwork(){
        return $this->hasOne(SocialNetwork::class, 'id', 'id_social_network');
    }

    public function Partner(){
        return $this->hasOne(Partner::class, 'id', 'id_partner');
    }

    public function Category(){
        return $this->hasOne(Category::class, 'id', 'id_category');
    }
}

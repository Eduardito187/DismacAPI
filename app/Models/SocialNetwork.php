<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Picture;

class SocialNetwork extends Model
{
    use HasFactory;

    protected $table = 'social_network';
    
    protected $fillable = ['name', 'url', 'created_at', 'updated_at'];

    protected $hidden = ['created_at', 'updated_at'];

    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'integer';
    public $timestamps = false;

    public function Icon(){
        return $this->hasOne(Picture::class, 'id', 'icon');
    }
}

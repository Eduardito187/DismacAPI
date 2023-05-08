<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Picture;
use App\Models\SupportTecnical;

class SupportTecnicalPicture extends Model
{
    use HasFactory;

    protected $table = 'support_tecnical_picture';

    protected $fillable = ['id_support_technical', 'id_picture'];

    public $incrementing = false;
    public $timestamps = false;

    public function Picture(){
        return $this->hasOne(Picture::class, 'id', 'id_picture');
    }

    public function SupportTecnical(){
        return $this->hasOne(SupportTecnical::class, 'id', 'id_support_technical');
    }
}

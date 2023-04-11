<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Picture;
use App\Models\Content;

class CategoryInfo extends Model
{
    use HasFactory;

    protected $table = 'category_info';

    protected $fillable = ['show_filter', 'id_pos', 'sub_category_pos', 'id_picture', 'id_content', 'url', 'created_at', 'updated_at'];

    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'integer';
    public $timestamps = false;

    public function Picture(){
        return $this->hasOne(Picture::class, 'id', 'id_picture');
    }

    public function Content(){
        return $this->hasOne(Content::class, 'id', 'id_content');
    }
}

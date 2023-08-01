<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Partner;

class Analytics extends Model
{
    use HasFactory;

    protected $table = 'analytics';

    protected $fillable = ['channel', 'medium', 'type', 'code', 'key', 'value', 'status', 'id_partner', 'created_at', 'updated_at'];

    protected $hidden = ['created_at', 'updated_at'];

    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'integer';
    public $timestamps = false;
    
    public function Partner(){
        return $this->hasOne(Partner::class, 'id', 'id_partner');
    }
}
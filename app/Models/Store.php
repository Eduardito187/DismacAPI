<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Delimitations;

class Store extends Model
{
    use HasFactory;

    protected $table = 'store';

    protected $fillable = ['name', 'code', 'created_at', 'updated_at', 'status'];
    protected $hidden = ['created_at', 'updated_at'];

    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'integer';
    public $timestamps = false;

    public function Delimitations(){
        return $this->hasMany(Delimitations::class, 'id_store', 'id');
    }
}

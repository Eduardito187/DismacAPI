<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Account;

class Mejoras extends Model
{
    use HasFactory;

    protected $table = 'mejoras';

    protected $fillable = ['id_account', 'title', 'description', 'status'];
    
    protected $hidden = ['id', 'created_at', 'updated_at'];

    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'integer';
    public $timestamps = false;

    public function Account(){
        return $this->hasOne(Account::class, 'id', 'id_account');
    }
}

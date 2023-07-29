<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Store;
use App\Models\Partner;

class StorePartner extends Model
{
    use HasFactory;

    protected $table = 'store_partner';

    protected $fillable = ['id_store', 'id_partner'];

    public $incrementing = false;
    public $timestamps = false;

    public function Store() {
        return $this->hasOne(Store::class, 'id', 'id_store');
    }

    public function Partner() {
        return $this->hasOne(Partner::class, 'id', 'id_partner');
    }
}

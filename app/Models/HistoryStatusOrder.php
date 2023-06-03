<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Sales;
use App\Models\StatusOrder;

class HistoryStatusOrder extends Model
{
    use HasFactory;

    protected $table = 'history_status_order';

    protected $fillable = ['sale', 'status', 'created_at', 'updated_at'];

    protected $hidden = ['created_at', 'updated_at'];

    public $incrementing = false;
    public $timestamps = false;

    public function Sales(){
        return $this->hasOne(Sales::class, 'id', 'sale');
    }

    public function StatusOrder(){
        return $this->hasOne(StatusOrder::class, 'id', 'status');
    }
}

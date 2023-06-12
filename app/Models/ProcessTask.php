<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Process;
use App\Models\Partner;
use App\Models\ProcessTaskLog;

class ProcessTask extends Model
{
    use HasFactory;

    protected $table = 'process_task';
    
    protected $fillable = ['id_process', 'id_partner', 'mensaje', 'duracion', 'status', 'created_at', 'updated_at'];

    protected $hidden = ['created_at', 'updated_at'];

    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'integer';
    public $timestamps = false;

    public function Process(){
        return $this->hasOne(Process::class, 'id', 'id_process');
    }

    public function Partner(){
        return $this->hasOne(Partner::class, 'id', 'id_partner');
    }

    public function ProcessTaskLog(){
        return $this->hasMany(ProcessTaskLog::class, 'id_process_task', 'id');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProcessTask;

class ProcessTaskLog extends Model
{
    use HasFactory;

    protected $table = 'process_task_log';
    
    protected $fillable = ['id_process_task', 'mensaje', 'status', 'created_at', 'updated_at'];

    protected $hidden = ['created_at', 'updated_at'];

    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'integer';
    public $timestamps = false;

    public function ProcessTask(){
        return $this->hasOne(ProcessTask::class, 'id', 'id_process_task');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Picture;
use App\Models\Partner;
use App\Models\ProcessTask;

class Process extends Model
{
    use HasFactory;

    protected $table = 'process';
    
    protected $fillable = ['File', 'Partner', 'Type', 'Ejecucion', 'Duracion', 'FechaEjecucion', 'FechaDuracion', 'Status', 'created_at', 'updated_at'];

    protected $hidden = ['created_at', 'updated_at'];

    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'integer';
    public $timestamps = false;

    public function Data(){
        return $this->hasOne(Picture::class, 'id', 'File');
    }

    public function PartnerProcess(){
        return $this->hasOne(Partner::class, 'id', 'Partner');
    }
    
    public function ProcessTask(){
        return $this->hasOne(ProcessTask::class, 'id_process', 'id');
    }
}
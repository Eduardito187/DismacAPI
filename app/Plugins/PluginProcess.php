<?php

namespace App\Plugins;

use App\Models\Process;
use App\Classes\Tools\Sockets;
use App\Classes\Import\Import;

class PluginProcess{
    const PROCESS_AHORA = "AHORA";
    /**
     * @var Import
     */
    protected $Import;
    /**
     * @var Sockets
     */
    protected $Sockets;

    public function __construct() {
        $this->Import = new Import();
        $this->Sockets = new Sockets();
    } 

    public function created(Process $model){
        if ($model->Ejecucion == self::PROCESS_AHORA){
            $this->Import->processApply($model);
        }else{
            $data = array(
                "ID" => $model->id,
                "Ejecucion" => $model->Ejecucion,
                "Duracion" => $model->Duracion,
                "FechaEjecucion" => $model->FechaEjecucion,
                "FechaDuracion" => $model->FechaDuracion
            );
            $this->Sockets->sendQueryPost("NewProcess", $data);
        }
    }

    public function creating(Process $model){
        // Acciones a realizar cuando se actualiza un modelo
    }

    public function updated(Process $model){
        // Acciones a realizar cuando se actualiza un modelo
    }

    public function deleted(Process $model){
        // Acciones a realizar cuando se actualiza un modelo
    }
}
?>
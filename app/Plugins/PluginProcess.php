<?php

namespace App\Plugins;

use App\Models\Process;
use App\Classes\Import\Import;

class PluginProcess{
    const PROCESS_AHORA = "AHORA";
    /**
     * @var Import
     */
    protected $Import;

    public function __construct() {
        $this->Import = new Import();
    }

    public function creating(Process $model){
        if ($model->Ejecucion == self::PROCESS_AHORA){
            $this->Import->processApply($model);
        }
    }

    public function updated(Process $model){
        // Acciones a realizar cuando se actualiza un modelo
    }

    public function deleted(Process $model){
        // Acciones a realizar cuando se actualiza un modelo
    }
}
?>
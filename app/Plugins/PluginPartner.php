<?php

namespace App\Plugins;

use Illuminate\Database\Eloquent\Observer;
use App\Classes\Analytics\Analytics;
use App\Models\Partner;

class PluginPartner{
    const TYPE_ANALYTICS = "Partner";
    const creating_PARTNER = "creating_PARTNER";
    const UPDATED_PARTNER = "UPDATED_PARTNER";
    const VALUE_ANALYTICS = 1;
    /**
     * @var Analytics
     */
    protected $Analytics;

    public function __construct() {
        $this->Analytics = new Analytics();
    }

    public function creating(Partner $model){
        $this->Analytics->registerAnalytics(null, null, self::TYPE_ANALYTICS, self::creating_PARTNER, $model->id, self::VALUE_ANALYTICS);
    }

    public function updated(Partner $model){
        $this->Analytics->registerAnalytics(null, null, self::TYPE_ANALYTICS, self::UPDATED_PARTNER, $model->id, self::VALUE_ANALYTICS);
    }

    public function deleted(Partner $model){
        // Acciones a realizar cuando se actualiza un modelo
    }
}
?>
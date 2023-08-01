<?php

namespace App\Plugins;

use Illuminate\Database\Eloquent\Observer;
use App\Classes\Analytics\Analytics;
use App\Models\Partner;

class PluginPartner{
    const TYPE_ANALYTICS = "Partner";
    const CREATED_PARTNER = "CREATED_PARTNER";
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
        $this->Analytics->registerAnalytics(null, null, self::TYPE_ANALYTICS, self::CREATED_PARTNER, $model->id, self::VALUE_ANALYTICS, $model->id);
    }

    public function updated(Partner $model){
        $this->Analytics->registerAnalytics(null, null, self::TYPE_ANALYTICS, self::UPDATED_PARTNER, $model->id, self::VALUE_ANALYTICS, $model->id);
    }

    public function deleted(Partner $model){
        // Acciones a realizar cuando se actualiza un modelo
    }
}
?>
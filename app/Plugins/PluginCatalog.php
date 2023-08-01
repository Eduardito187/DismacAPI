<?php

namespace App\Plugins;

use Illuminate\Database\Eloquent\Observer;
use App\Classes\Analytics\Analytics;
use App\Models\Catalog;

class PluginCatalog{
    const TYPE_ANALYTICS = "Catalog";
    const CREATED_CATALOG = "CREATED_CATALOG";
    const UPDATED_CATALOG = "UPDATED_CATALOG";
    const VALUE_ANALYTICS = 1;
    /**
     * @var Analytics
     */
    protected $Analytics;

    public function __construct() {
        $this->Analytics = new Analytics();
    }

    public function creating(Catalog $model){
        $this->Analytics->registerAnalytics(null, null, self::TYPE_ANALYTICS, self::CREATED_CATALOG, $model->id, self::VALUE_ANALYTICS, null);
    }

    public function updated(Catalog $model){
        $this->Analytics->registerAnalytics(null, null, self::TYPE_ANALYTICS, self::UPDATED_CATALOG, $model->id, self::VALUE_ANALYTICS, null);
    }

    public function deleted(Catalog $model){
        // Acciones a realizar cuando se actualiza un modelo
    }
}
?>
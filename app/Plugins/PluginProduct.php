<?php

namespace App\Plugins;

use Illuminate\Database\Eloquent\Observer;
use App\Classes\Analytics\Analytics;
use App\Models\Product;

class PluginProduct{
    const TYPE_ANALYTICS = "Product";
    const CREATED_PRODUCT = "CREATED_PRODUCT";
    const UPDATED_PRODUCT = "UPDATED_PRODUCT";
    const VALUE_ANALYTICS = 1;
    /**
     * @var Analytics
     */
    protected $Analytics;

    public function __construct() {
        $this->Analytics = new Analytics();
    }

    public function creating(Product $model){
        $this->Analytics->registerAnalytics(null, null, self::TYPE_ANALYTICS, self::CREATED_PRODUCT, $model->id, self::VALUE_ANALYTICS, $model->id_partner);
    }

    public function updated(Product $model){
        $this->Analytics->registerAnalytics(null, null, self::TYPE_ANALYTICS, self::UPDATED_PRODUCT, $model->id, self::VALUE_ANALYTICS, $model->id_partner);
    }

    public function deleted(Product $model){
        // Acciones a realizar cuando se actualiza un modelo
    }
}
?>
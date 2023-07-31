<?php

namespace App\Plugins;

use Illuminate\Database\Eloquent\Observer;
use App\Classes\Analytics\Analytics;
use App\Models\Category;

class PluginCategory{
    const TYPE_ANALYTICS = "Category";
    const creating_CATEGORY = "creating_CATEGORY";
    const UPDATED_CATEGORY = "UPDATED_CATEGORY";
    const VALUE_ANALYTICS = 1;
    /**
     * @var Analytics
     */
    protected $Analytics;

    public function __construct() {
        $this->Analytics = new Analytics();
    }

    public function creating(Category $model){
        $this->Analytics->registerAnalytics(null, null, self::TYPE_ANALYTICS, self::creating_CATEGORY, $model->id, self::VALUE_ANALYTICS);
    }

    public function updated(Category $model){
        $this->Analytics->registerAnalytics(null, null, self::TYPE_ANALYTICS, self::UPDATED_CATEGORY, $model->id, self::VALUE_ANALYTICS);
    }

    public function deleted(Category $model){
        // Acciones a realizar cuando se actualiza un modelo
    }
}
?>
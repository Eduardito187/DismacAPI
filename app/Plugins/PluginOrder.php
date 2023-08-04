<?php

namespace App\Plugins;

use Illuminate\Database\Eloquent\Observer;
use App\Classes\Analytics\Analytics;
use App\Models\Sales;

class PluginOrder{
    const TYPE_ANALYTICS = "Order";
    const STATUS_CANCEL = 1;
    const ORDER_STATUS_CANCEL = "ORDER_STATUS_CANCEL";
    const ORDER_CREATED = "ORDER_CREATED";
    const STATUS_SUCCESS = 3;
    const ORDER_STATUS_SUCCESS = "ORDER_STATUS_SUCCESS";
    /**
     * @var Analytics
     */
    protected $Analytics;

    public function __construct() {
        $this->Analytics = new Analytics();
    }

    public function creating(Sales $model){
        $this->Analytics->registerAnalytics(null, null, self::TYPE_ANALYTICS, self::ORDER_CREATED, $model->id, $model->total, $model->id_partner);
    }

    public function updated(Sales $model){
        if ($model->status == self::STATUS_CANCEL){
            $this->Analytics->registerAnalytics(null, null, self::TYPE_ANALYTICS, self::ORDER_STATUS_CANCEL, $model->id, $model->total, $model->id_partner);
        }
        if ($model->status == self::STATUS_SUCCESS){
            $this->Analytics->registerAnalytics(null, null, self::TYPE_ANALYTICS, self::ORDER_STATUS_SUCCESS, $model->id, $model->total, $model->id_partner);
        }
    }

    public function deleted(Sales $model){
        // Acciones a realizar cuando se actualiza un modelo
    }
}
?>
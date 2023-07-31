<?php

namespace App\Plugins;

use Illuminate\Database\Eloquent\Observer;
use App\Classes\Analytics\Analytics;
use App\Models\Account;

class PluginAccount{
    const TYPE_ANALYTICS = "Account";
    const CREATED_ACCOUNT = "CREATED_ACCOUNT";
    const UPDATED_ACCOUNT = "UPDATED_ACCOUNT";
    const VALUE_ANALYTICS = 1;
    /**
     * @var Analytics
     */
    protected $Analytics;

    public function __construct() {
        $this->Analytics = new Analytics();
    }

    public function creating(Account $model){
        $this->Analytics->registerAnalytics(null, null, self::TYPE_ANALYTICS, self::CREATED_ACCOUNT, $model->id, self::VALUE_ANALYTICS);
    }

    public function updating(Account $model){
        $this->Analytics->registerAnalytics(null, null, self::TYPE_ANALYTICS, self::UPDATED_ACCOUNT, $model->id, self::VALUE_ANALYTICS);
    }

    public function deleting(Account $model){
        // Acciones a realizar cuando se actualiza un modelo
    }
}
?>
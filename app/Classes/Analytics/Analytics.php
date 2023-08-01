<?php

namespace App\Classes\Analytics;

use App\Classes\Helper\Text;
use App\Classes\Helper\Status;
use App\Classes\Helper\Date;
use App\Models\Analytics as ModelsAnalytics;
use Exception;

class Analytics{
    const DEFAULT_CHANNEL = "Marketplace";
    const DEFAULT_MEDIUM = "Internal";
    /**
     * @var Text
     */
    protected $text;
    /**
     * @var Status
     */
    protected $status;
    /**
     * @var Date
     */
    protected $date;

    public function __construct() {
        $this->text = new Text();
        $this->status = new Status();
        $this->date = new Date();
    }

    /**
     * @param string|null $channel
     * @param string|null $medium
     * @param string|null $type
     * @param string|null $code
     * @param string|null $key
     * @param string|null $value
     * @param int|null $id_partner
     * @return void
     */
    public function registerAnalytics(string|null $channel, string|null $medium, string|null $type, string|null $code, string|null $key, string|null $value, int|null $id_partner){
        try {
            $ModelsAnalytics = new ModelsAnalytics();
            $ModelsAnalytics->channel = $channel == null ? self::DEFAULT_CHANNEL : $channel;
            $ModelsAnalytics->medium = $medium == null ? self::DEFAULT_MEDIUM : $medium;
            $ModelsAnalytics->type = $type;
            $ModelsAnalytics->code = $code;
            $ModelsAnalytics->key = $key;
            $ModelsAnalytics->value = $value;
            $ModelsAnalytics->status = $this->status->getEnable();
            $ModelsAnalytics->created_at = $this->date->getFullDate();
            $ModelsAnalytics->updated_at = null;
            $ModelsAnalytics->id_partner = $id_partner;
            $ModelsAnalytics->save();
        } catch (Exception $th) {
            throw new Exception($th->getMessage());
        }
    }

    /**
     * @param int $id
     * @param bool $status
     * @return void
     */
    public function updateAnalytics(int $id, bool $status){
        $ModelsAnalytics = ModelsAnalytics::where($this->text->getId(), $id)->first();
        $ModelsAnalytics->status = $status;
        $ModelsAnalytics->updated_at = $this->date->getFullDate();
        $ModelsAnalytics->save();
    }
}
?>
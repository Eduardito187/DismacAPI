<?php

namespace App\Classes\Helper;

use Illuminate\Support\Facades\Log;
use App\Classes\Helper\Text;
use App\Models\Ip as Model_Ip;
use App\Models\RestrictIp;
use App\Classes\Helper\Date;
use \Exception;

class Ip{
    /**
     * @var Date
     */
    protected $date;
    /**
     * @var string
     */
    protected $IP = null;
    /**
     * @var Text
     */
    protected $text;

    public function __construct(string $ip) {
        $this->IP       = $ip;
        $this->text     = new Text();
        $this->date     = new Date();
    }

    /**
     * @return array
     */
    public function getGeo(){
        $data = json_decode(file_get_contents($this->text->getIpHost().$this->IP.$this->text->getBarraJson()));
        Log::debug("IP => ".$this->IP);
        if ($this->IP == $this->text->getLocalhost()) {
            $localization = [$this->text->getCero(), $this->text->getCero()];
        }else{
            $localization = explode ($this->text->getComa(), $data->loc);
        }
        return [
            $this->text->getLatitude() => $localization[0],
            $this->text->getLongitude() => $localization[1]
        ];
    }

    /**
     * @return bool
     */
    public function validRestrict(){
        $restrict_ip = $this->getRestrictIp();
        if (!$restrict_ip) {
            return true;
        }
        return false;
    }

    /**
     * @return void
     */
    public function validIp(){
        $ip = $this->getIp();
        if (!$ip) {
            $this->addIp();
        }
    }

    /**
     * @return Model_Ip
     */
    public function getIp(){
        return Model_Ip::where($this->text->getIp(), $this->IP)->first();
    }

    /**
     * @return bool
     */
    public function addIp(){
        try {
            $Model_Ip = new Model_Ip();
            $Model_Ip->ip = $this->IP;
            $Model_Ip->created_at = $this->date->getFullDate();
            $Model_Ip->updated_at = null;
            $Model_Ip->save();
            return true;
        } catch (Exception $th) {
            return false;
        }
    }

    /**
     * @return RestrictIp
     */
    public function getRestrictIp(){
        return RestrictIp::where($this->text->getIp(), $this->IP)->first();
    }
}

?>
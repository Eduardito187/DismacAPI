<?php

namespace App\Classes\Helper;

use Illuminate\Support\Facades\Log;

class Ip{

    protected $IP = null;

    public function __construct(string $ip) {
        $this->IP = $ip;
        $this->getGeo();
    }

    /**
     * @return array
     */
    public function getGeo(){
        $data = json_decode(file_get_contents("http://ipinfo.io/".$this->IP."/json"));
        $localization = explode (",", $data->loc);
        Log::debug("IP => ".json_encode($localization));
    }
}

?>
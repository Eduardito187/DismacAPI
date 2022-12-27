<?php

namespace App\Classes\Helper;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
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
        $url = "http://ipinfo.io/".$this->IP."/json";
        $data = Http::get($url);
        Log::debug("IP => ".json_encode($data));
    }
}

?>
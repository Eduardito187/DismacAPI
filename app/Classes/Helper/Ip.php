<?php

namespace App\Classes\Helper;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class Ip{

    protected $IP = null;

    public function __construct(string $ip) {
        $this->IP = $ip;
    }

    /**
     * @return array
     */
    public function getGeo(){
        $ip = request()->ip();
        $url = "http://ip-api.com/".$this->IP;
        $data = Http::get($url);
        Log::debug("Rejected => ".json_encode($data));
    }
}

?>
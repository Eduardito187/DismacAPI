<?php

namespace App\Classes\Helper;

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
        $data = json_decode(file_get_contents("http://ipinfo.io/".$this->IP."/json"));
        Log::debug("IP => ".$this->IP);
        if ($this->IP == "127.0.0.1") {
            $localization = ["0", "0"];
        }else{
            $localization = explode (",", $data->loc);
        }
        return [
            "latitude" => $localization[0],
            "longitude" => $localization[1]
        ];
    }
}

?>
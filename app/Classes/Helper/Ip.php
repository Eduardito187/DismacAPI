<?php

namespace App\Classes\Helper;

use Illuminate\Support\Facades\Log;
use App\Classes\Helper\Text;

class Ip{

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
}

?>
<?php

namespace App\Classes\Tools;

use App\Classes\Helper\Text;
use App\Classes\Helper\Date;

class Sockets{
    const URL = "http://31.220.31.243:3000/";
    /**
     * @var Text
     */
    protected $text;
    /**
     * @var Date
     */
    protected $date;

    public function __construct() {
        $this->text = new Text();
        $this->date = new Date();
    }

    /**
     * @param string $link
     * @param array $data
     * @return void
     */
    public function sendQueryPost(string $link, array $data){
        $url = self::URL.$link;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        $response = curl_exec($ch);
        curl_close($ch);
    }
}
?>
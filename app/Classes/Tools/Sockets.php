<?php

namespace App\Classes\Tools;

use App\Classes\Helper\Text;
use App\Classes\Helper\Date;
use Illuminate\Support\Facades\Http;

class Sockets{
    const URL = "http://62.72.9.102:3000/";
    const FCM = "https://fcm.googleapis.com/fcm/send";
    const TYPE_JSON = "Content-Type: application/json";
    const TO = "to";
    const NOTIFICATION = "notification";
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
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(self::TYPE_JSON));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
    }

    /**
     * @param string $token
     * @param string $title
     * @param string $body
     * @return void
     */
    public function sendNotification($token, $title, $body){
        $http = Http::post(self::FCM, [self::TO => $token, self::NOTIFICATION => array('body' => $body, 'title' => $title)])->header(self::TYPE_JSON);
    }
}
?>
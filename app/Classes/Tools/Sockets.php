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
    const TITLE_APP = "PlatformDismac";
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
     * @param string $message
     * @return void
     */
    public function sendNotification($token, $message){
        $ch = curl_init(self::FCM);
        $data = [
            self::TO => $token,
            self::NOTIFICATION => [
                "body" => $message,
                "title" => self::TITLE_APP
            ]
        ];
        $headers = [
            "Authorization: key=AAAAoqaefyg:APA91bFxpj2TAd6IXz8cz6RjQx2qxlsYxTtP9uBwa4-4ij0BDuC8ayh-QJO0RKyKVTbaF_jrrCyDSUWa-2c1ybOm-mgq9L73EJdKOhzHHlHhUXieaj0jEQSbSvyAzIbvhgSR0xSuTtyG",
            self::TYPE_JSON
        ];
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        echo "SEND";
    }
}
?>
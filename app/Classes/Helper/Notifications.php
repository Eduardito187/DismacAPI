<?php

namespace App\Classes\Helper;

use App\Classes\Helper\Text;
use Illuminate\Support\Facades\Http;

class Notifications{
    const SERVER_KEY_DE_FCM = "1244a5d2c0b4704753057b3ff2e16f81b3911a9d";
    /**
     * @var Text
     */
    protected $text;

    public function __construct() {
        $this->text  = new Text();
    }

    public function sendNotificationAndroid(){
        $fcmEndpoint = 'https://fcm.googleapis.com/v1/projects/project-127322741666/messages:send';
        
        $tokens = ['e6jAm2RBRhirK4-HSfljnd:APA91bHdU3JEwX0TlHVN6zsFcNQUgRhimYKFePq8RTMZ3i56mOE4ViC_IN6pznbn8cFaHille6wceDh1C1xJJLFGDp9eO6EwsbE9qu7sSGj1_sDQPp-lwfltQvdeApv4oGEUHbs6T79o'];
        
        $notification = [
            'message' => [
                'token' => $tokens[0],
                'notification' => [
                    'title' => 'Título de la notificación',
                    'body' => 'Cuerpo de la notificación',
                ],
            ],
        ];
        
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . self::SERVER_KEY_DE_FCM,
            'Content-Type' => 'application/json',
        ])->post($fcmEndpoint, $notification);

        print_r($response);
    }
}
?>
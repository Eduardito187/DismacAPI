<?php

namespace App\Classes\Helper;

use App\Classes\Helper\Text;
use Illuminate\Support\Facades\Http;

class Notifications{
    const SERVER_KEY_DE_FCM = "BNqTYcanIiF59jll47CKp2tM-4apzHUYMyj7g6tdT-HfYKeL8WtvSSfQ4CALHAjvHbdtA_LbXPiznXv3EpwRubY";
    /**
     * @var Text
     */
    protected $text;

    public function __construct() {
        $this->text  = new Text();
    }

    public function sendNotificationAndroid(){
        $fcmEndpoint = 'https://fcm.googleapis.com/fcm/send';
        
        $tokens = ['e6jAm2RBRhirK4-HSfljnd:APA91bHdU3JEwX0TlHVN6zsFcNQUgRhimYKFePq8RTMZ3i56mOE4ViC_IN6pznbn8cFaHille6wceDh1C1xJJLFGDp9eO6EwsbE9qu7sSGj1_sDQPp-lwfltQvdeApv4oGEUHbs6T79o'];
        
        $notification = [
            'title' => 'Título de la notificación',
            'body' => 'Cuerpo de la notificación'
        ];
        
        $response = Http::withHeaders([
            'Authorization' => 'key=' . self::SERVER_KEY_DE_FCM,
            'Content-Type' => 'application/json',
        ])->post($fcmEndpoint, [
            'registration_ids' => $tokens,
            'notification' => $notification,
        ]);

        print_r($response);
    }
}
?>
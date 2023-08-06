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

    public function __construct(){
        $this->text  = new Text();
    }

    function getTokenAssertion($privateKey, $clientId){
        $accessTokenEndpoint = 'https://www.googleapis.com/oauth2/v4/token';
        $now = time();
        $expiration = $now + 3600;

        $header = base64_encode(json_encode([
            'alg' => 'RS256',
            'typ' => 'JWT',
        ]));

        $payload = base64_encode(json_encode([
            'iss' => $clientId,
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud' => $accessTokenEndpoint,
            'exp' => $expiration,
            'iat' => $now,
        ]));

        $signature = '';
        openssl_sign("$header.$payload", $signature, $privateKey, 'SHA256');
        $signature = base64_encode($signature);

        return "$header.$payload.$signature";
    }

    private function getAccessToken(){
        $accessTokenEndpoint = 'https://www.googleapis.com/oauth2/v4/token';
        $privateKey = "-----BEGIN PRIVATE KEY-----\nMIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQCbRDoLP4S3e1aW\nJtn1yJaFh5tkiIXAyZiUr9WKIqGLM2fXRjz+zXgNU58f6XJxrrUxC1zW2B0yBab0\nG0Yt4JglVDYeMrstxtacRW5eyb9fTTdBnPEoLnjaggOGnRscad247yoqLKKvIZZq\nwY+ZHYsrRXZYjhIu3PgI0V/jQMxH1ILWnC2fqGl+VF+OgnCtzUbi+/rNol/Be25q\n+xRzpwHXCqR3WAB5CezkcGncrSj1nmCNT0zfPLDo4jtL7M9EqiqLCED+ofoLMv8A\nfiNsRv2G1b0F7oaaz+jg3Gy/KD374HMthNI5y9YHA7nKhx2sxiVCc5YQsrfDXSac\nn0rk4uiHAgMBAAECggEALuTEyRfmjAn2xUO3qZlrCKpDvvHSv77DVAjlLCK4epPM\nhYKjsms5GoFKendlxrr243ikkgjUjIKTgX9pSzrdiMytyaIV0UYQFEOOCZfSMkTe\nbvUpCrM6cfg5e2MgyIya2Bt3tyQjAFTbGqQaLCr1mNySWT2TdU7spLEjzj2IVRr7\n5QzIUmBfyFDNIV9gftWHZ6N8I0y5u2AnbgklvJAPOfTPqcz1P+4I9ti+3I846jDv\nppeuNoeClJcdswJe50hVSoiIcs9xWoACh8fkBdPuh+qcbEaaZzljWil7JGYYDHQS\nW10SxI2wZ2HrNeZOFViz6zr5wtGmYeRoVbgftaF8aQKBgQDOvVK7LzsJq8xcXvYQ\njGZ6ozPKr2CMDIS3bW6A0uuPpPJ+8sjj/jwnwbZR5RcUW66I2E9GR5MYv2UR2GRn\nZf8wDMLs8JzxfwI+BxXCpHnNL4GZqXaIyKbPlp4dHI2GE6VHc28NiXrLGGWXqeT4\nqPXs9DNTJcDDdBuHxOAgZ/2ezwKBgQDAQygtWkUBtYOJw2/R6Rn+0tZ2h/pHOlry\nHr+fMA7P2ov1WGv4XT85dHh8TTSHkzuXBQJX5yLdQhlRVjLbpHNyh/kmaGnfwy+E\nrKD4plSB/72r4fSXRET2jmy5waRNhY9CuuNH0fuukPmG5sgNkiZT+KvRGvL5Nfr+\nc8pZEjlIyQKBgQCju82J8h4PysBPZ+X5tTh0aEzceYk9Yay6mTCrVmC5oylq4V25\nM+Dwm7qJoaZluTbBqo8eWhFlyC+Dsp3SRjWVGIZIoUeq+6wK9BGH1juhBb7etBfs\nvP1f6ynOZI6xYV3E+Oc7EWbu8qODOkrdpPMgd0cu0veX1Jz5KjNXdwSmgQKBgF/W\n2kdJUU7Zybk5uBr+RNwXA5tQiz1IZJ9/HmxfC3MncU8bBa9n5CVi3tCvt7jBdxoA\nADwLVwDOe0plWrLMllQIXfV8ZRKK/Lv5RcDNQSEQd98fospo2KvDMYWjdqDCLDjK\napOZpAlP8WMC1cSWDw8azaNN+MKr2vNOixa9k9qpAoGAZv2B3Wgi7g+DPvJjp+v0\nTBVJigLkhlPRMLqc+KLmDdDQdCSA+cMvNch6tA6SQ4zanUVZdOrTI71KtsH04C9+\n0Hrpq4q5xMPWuIT7OVDVJ9mWl1eccksjai/4pWG1vt/H3q6/jnTmxiGNqvLxArPc\nXc1wRWL7t1hIAagBL6F7Ndc=\n-----END PRIVATE KEY-----\n";
        $clientId = "fyz-3-754@modalidad-682cf.iam.gserviceaccount.com";

        $response = Http::post($accessTokenEndpoint, [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $this->getTokenAssertion($privateKey, $clientId),
        ]);

        return $response->json()['access_token'];
    }

    public function sendNotificationAndroid(){
        $serverKey = 'AAAAHaUG26I:APA91bFDOcLu0wTpRsR7PsFoCJJIqYnwh3l6yJBs239HqzZtrWWq7JhzP2SRQQRHOvLeTFSPqfzwgpN7sHcJH8QgETL8u9DnWCCdLWuI-aFFMbT5T5RwbNgp-NJXf-vZXSuGssvvBtrT';
        $fcmEndpoint = 'https://fcm.googleapis.com/fcm/send';

        $tokens = ['e6jAm2RBRhirK4-HSfljnd:APA91bHdU3JEwX0TlHVN6zsFcNQUgRhimYKFePq8RTMZ3i56mOE4ViC_IN6pznbn8cFaHille6wceDh1C1xJJLFGDp9eO6EwsbE9qu7sSGj1_sDQPp-lwfltQvdeApv4oGEUHbs6T79o'];

        $notification = [
            'title' => 'Título de la notificación',
            'body' => 'Cuerpo de la notificación'
        ];

        $response = Http::withHeaders([
            'Authorization' => 'key=' . $serverKey,
            'Content-Type' => 'application/json',
        ])->post($fcmEndpoint, [
            'registration_ids' => $tokens,
            'notification' => $notification,
        ]);
        print_r($response);
        /*

        $fcmEndpoint = 'https://fcm.googleapis.com/v1/projects/modalidad-682cf/messages:send';

        $notification = array(
            "message" => array(
                "token" => 'e6jAm2RBRhirK4-HSfljnd:APA91bHdU3JEwX0TlHVN6zsFcNQUgRhimYKFePq8RTMZ3i56mOE4ViC_IN6pznbn8cFaHille6wceDh1C1xJJLFGDp9eO6EwsbE9qu7sSGj1_sDQPp-lwfltQvdeApv4oGEUHbs6T79o',
                "notification" => array(
                    "title" => "Match update",
                    "body" => "Arsenal goal in added time, score is now 3-0"
                ),
                "android" => array(
                    "ttl" => "86400s",
                    "notification" => array(
                        "click_action" => "OPEN_ACTIVITY_1"
                    )
                ),
                "apns" => array(
                    "headers" => array(
                        "apns-priority" => "5"
                    ),
                    "payload" => array(
                        "aps" => array(
                            "category" => "NEW_MESSAGE_CATEGORY"
                        )
                    )
                ),
                "webpush" => array(
                    "headers" => array(
                        "TTL" => "86400"
                    )
                )
            )
        );
        print_r($this->getAccessToken());
        
        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.$this->getAccessToken(),
            'Content-Type' => 'application/json;charset=UTF-8'
        ])->post($fcmEndpoint, $notification);
        */
        
    }
}

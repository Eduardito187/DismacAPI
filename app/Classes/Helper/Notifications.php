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
        $privateKey = "-----BEGIN PRIVATE KEY-----\nMIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQCnyPfRPs7zCae9\nLbt8rx4jeY1CLZXO4B0+4NPXIt+1wdffuEJjQSRCZl7Rt6S6SMbvM6puGr0g5oZg\n9kIXizkOzMV3tGA/a3EeUR8O6vyb3eBFWgOAxfHF/fqSgK3H8ogOAjEifx1Y5dvB\nBtj5RBRtS1oL+pryAfDgGkEWNM2VuCQPljkjPMpYjNI+Y/k9NhaDccGSaLAHgwM5\n3Kp8vuH93aQGHNLOOgHtBTPNgLEHyc45zw+X8rR4PxqE8kUlgmNk6WdiJvnq7wA1\nwlLktuK+JEltu+NQcjOMV5xAVoZBqXyODYuMUxhDGua3ZRI+iHR6uzxaFVJVKyGo\nkB1s696LAgMBAAECggEABVe7pX83xcv2C1nF6w4EeVVgiQEA3XjyZoZfKZSF+Nqu\nMDgAkGDw2CwrRB0c2UBf4ZHqmZl8PKgpeYlXhDpooB3SE6IzW/5tQNPzhn83VxK5\na9NjN29i+Qpse5jY+FFusFQKNOXepsOPzRnJDXqnB1S4MJe6GNOjqXe3OA9V+zjJ\nEjiYW3KqUi38VZNLzAz/oZJWwPSYJxCzMLs9TlLtKBgTADxXJitm/9IevZHG8Q/z\nmZXbNxrsZXJoplKdtv+0feOIXy7Jd/Hr7MzD9ZNa9EpeUYOXUR3mALPLa0Wm7iLN\nk5tx9d0vn/0oCYt9vOaCvHGN0SJTb5AoRZd8NyjaEQKBgQDjrgUTFFX6xuAAU74Y\nrpElkcOTFiiLYL7QHH63M5fQq8stkGEueRgtL5g3gm4jZAO0r1em9f6608S0to7L\nHWmQt67yu1ZvDb7TsC7SaSt6u4iSzPz+igTyVmxnLbvZpCPk0S1CVDLyu/XnL3Qe\n46brwBw14iX0EZYryk2gVdANCQKBgQC8p7oqJnGUxV03aXZl+Tr7xHNqHewVUXyR\nm/HGDH/nrNAX/0erc0yOsQE+T/9lB84yWD7uM9u/TaiRVq92mFDbR+Pf4qznbrPu\nbTwyeohjlIgsvAn9WGeOZ2WUASCCU+LPQgNucN0j893rOHE0zCRixY+HSauRKXQe\n9hzHRMJH8wKBgH3EoRT7BMu0lARaPgYQWClyraBQezESzhTTyMOWgmRIocGXobc0\nOUCsq97t870lAE8NFNQryvyVAnH47kQRCUFh1ghLp+FMrH7vMc4VPmlbsoO1LIPG\n5hif2N3eC4ib/R/m1KNxM3uWjK2aLwDpLEnKI0s4k1KRbGzgIaVSAc1RAoGBAIaV\nXUCulj6cochjCxzqNMLhPbjJSd9Xd3dk+ZDg3+pFpVpJOja8lTQCYDG03iyT6eyO\nea5y/mQ2IXg1kA0Z0izs10qjqdecaO5BrT0RnvTzREYtiy7Z/15est9oAmoXL+nN\nXSdPiuMYGQuyA0i4DYkNCTxpOOTg3r8rshISIkszAoGATxIQ4Lg+AgjsvgrjBufa\nh9EdtaQboBogt/PqqR5gT9G8WlJfvyVo8rvr8ivwrBEFdN0BNMfbxkJCeNsHuD6n\nMIF58KhSMFdLh7r2gZb9v5WO0AlCtWpfjNIuvTAWt/ly5w5muE6oDSgl8gsvN5JZ\n9HQY1gJELM5gD0mzLwLGzK0=\n-----END PRIVATE KEY-----\n";
        $clientId = "customizacion@notificaciones-a60ac.iam.gserviceaccount.com";

        $response = Http::post($accessTokenEndpoint, [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $this->getTokenAssertion($privateKey, $clientId),
        ]);

        return $response->json()['access_token'];
    }

    public function sendNotificationAndroid(){
        /*
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
        */
        /*
        $fcmEndpoint = 'https://fcm.googleapis.com/v1/projects/notificaciones-a60ac/messages:send';

        $notification = array(
            "message" => array(
                "token" => 'fofveZuBRLuskqi6YuuPvS:APA91bHN9_iwToKLq6AdvhOcGO0K3sUzhA8X_bEf6qj5UCimtV5FpD91Bs4WCVYxprAnVua904-ApZY-jrOpJQfpOCrK10HWvwEfen62B4VWj4X1f73C3tFjy51_YCFHUb7Fl-kGiHu-',
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
        
        $url = "https://fcm.googleapis.com/v1/projects/notificaciones-a60ac/messages:send";

        // Token de autorización del servidor de FCM
        $authorization = "Bearer ya29.c.b0Aaekm1K98icYYmZqvU7wnGWwFYd0asWQERckqHYUswTfJELkwFbbKWNIqVLesBZEzqn7oAbitnVlsRFzsHRGSuBq8uTekKWFLF7lTNRbnbr6ct58KF0d4F2JTMx9RKyE4pQGhdzpbnh-UWpf-SGc4v0LnyP8yOhLxsv-nkVzpAkasW6ALWZSFTU4Pu7ChJwthham3G7S-8xbM2LrHO9mnHIpgWWf5MWqArNi6GRe5UU6ulaiQvm0XRwbHfwPHPzpTRUxF00t6Wpq2AHpmODqZUCV-88q3G81aIH1k4uvRZUFWJ1OQoE3bGrprx4wyEp2dyexBLGDN337P4I1i5RkZn-c8qpO5FbZd8JfygMj7ieo6YY0tdkb67m8yxY7tx26vqvRaB-4XqFyftz_Vjt2qeMR4rFohJtqsUVm450q5WYRvSmWj16U-0xZcruvcdygs-6rhUXu0Z_WXe_csbffZ9Mu67O2fjnrMwav92tB9Uo8_oUrI_QZ-es33yg64lfIp2rIUbtRR-2lWygj0QFq7WB1zSldxmpyi0etphX5629YRSta9Xbu8VuF6m11upmqytQSf6fUQ72g6ed7sablnnyyQd08kXifpFnkyu6Yn9kRRrZuvQ97s_RIhQRaJnvoi3msMjblOaoz4OUf9plZzmMcROraf0VO0l7kcfmSZkOzFl6xlWXj7-x4Vr5rpkj6h-j451qeOc9Wam3zwmYskFg4061ZRnngwQ9Rq6zdRecotos4xacex0IjdSUyfX1R3iQB7a_XZ747_a0Up42dk3hz87F5wVc0nd2II1u55ysjhdosx6Fll5lc_fFX_wYr8sXdfWlF9zRiZ93ZgRyhJ4BZ52uRXs4ehlUwhtwdF7-yeM2xZ_b9_QBuVmFr_hnkvk5lFs0u56pWU3XjIlWk657vXu0g5vMS94Mq2t8lwvi3dvYji-6vI5Qxb9lfSJsdSIpOp3lSItOUz80eISpiFRcgVYSowcsMSSahsocZ5cuzrZ-v-xlp2m9"; // Reemplaza con tu clave del servidor FCM

        // Datos de la notificación
        $data = [
            "message" => [
                "token" => "fofveZuBRLuskqi6YuuPvS:APA91bHN9_iwToKLq6AdvhOcGO0K3sUzhA8X_bEf6qj5UCimtV5FpD91Bs4WCVYxprAnVua9O4-ApZY-jr0pJQfpOCrK1oHWvwEfen62B4VWj4XIf73C3tFjy5l_YCFHUb7FI-kGiHu-", // Reemplaza con el token de registro del dispositivo
                "notification" => [
                    "title" => "Título de la notificación",
                    "body" => "Cuerpo de la notificación"
                ]
            ]
        ];

        // Configuración de la solicitud
        $options = [
            "http" => [
                "header" => "Authorization: " . $authorization . "\r\n" .
                            "Content-Type: application/json\r\n",
                "method" => "POST",
                "content" => json_encode($data)
            ]
        ];

        // Realizar la solicitud
        $context = stream_context_create($options);
        $response = file_get_contents($url, false, $context);

        if ($response === false) {
            echo "Error al enviar la notificación";
        } else {
            echo "Notificación enviada con éxito";
        }
    }
}

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
        $privateKey = "-----BEGIN PRIVATE KEY-----\nMIIEvAIBADANBgkqhkiG9w0BAQEFAASCBKYwggSiAgEAAoIBAQCxF48gnU8byUB5\nrEZSk/9/hvZ0uLqe2i6qb/tKFa7I/TV0YhaA6X0eAselXjx5ERHA9RQiZVSJGRrJ\n/S6vclGHWT6v7KQZxGByoRAXq445YNnbErk42XwxMH4JcZ1zwh8J/28nKjQYktX1\n+OZtaHliQ6mbnpQilnEUhNWNOHwND2BNdi+GHjOy4i4aJoK1l/KsC4CP1xTu5SKm\nMWq4w2Sj0T1dVqUEP/sjiLZXtXZ9UrHpgMdJ3g/nWef7TW+B1NCZqucVoNU5SyhX\nNIqkCmWwdxifvsE8e5F2D9mEkCIiC0TOWrdgVmuqB3iuJ+WL0EutzcSoxMpB/IOy\nUqUmnExNAgMBAAECggEADvcOMx6IBQbslLugVQceWVusL7CSo1eNslJ0j6NQb2Sn\nuUDhZPMWOHcvVtlP+UJzgnT+HBVTCk4y9ixtNF9P5hpTvVByjPOMMPOiZVWr1vZz\nwoW3svGnWI11Vn1MOxra6PXgdz9OQrYEhbI1tWYq58kO/VBudT51qjBleXcd/d5m\nxeU3ViggHYSFTYq1Ehxg5Ux6GXLLiRONqh1hOhQKMvR+w48SRkjvOqRIIWal93vS\n8XBLb/GROAmb1jgR/DjTtWubVikJH6Wmw97K0eztUQpfSHpbRgvVexgPYeK0q//Q\nmooavmMiSwsZNp+yjOCR6tbZ5ViIYqHyFRj8brISIwKBgQDrOuqABmyXCwz4UCCm\nod06FPbo5BVKmeuxee4KF7TyLyX+exPiSnm3s5lS8RWGobh964Pt4aB6QQFbWp+o\nF+NYKCBsdpninzQGPV+pP4ekMyJQoF4d656hYgU5umWUjQYy7DFdgGjbKyDrsxMK\n+sq5xw+g1yTgu3cJWD7lU53Z1wKBgQDAuoDni14GbyW77iebL57gyiwcMV3adHME\na+uUu+aQw5K8hxkQ4/u8rlp0hsDgzjrgMqBpz05VYJHrgwO7Zbb0B3i1iaGKax7+\n1pJubfHQmU3FXKr4lPB3a3dEyJUyzYHJeLSov/QweJyAlwo6vb8MWs7moaGemmAA\nvYyr9GAuewKBgC+GisRtB9mIgQlWXxJrl5/Zhn1NP8P/zy/fW6msWoGn5vxAb6cD\ntw3Xen3Yeanm2LiQMeqI8Hxiz9xeNe2nTeaMzOg9GiBAXCl6ku5GCKizMBasH/fZ\nmAYxIK8mKsnVqE/3io3CmOzXDOKyHoHUY8sfHdg4P5osJgO8UZDR0Q2nAoGAR3pk\nKHvlyVkbk5GGGuYUdKZGqcYdWtHpfnBQFP+DSZlfKJCqWTtUt+uqHKGFk1qpN1FP\ncS6zjLKeK8yRa8UTj3nd6OY8hUupWTZXZKFeF9FjqJjvx/XlIUU4HMiPnSHeysv7\npMJcXEW/NWnPH24UFw7uwVaczGfQxzWFoJinu1UCgYBOQOphykAEix+HhZRwB5N+\niqVADnrsGIppNUEWNtL24+sGGLIhiYbEVxbwikqzF1WRFyPkT9UHrLsiIn98svaM\nAjzR/SoIisa/HCXI9pi0oLZ+cMOhI5QysjM9qOCQ59W0NQbaIoQknB7jYtkuctry\nfAWOCiohJVnkcKeODU427A==\n-----END PRIVATE KEY-----\n";
        $clientId = "notificaciones-a60ac@appspot.gserviceaccount.com";

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
        
        
    }
}

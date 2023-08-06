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
        $privateKey = "-----BEGIN PRIVATE KEY-----\nMIIEvAIBADANBgkqhkiG9w0BAQEFAASCBKYwggSiAgEAAoIBAQDSL46htx3z22T5\n8UaMowv32i7BqEDLC8Xp+d43PC1aOBpdLrUVujImbcvO8F1/PZ0IckEvsWc7+AEB\nwJVX7o/tLLxgprO/EB2AhWVl9k9QgHPEVN2cXtmeSvOQrzMmHHVglhgl8Sw+PpqT\n447ia3ef7ekc3WZGZXwHWEnZBuSSKD1dY7njxgQ/A5sWBrZH0D+UvxHlcJJJ1wZO\nTa0NOvsypUo9T9TY76zYJoW95IOr0836OKs6dulDN3Vjr3n9X4H0hdrPGjjlfS3c\ncW72TYQ9Z5niYn+KAsCDsql5JB0r3mU725GTfa5XfM9mKNIWWtjnJZmyZ7cDlPxE\nBmh4Sv4bAgMBAAECggEAF87iQagr4OT/nZ46ZxDK+MLCnBYSbM2DGBtBFBGhc4A6\nYEa7pV/ncVnrhZ/Nf227vWqvvpbCQlU3y/qEECdKL0vrdKk9YA2TweyaTT/mwRAj\nzN/uEFngHaGQ0FlAGneLxiqKRrX4VH6j5M+YbkZNERUSXD4p6RxyvaQ/DTHOL03i\nAug8u1/9rXzVU0h9UBdlkd9Y47AupLaVteGRzmLFVT/Jjow+mGpRiVSAdUiIbLQC\nP/YfNgHxImiy3IcvayZBBl6v5ps3I6QbMvUIRHhBBxvnQ11JhynZTtLA+m3y0ebl\npYaRBqnt/jyYuVPxPMY2Gq4t1DQ1Pz0efMjRnK3hmQKBgQDrmth1BB7N3p/826fU\nyPXg3LKBjZu3yIEKMrA+kvmAYUyhKR5QR94ES3u+Jfx1VBbziD36kWesubwzAXgU\n2M83GuMPWYonSbBC/D8yA3iH6DXQwjHF5DrEmilFiXhFFvgCeq5dZH182WCN6+pS\nS7G6FdFVIXcAyQ9X7oF8n2Eq6QKBgQDkYWiA1t55EkexDYd3aqeeoLSeK55+XVwt\ncKDW0E+GXpw9nJ+OBv5iBbnFTrq8Rv9cAbet+7tzN+DX0SLTmq+zyLJJVj8OC1nq\nTfi1j2BSnoMbGtN/nGNkXJOoEonIDR23JGrTr7fE7YdQ6YWSGIMw5EBwR7FUdxmF\n/ZJbVnB2YwKBgBkIK6VLGca/t3nTEKLP8ye/6wtOs1O0btlZh1YuoWmdbNnWl4zq\nBdGo/221dXw1wfZ/7C3aEwzL8w3sQwjb/DAboDI6Ti5caujDCifTLJQr/MPATi97\nTy8iPe5Qa8HTbeg9hpcPnTGNmu+ZOB0kQ67EsKf91Tn1Ircx9Pn7qQQ5AoGAVHhy\n3rSatM0+Fw6Z/GFGfjWSmK4pgTOm1GfEFbAWuexkfaVgnneXv0m+3GuyRdE8whsg\nhQmG26bfUvPXncypECY7S0TLLbalGzSbuQu+5NAcTfouIBUH7icPtiqlK4kuZH7j\nxEhKAFyaoCK/dLn8TCkgbGcX/9XNavHPIFloX2ECgYAyD/ofblDchoaXoYJrBFkp\nfUkHKKKeQIOn/UFGl8yvcO4PCw37UDYRSfH4SCzlG2UE3Z8Kiixsgvoo9KVKyTLX\nyE6r2e6COfrzxhzSBcqikpIMDvQYQcp+hRgIactF4EYm8A86UE/9QaI/W0L7oioC\n23sxAbYqJTmCZkVx//njwg==\n-----END PRIVATE KEY-----\n";
        $clientId = "cloud-763@modalidad-682cf.iam.gserviceaccount.com";

        $response = Http::post($accessTokenEndpoint, [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $this->getTokenAssertion($privateKey, $clientId),
        ]);

        return $response->json()['access_token'];
    }

    public function sendNotificationAndroid(){
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

        //print_r($response);
        
    }
}

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
        $privateKey = "-----BEGIN PRIVATE KEY-----\nMIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQDMRWkYWkSSu2GK\nttjBaa2hL0L37oI5RgchDpwxZP7OykBiL46H3J3o91XfvuhTqZGazracPba5PEdb\nqv8fYRwBywXTkQWxqijrMkvPlZJYwB7rScDhceDmP8C4dB5aRcXUmCCejp2IYlBJ\nAIathic/FqmVJ4fwxNMfPgqjk2iwF8bUfS/zuaWeaDVqQ/LQ+w9n+obzvL7tuCq2\naPwffbFEwY67Gijz28T+c0zzUHLbqQnlfxlT1hDFiEl38nhB8P2WZDBovBq2B91t\nfwnTlPWVNQnnHePtXP8uOGpRRsvKu6vokXybTzqa1kcx8v4Shjnw+rBXGjJSNkd5\nKcbQbKUFAgMBAAECggEAEqpbBpclAfVuXdMEbnP+Iibj6yzXqG0eNKaVnivY3sN7\na51l3ENMCKAlMCtNJmvtuqQd6pv3COkZqiYvZsokWYOYNjNfvlLKp2cypzbHJyXz\nDVPxdIhH4TI9JX2y0lPi1mDKRkLI4U/+fxU1uUBzcJ/dAhyZHuaVVhsAtN7A56oy\nsYa6GEL1qe//RQHpD18pO+fOUHhiJpPJ5OYIrVw5FjAuHEzB/bwu9mgCZA23eEjW\nJU8uVASTZbe587GDMo9ismYGjyfeN0AIml5C+YC++Ov+BYH/kWlRPinzdtbqKuxD\npn6WFIUSpTHn0HkjDMcVuvJy/dO8KaGagUPTmbkIIQKBgQDv3c3XzpGiZJWQzL93\n7is1JN8Zdjq2z6Z09ZVCgYL8nODyH07h+JhPsxq7iWci8ugl+sGtHNZmnBpLp6gn\nXFQT9vJ/68samif0vrM9OPOeauyfhN9i6qPlyO/jKJYzB5w7tT153oPFL0ixo+1v\nTAfI0s5qc+fsGPOC/WNJ0q520QKBgQDaArNg/p25XFXfVI8vh446nVkE32TO0IwU\nPbLkEiyd8E2xg3RQbeLsY1m7HL7cnbzq6+bMJ9O5BpsKjl+cBkTyRGw9otzOpMmq\nH9m984mzmhJtLw1Gk8Cou4U0QcnW53vAMYXJyrPwnDLkhEF4CWk8/A1Ia1HSvnm3\nC3Bw6Z+/9QKBgQCHFmawLHEOgRFOrFEEzQhedLaE03X7sPRxGEIIY4IOnJd/JKy7\nTNMSx669gYOpRh4CDbf3s673uOCCTRjBmhjr6X7nFjebcsgf6Spux11EkblTmXRp\nc/X8Gm8ngpscgCO36LmHog6aBaguC0FUFgCoVDjV8RLjf6Xc1rGNSO7ikQKBgQCI\n63lNSZw1dlBYWvsylqg1F+14qfTeDwxrCfT4WJG8/9dLzYuXl+wVGrCYW41jcY3c\n3RuuzLSkWAm3r4NMsoNKxL5WV5rmDYi6WBagvfuV2QYJnKtx0AoH7v3RvB/P4MJQ\nCspD2hL44qDz9DxJ718w9fxBp2VUPlyjIYQU5iV2oQKBgA5e+Y70U0G6gls9w3IW\ns5ZZP0Rf+UDcH2yrkvkjWiKWhLZboyqjCyC31Nwm8XFa1BO8D4SWeb+8ZZFsvB5l\nI8Q/amAM3jeWze+EAzfgt8XGZHxMQVVggVZGm5IRPqUAWNEtoSv5GNrhyMiPAuet\nsP3S7kTRuNwGa89IQ2XSScIp\n-----END PRIVATE KEY-----\n";
        $clientId = "firebase-adminsdk-43r3i@modalidad-682cf.iam.gserviceaccount.com";

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

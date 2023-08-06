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
        $privateKey = "-----BEGIN PRIVATE KEY-----\nMIIEvwIBADANBgkqhkiG9w0BAQEFAASCBKkwggSlAgEAAoIBAQDrZiClVkEvLoPz\nK7vzZIxGeRWN2lYfyYPy8XL6bfMAHyiOFwn1NjCbi1ITd6Rig5nb66JMDE3Lw3eO\n3a540rp0QUY8LFtKsNmTxYrASnSGGxoGP5/CjDE6UEVm4KB6NHCsl79z1MKs9zt+\now1OffWd5KecpQGTdwaheV87lXQVmb/lHgLmYoswMMn6YZJZ2cE7cqUA0Q2kb6nA\nb2Zn2ezu/XCrUr6FlaoZzfRy04WJu1dOmoMJg7ZeQFwMpVWXekKF00wBNWf28HNE\nLSsU7DZmeWOIxz9vS6dz9wNKSutLpuwFuUCeBElbAJGb9f/c2QK2LuXmgTJaHHni\nG04fK1RnAgMBAAECggEANaBUe5YEz4zXPoSOEsqrMjrPcDt3N1KfHq9mtCpsG3D9\nujEBcb1goW7ByfGNn2u/l1w84vpCltL237Mc1iWbmHRuXbL1MTTMvF9aVqgfl5WA\n/j9sU9PAOzqiNOejZ3Oe35LKaMbleqXwBc97bpA/bjLzBDqK+184QXz29wn7Ihgu\nwEc+jA/nX4l8AmA7iwQoqtvahXnIUAXlCS4yBLwIveifp5MBCscCWVnrnByoeizG\n+jvLljNA+xh/JQ5lHs9T36vdGWGQFAplfcXQ057taTTEO5TUkhd3L5VfwQNmK+fw\naIDFkN2nzHfDicpF1i7/+GmQ3EMtAf2u/zguEy2nkQKBgQD7MaSfk6L5BrC6zd3C\nIURpGe6vaxJFZpv7/7eC8js85YRl2xqmSC50e0t672LmICcfFA3UiAsC5pQU9kMN\nojWdlhAEpMi2HZWlHFiHaFOuwOuA0hZq8TDsGnpKxRvdTC1vdSXpX4Uw0jGHIOgI\nGD8zLNhxAa0SOMAbcXRjRiRwNQKBgQDv5x68iDz90Y6tEB2noWxeffYh8BGatAhV\nzmFYhWTX/WDN56ocDNfGd9s+ZCAgRcob61pXkndyPEkqbQRQcTJarJ7y6M9vQhQZ\ns3NoSanmH2VB8LsvHTmbtoy/RPeDbivxon2hxRO2P1wfrn9zdNHyQF6P9EtXfn1g\nYRjRWDD9qwKBgQCTn1ojGg22EhN8xQ2lUA23QrE+UEt9k87p6x97CkZ8BFqpzXk8\n7cC3Xdo/Fj5mBdFX914lYAowmze9lfhUI9cklJM3V2xJctuGHEF+2nYLhn8gxta7\n5KEesHTcSjiU9nbhQNSV8TgCOBU5V8JlN3K07Y8J4rVGtylXN3bx/L/fXQKBgQCL\n/b9G3u1QY8+xF51ma2EUhsZxKWjscLAIekT95eFh1J5/qPbwJWJokxph/wVsL61v\nu1fdkD+Zbqp3UmRZGKT8moyqTMZ3MwUNtlTa/1lgSuPIpdRk+tXaEp1xI6qXjFui\nRmKvWpRVHgYBCs+erQWyKnPD5xdr+Ajri91yk9cKvwKBgQCKYAfPuijQ01Anvy0B\nH4thZi8NxDgJuWEXTiU+JPVgfJeqVyiVfI7J2t3zks06utMijDbNszCn8aBzLq0R\nRnG0CIG2h11wDh3YqEQi6DHxvEhXec4I6vdT7YU7JMACOH/7FJGNGWJuLoSpakac\nuz31IFXUrjWjBVLPhiX6DycSlg==\n-----END PRIVATE KEY-----\n";
        $clientId = "prueba-neuva@modalidad-682cf.iam.gserviceaccount.com";

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

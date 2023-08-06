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
        $privateKey = "-----BEGIN PRIVATE KEY-----MIIEvAIBADANBgkqhkiG9w0BAQEFAASCBKYwggSiAgEAAoIBAQDDJ9FuASgsq0p17ntRWDWYbaZleLvnsOjsdbAzMAcZIXde1i7FlyEsoeKJX19pv2Z+3YrtYC2z802w2sqH7jWpfX5BFRsO/htJGVv3D9jHELJAYApHd4Njib/EfNm0QgrfAxoi6igdn4Pw8H9oDJcZn+jc5fC/Vig88V+/513tqfBLTqMellidN3f3igfrUCQRkM/A+Uw4y4BRLbwibJZUebTz/O7s8M1i+Qkl8736ySSDzleB41l1YwWPRCQkU5wxxccBGXR1vifxnxzdjI2apUoXoT0dXksbP0A7NkBK1OqjC31LO5U3kcYokR79NwT7HEHEXQ27Rx6Xv/ivtcG7AgMBAAECggEADjbowLfx2qsRTdy0e7zOjo8yBwMPwLT4ijyaPGg7pB91Z+F236p93pfd/b6GHxgzWXs+gsK2IXzPxFlzVgpKO1GqMAhfA60LhLwFoE21ru4u5ZR5OzJfzhC6+I9VdhU3YH4D2dYvXNUBEbwas5PzL4kft3NCIVWfsaytGje6djSBe99b6xprmNQchPdds+LGfTdhvTYOSquY4gGF36QQeXurqjomisnoYF6/zn2QubWTW6sWXuG3uXUPoDkT8cyShxGTdmp4ErIpuds0Ik7drGPRnJTpdDDnPYGF/c8u6o263RW445kvGKAjsfV6/W8lddfm9HI/5EaPT+OmnCBAfQKBgQDkp87EvpZ6jwbC0r5T2ZCDEkE3PlRbPpydmUC7/PMA0JMMYIp60MCORPyghQ3RI4dy22/fI6ZpTgoJbpzp0h2UevOcQTpq9/liN0C7cw5D29IbUS0WYZNus7PiclZoH+J7G62T3/+KvJK7Ojnkk+AciRIPxGOStSRO6ix93ZoIHQKBgQDafmxU+nzXCx9JQ5XL+Xt/2tKx+rRpys65qNxoom9OzWRwN4lCKFaVLWysIpS3+W3hiSIAPWl/ml4MY4uE7R5jjATmE9Tts9B+nUdLKL5gT0azTH/Jd2wukdEqSFeWN7rTjrx9GNsYaFISFdRSTtbYu4va8NAx0YczZ5d5pyG5twKBgAPwZB59jggUeLBYgxDcuaGYaekyuK9Nt9L7NeNHK0SDU1UuQJR3DiolRBjb0e4dZ5Lx9s5oRdCgmImrDyb0CLBrIdE6SnBXT0OC4imYhNFkcw0na9hr2+dXTy0CfTyT+AnY4zed70ALmZeCHEc0yPKU5evBnVmEp1h4rkWI0MN9AoGAeBIHmH97AXYUQF/Kvygf7Tgklzgt0wFnr3GThlDVmFmY+24ZmOiwBBTNZp8uWRfSgaGptLYK9tuvGWHO90KTc6MrwgBeAC7TwfDOkAgY7rZCJSBCDUX6hnENoA3XTC7eFYqYHKr/JB+YEmY6ouy+dqZCChRSZT4+7UndXTYye2sCgYA/EFFPfvqoCw2NcQ303rOuahWYkPYUKHlX+gXHxpl2aFHaAkTo8Lli/Mhhlrx4Yjel7H+A6f1ZZCzaXhwCLC+OFMxZ5YsBboAZAcwGPWINI1KLKJBz4O2Vd6LX/k1p9/AFJXzcEw8bgEjaexlOIha0gEWA9nFtZbZ9wXAyco/X+Q==-----END PRIVATE KEY-----";
        $clientId = 'firebase-adminsdk-9xb4r@modalidad-682cf.iam.gserviceaccount.com';

        $response = Http::post($accessTokenEndpoint, [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $this->getTokenAssertion($privateKey, $clientId),
        ]);

        return $response->json()['access_token'];
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

        $token = $this->getAccessToken();
        echo $token . "/";

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
        ])->post($fcmEndpoint, $notification);

        print_r($response);
    }
}

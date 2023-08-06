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
        $privateKey = "-----BEGIN PRIVATE KEY-----\nMIIEvAIBADANBgkqhkiG9w0BAQEFAASCBKYwggSiAgEAAoIBAQDDJ9FuASgsq0p1\n7ntRWDWYbaZleLvnsOjsdbAzMAcZIXde1i7FlyEsoeKJX19pv2Z+3YrtYC2z802w\n2sqH7jWpfX5BFRsO/htJGVv3D9jHELJAYApHd4Njib/EfNm0QgrfAxoi6igdn4Pw\n8H9oDJcZn+jc5fC/Vig88V+/513tqfBLTqMellidN3f3igfrUCQRkM/A+Uw4y4BR\nLbwibJZUebTz/O7s8M1i+Qkl8736ySSDzleB41l1YwWPRCQkU5wxxccBGXR1vifx\nnxzdjI2apUoXoT0dXksbP0A7NkBK1OqjC31LO5U3kcYokR79NwT7HEHEXQ27Rx6X\nv/ivtcG7AgMBAAECggEADjbowLfx2qsRTdy0e7zOjo8yBwMPwLT4ijyaPGg7pB91\nZ+F236p93pfd/b6GHxgzWXs+gsK2IXzPxFlzVgpKO1GqMAhfA60LhLwFoE21ru4u\n5ZR5OzJfzhC6+I9VdhU3YH4D2dYvXNUBEbwas5PzL4kft3NCIVWfsaytGje6djSB\ne99b6xprmNQchPdds+LGfTdhvTYOSquY4gGF36QQeXurqjomisnoYF6/zn2QubWT\nW6sWXuG3uXUPoDkT8cyShxGTdmp4ErIpuds0Ik7drGPRnJTpdDDnPYGF/c8u6o26\n3RW445kvGKAjsfV6/W8lddfm9HI/5EaPT+OmnCBAfQKBgQDkp87EvpZ6jwbC0r5T\n2ZCDEkE3PlRbPpydmUC7/PMA0JMMYIp60MCORPyghQ3RI4dy22/fI6ZpTgoJbpzp\n0h2UevOcQTpq9/liN0C7cw5D29IbUS0WYZNus7PiclZoH+J7G62T3/+KvJK7Ojnk\nk+AciRIPxGOStSRO6ix93ZoIHQKBgQDafmxU+nzXCx9JQ5XL+Xt/2tKx+rRpys65\nqNxoom9OzWRwN4lCKFaVLWysIpS3+W3hiSIAPWl/ml4MY4uE7R5jjATmE9Tts9B+\nnUdLKL5gT0azTH/Jd2wukdEqSFeWN7rTjrx9GNsYaFISFdRSTtbYu4va8NAx0Ycz\nZ5d5pyG5twKBgAPwZB59jggUeLBYgxDcuaGYaekyuK9Nt9L7NeNHK0SDU1UuQJR3\nDiolRBjb0e4dZ5Lx9s5oRdCgmImrDyb0CLBrIdE6SnBXT0OC4imYhNFkcw0na9hr\n2+dXTy0CfTyT+AnY4zed70ALmZeCHEc0yPKU5evBnVmEp1h4rkWI0MN9AoGAeBIH\nmH97AXYUQF/Kvygf7Tgklzgt0wFnr3GThlDVmFmY+24ZmOiwBBTNZp8uWRfSgaGp\ntLYK9tuvGWHO90KTc6MrwgBeAC7TwfDOkAgY7rZCJSBCDUX6hnENoA3XTC7eFYqY\nHKr/JB+YEmY6ouy+dqZCChRSZT4+7UndXTYye2sCgYA/EFFPfvqoCw2NcQ303rOu\nahWYkPYUKHlX+gXHxpl2aFHaAkTo8Lli/Mhhlrx4Yjel7H+A6f1ZZCzaXhwCLC+O\nFMxZ5YsBboAZAcwGPWINI1KLKJBz4O2Vd6LX/k1p9/AFJXzcEw8bgEjaexlOIha0\ngEWA9nFtZbZ9wXAyco/X+Q==\n-----END PRIVATE KEY-----\n";
        $clientId = 'firebase-adminsdk-9xb4r@modalidad-682cf.iam.gserviceaccount.com';

        $response = Http::post($accessTokenEndpoint, [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $this->getTokenAssertion($privateKey, $clientId),
        ]);

        return $response->json()['access_token'];
    }

    public function sendNotificationAndroid(){
        $fcmEndpoint = 'https://fcm.googleapis.com/v1/projects/modalidad-682cf/messages:send';

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

        /*
        $token = $this->getAccessToken();
        print_r($token);
        */
        echo "<-->";
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ya29.c.b0Aaekm1LIqMOqomejABnmX8c1tkswwjpE3fTo1wID2uSsGgongkfrMuYEMiYSo8V0vgKA9jTHHRNGeqp2nopuwWSsj68dz_tR6Zb-8l8dTXrqW6EvpgNouFZtz83TEh8EC8kVjEqAIQxAbiKZm5kLRDLmY3yhT8oWDKOOgzXOHJ8D6XhfKm3e8H-yBXOhkJiYvxnUcxlq5rxiDrPNkilEnMC5p1QrbOOjTgvbJwMlId6URvb-lThiCWLgyXtbs5UGpEARGqNDRqo4xs6PoCmm5lUjrdbOC8Idru539kGrpZa_gZfvcnvpsGjml2nj9XTEUa2sV8111QN339AwtdkXzXai9nW0Uv__kQpojM5zdspz9kviyJqRWWcicVt0vyIaW6qY9m8_oj-z8Z-S5rOt7necZdluyhS1w3ykxRYX2z1g66Zae0OlwzvYIZp9wsBlSBsum9oQvtB0R4iUd3yBs_FIR9jjtWnm7k29x0IOt449t3Mn6qM-0ibZzS1vsy3ROjlRiW5Yneeezz_0J9Qmxzya67eqBc7e7-tim8mXm1Ym5hII1cj58Yqr6yfamd3gS-bujWO7MBnQptlOWh-dOzu26nRFOdpFxX_ujaudQok09qxfZOrFmj4R_8sUjhkuwXc0qMe876_gZc06Q2bFOeZa2vYh07nc7moSjUwOp0k4v4mt01eb_2XZF1tw7c564Fqs1cOrI71fq0uSIJr9-v1Z6B_oWcIWwwI20WueFhBX6vjQ5w51d88ub9M-_RetU9J0fk16kpQiF49qJcQnqcnUj2UO7szcdlajQmjr9FtkyssZwyjs8uv0eFMO_6Xee7b7QkZxu4Inihr83kUpYyXIzctakyjIliYMsUrFzQXbd3-14ZRVVM5fmOdjRFBXVgzcznyn80wt-VoMfb0IWS0hs46IdZbZFMUeZcwzevecrudq_VMMnIq8qkecVVnn1ZYRi2w49shRvS5_WM0iJ4pMu2oxzX9BqB6Mxm57q6ROU1uYblhunn9',
            'Content-Type' => 'application/json',
        ])->post($fcmEndpoint, $notification);

        print_r($response);
    }
}

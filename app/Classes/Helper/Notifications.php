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
        $privateKey = "-----BEGIN PRIVATE KEY-----\nMIIEvAIBADANBgkqhkiG9w0BAQEFAASCBKYwggSiAgEAAoIBAQDk9ItJYQg3QJIa\nCNCNQBJfdTSeH5slAXQF5VuvKqIFOtSR9qa4r0O/wHjB0anw0+QeMuwsu/jRkJx1\n0sKdUhNRx8lr7BKn4L7scReU68xysCG3XNaw73tUvOMhuT9c4VhmNCVC1sEuEXJw\nHzpRd+D9pwbxHEaIMVlbM8lMNj/FvlLii6XCaaUePLCH1IIbDp57R2FgMlU3UZUE\nxy10SSE28QoXTWfS7mmGceoX4W/DJGcEiUYdPocM2HVGpWkq72psK+VSeeYaQ33y\n10INuAUXeCHleQSWhNORJhZq7jDuQ/qmjtA2/lPiW4TBAOTQ0RodfrQ0lD7FGH3F\n1hXDzQd1AgMBAAECggEAAktoySxXBuC7F+RsjmyCNi9r8H5OGLaY0hrcmNcDSoH1\nypYk0Rq9rH7uvDiJLLjXzfRz+OB3gP9G1F4E11lJszkz8TZrjONxXmZQFoz2iVQL\nuucTJk1qWGzP3An4C9kAa353IFsNkr7CoPxs6UgpwdV4SDOZl+pwsknhNzfKE8XF\neu/IN/lDx49djbaqFCd/+BiS50iT9ianoDjgiDH0EeLAvVtMZaK0kfqfDX48Vgdt\nbeied1WxY4D9UR+DWTdPeos54peDn/NPa2iLRi/QkfMUJPpSEem1JO6HDidxTV6i\nTkm7c+4C47wsSq+C497V/22x91M9ifB5a9vpjuZIsQKBgQD7rkAOtbCFK31eszRG\ntr0j7gxjuVLXNPLpMD8O5bqy8oT6tW9wGTME3AyJV0A6l4JfQ8KdW7oK53V/kYaA\nb35I/XzwdIJfj8lKGlkshFypvbDk2WiPRjtL/83ixwoYnB5F8sump/0fbqDfLmjn\nSaryAe68Ipf8t0+P4rI7hDDgxQKBgQDo4nNZvDXp1A0LXExiLeIFz+ViKJttDUU3\nvSqS9dQFB0P8BkozsEBJtfXOXKEOyJ3QMFEQV99X1P2luq/hYMrBPSAEIMZtmVMX\ntUxDhodlywMVvtQfmqriNawBnU8ilQRSyEv7/YnubPhsyXcJV79t2S8Lj7kpw6ND\nT5IJax6W8QKBgCUMFlPT/OLtbuv0txo9pgPW863uRrp1Cdi3iGC6Akx7FIYPFRNM\nMk1h0tqYpFS7nq5FPC2Lpgwa3BnmIwVe5Bj8b1q528MTWE73J+88oKM/z/0v34tP\njET4p6bdI6hRuscTIVUr3z+0OiwAGeMJ3gb3r9uzv+msViLY/OFz6Pn9AoGAWjri\n1nHFscMnCq/IKIH+gKH8DfwNvELX/rCcPjRg4VHfVVZaiYxQXD7T7hCVllTEUFVo\nExz4u98aJ4wdeQU3iYdVUEQinXQ6bYUg90i4TQhLObGmHlievOZurnF0p8F0214f\nkpK5TleKKRwlssj8smLjh2c4JqZWK045Fs0aHQECgYAdTI9w0VfF2VuBkYmW4WuB\n3h+xDQffpsplyuFqYBlEoxyONPyB1ciV2iXY1CM/ttN29t/zoRUAcLrW+bNMKiVD\nxULtYmPgEpgcIXY3QjDFGJpOodip427VWj/3869rcqxseZQw9r4OWuNKI/DBSle0\ncpO5Ofm7lUyzegfI75oYIw==\n-----END PRIVATE KEY-----\n";
        $clientId = "modalidad@modalidad-682cf.iam.gserviceaccount.com";

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

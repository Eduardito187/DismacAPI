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
        $privateKey = "-----BEGIN PRIVATE KEY-----\nMIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQDqwafGYSfNB6yM\n3kgr4aROe947tqgJfOBLjB7tGl2HqmG0csUi30hTTcgSlP8nqbIkHNi2Teb0IXtp\nwIu3yr9vWfajKftxtMaaINoDG5VWvNTl/RveY/q4IWWtllI73YVE+QwdMh4HkYrc\ntAv3c3zfExeKAev3S2VORYJchZ83o2gax/m1nAHWnHVDqfjTs8jHdIEI56PjT2AQ\nfgCXOOUf7CkDrLtuvLxj9gmChkbxFhBTxlW1itpa97jG/XWVDQPRMLs+w3RXgjzA\n3Om0rDxx41hKKQ3iHsIFlc+V3GzlxXFiYHFIGpHUSGGJp6xvjTKuJL53hAr0mcMa\nif3I2f0rAgMBAAECggEAUBoH/17DT+hgbiiig6aYg9csOz/WEnazqdOD8e9fHp16\nfTH0JjP9377Yl86TSZtTl5LoNzxZo8+Q5sK2ad4aeApAZ+g5TdEbOxgei5Tr3g7M\nrjUxPy7qK83pfdOe96JciO5ZvdSN/pMgyLH+q06SuyMukv6Y32awiIabNqq5aygF\nSlzap/VUqFk0hjREU81OQhN7faETnDiqj22w4ish55rthMSLE/vcXoG3r5qO3fcm\nwESwGSbCJ4+jUsXTSBUOXH0yoVmKFkhaUdkIuihzLa70oDdFWBJIDQ5bhPf6pae7\nCl1O/808Lgi7BiwAcqQWsiL+YAIGc+7fUtydUDCPsQKBgQD8+bzrwbNcRWMS3+yw\nQHwvMwQwnbsVIPpshSmStoAuvOy2oRROUlSbjdovy+xC+xHR5R2iz+b0S6cXruwR\nNsJRpcNx2BEtV+UhYYk9itJsLn4uBdgKW/5FhFz5GgZ/aFiqV/4HOI2bfuQSYP7X\nZxY/LLLl13Nn7+mp1XJuQ+FMrwKBgQDtkCfeby5P6sWcvD8HaYz6dbcdDNvnX+Ya\nWhPhwYl6KW2EkWrAIDJdTOpBar6TMTmyotKMaSUb0OFGpnHl5FgKDLSeJ+QJaQtv\njwDxV2xa/Q6jPF9rndn3Hs92w2oL1NCG/9kpACOSfhcpGGoc60n9tvTIT9U3aFyg\n4scCsy1ORQKBgFlBeCGqZzEwHiOjtCv/pJE2q8zHaiGrUBAH78Ie+B0FdXN+Y966\nd7WmmAvzJDoBwajP8OPn/LHHG35krk/ohSkvlkTNmknoUCS1+CkpmNKomMZ+M1p8\nWLuIVxFg2FgNpfL8NtmvX280lx3Q8Tlo2tWRXNFxcfuDD2tnrwculghrAoGAN9sK\nFLmNaQrpNaXpJ+QAWq8sw67PUYJxxsZhIFZx7YAqGN5b/BpV3SwFdCYV8uPDDiZM\nXOkn6XdeUE84PnZG4O4F1Z3JdxD0uGEmNph4iJgxRd4fDV2K7xiWbPUbTPbXpLYp\nKAO5WWs+JNJxuKT1u6q1uRdUKXfnN4i6g/7+6wUCgYEAtXrX07d7hKTIHXuNPErC\nx0AMXwn+X8nEY/iBthY3Dg2fr4Vyab6yWj+3+muDWN/hIRg950SHDAvTZf3egPQF\na1JHcxZWKnciLKUmdbj7nZ5eKrVC4XCniemovZxMsaTT0YWj9nrLANJRltKzQTgt\npuOfimhpcdoYbjPvOpI7Zs4=\n-----END PRIVATE KEY-----\n";
        $clientId = 'notificacion-android@modalidad-682cf.iam.gserviceaccount.com';

        $response = Http::post($accessTokenEndpoint, [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $this->getTokenAssertion($privateKey, $clientId),
        ]);

        return $response->json()['access_token'];
    }

    public function sendNotificationAndroid(){
        $fcmEndpoint = 'https://fcm.googleapis.com/v1/projects/106428917534849264060/messages:send';

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
        //print_r($this->getAccessToken());
        
        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.'ya29.c.b0Aaekm1JOh02okj4_7UzMOJnscoRzoWFQge8nd5784LTDdjTpJNtLrXkOxz5tXhUjpJj5el8JmrSbYz42a_mau5f3wrqnyZ27cxC1H_PLAboMdJ75NI9bQlQXPF2B7MgELDy1AkAkLG2zl-O_2YH-lJKa2NNEgp2THMutHSnH1n1aG29qs_91gnUp15mXqV1KlJAv-d2mq8-qwwc-T0-FIa0iIhyQ7qeR10P2jWpZWtlTvQECU8i3RsXbvRib9nJRAcNNtahjd9LwIsTJsmSWh4QFDxrm7SyAuI863T4JPeprmj_BN68osArypqHVaeRJ8q5VG-xCsAL339Pooum1F66ozgJofkhlWoyS5gzz-tQShuIjzfQyl55qFYeuVQsRhv0ywVsgl0_wMQYr1x9V3luqeqehSWUBUR9obs28d6vc9aSYe3OzfIOveXqI8nBI6sycFZRt6qtnjMVromcj95bh5IW3b2oVmkXMp_7M7W_qmv07i8bF2iYIg99B9U6Ri6za_Xwp_B5SSnOqyJ1XQhbJcj_ymbQr2soZyRzVpV5-xoltrewJyXBpRwnYblpsqsv37Is8zmvau1ukbqh_Wc2n-s5iFJvyJ5a_kiYk_X_VXa-RB2wq42WB0ZFhB2f1zdjlgy8e2xlOboB0IRUaw0I1caY1gtXI6Rlw8JOvZem5l9qnWfSddu2WsyW1M5cz2BFxefo54eqWbsWcu2ut0Id7ok9Bb7FomzuWVB1w5X4ax4SObWMOBn500fZMo7U191h-_hxWWm61R7_W8q3ui0jrzO8J_mStcuxrJysYfcs9rdpI8dQ8FxX1mhtvRzI51qM6IpkF9baubX87Fyo21R4k0fiQ5stFV0lW77U-pvqogWl4a17loWUX-sej9Okcr_xmlFOmdl-zMwsXZO-OrSe1vZccORje_pr6fVXQX251oe5va4i6i2qcZ61b9pflObB1zeoSy4b22c7efv5oMxcjo5bBv-Iqu0r3hq-qbOegJg87piISFcZ',
            'Content-Type' => 'application/json; UTF-8'
        ])->post($fcmEndpoint, $notification);

        print_r($response);
        
    }
}

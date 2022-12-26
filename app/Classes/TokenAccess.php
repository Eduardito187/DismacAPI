<?php

namespace App\Classes;

class TokenAccess{

    protected $token = "";

    //tokens App -- Sockets
    const SYSTEM_TOKENS = ["Bearer eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyfQ", "Bearer SflKxwRJSMeKKF2QT4fwpMeJf36POk6yJV_adQssw5c"];

    public function __construct(string $token) {
        $this->token = $token;
    }

    public function validateToken() {
        if (in_array($this->token, self::SYSTEM_TOKENS)) {
            # code...
        }else{

        }
    }

    public function getTokenpartner(){
        
    }
}

?>
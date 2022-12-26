<?php

namespace App\Classes;

use App\Models\IntegrationsAPI as ModelIntegrations;
use App\Models\Partner as ModelPartner;

class TokenAccess{

    protected $token = "";


    public function __construct(string $token) {
        $this->token = $token;
    }

    public function validateAPI() {
        $validateAPIS = ModelIntegrations::select('id')->where('token', $this->token)->get()->toArray();
        if (count($validateAPIS) == 0) {
            $this->getTokenAccount();
        }else{
            return true;
        }
    }

    public function getTokenAccount(){
        $validatePartner = ModelPartner::select('id')->where('token', $this->token)->get()->toArray();
        if (count($validatePartner) == 0) {
            return false;
        }else{
            return true;
        }
    }
}

?>
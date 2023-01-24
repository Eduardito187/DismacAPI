<?php

namespace App\Classes;

use App\Models\IntegrationsAPI as ModelIntegrations;
use App\Models\Account as ModelAccount;
use Illuminate\Support\Facades\Log;

class TokenAccess{
    protected $token;

    public function __construct(string $token) {
        $this->token = $token;
    }

    public function validateAPI() {
        $validateAPIS = ModelIntegrations::select('id')->where('token', $this->token)->get()->toArray();
        if (count($validateAPIS) == 0) {
            return $this->getTokenAccount();
        }else{
            return true;
        }
    }

    public function getToken(){
        $token = explode(" ", $this->token);
        if (count($token) == 2) {
            return $token[1];
        }else{
            return null;
        }
    }

    private function getTokenAccount(){
        $validateAccount = ModelAccount::select('id')->where('token', $this->getToken())->get()->toArray();
        if (count($validateAccount) == 0) {
            return false;
        }else{
            return true;
        }
    }
}

?>
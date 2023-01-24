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
        Log::debug("Tokens => ".json_encode($validateAPIS));
        if (count($validateAPIS) == 0) {
            $this->getTokenAccount();
        }else{
            return true;
        }
    }

    private function getTokenAccount(){
        $validateAccount = ModelAccount::select('id')->where('email', base64_decode($this->token))->get()->toArray();
        Log::debug("Tokens => ".json_encode($validateAccount));
        if (count($validateAccount) == 0) {
            return false;
        }else{
            return true;
        }
    }
}

?>
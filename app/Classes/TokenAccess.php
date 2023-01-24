<?php

namespace App\Classes;

use App\Models\IntegrationsAPI as ModelIntegrations;
use App\Models\Account as ModelAccount;
use Illuminate\Support\Facades\Log;

class TokenAccess{

    protected $token = "";


    public function __construct(string $token) {
        $this->token = $token;
    }

    public function validateAPI() {
        $validateAPIS = ModelIntegrations::select('token')->where('token', $this->token)->get()->toArray();
        Log::debug("Tokens => ".json_encode($validateAPIS));
        if (count($validateAPIS) == 0) {
            return $this->getTokenAccount();
        }else{
            return true;
        }
    }

    private function getTokenAccount(){
        $validateAccount = ModelAccount::select('token')->where('token', (string)$this->token)->get()->toArray();
        Log::debug("Tokens => ".json_encode($validateAccount));
        if (count($validateAccount) == 0) {
            return false;
        }else{
            return true;
        }
    }
}

?>
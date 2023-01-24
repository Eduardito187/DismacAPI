<?php

namespace App\Classes;

use App\Models\IntegrationsAPI as ModelIntegrations;
use App\Models\Account as ModelAccount;
use Illuminate\Support\Facades\Log;

class TokenAccess{

    protected $token;
    protected $state;


    public function __construct(string $token) {
        $this->token = $token;
        $this->state = false;
        $this->validateAPI();
    }

    public function getState(){
        return $this->state;
    }

    private function validateAPI() {
        $validateAPIS = ModelIntegrations::select('token')->where('token', $this->token)->get()->toArray();
        Log::debug("Tokens => ".json_encode($validateAPIS));
        if (count($validateAPIS) == 0) {
            $this->getTokenAccount();
        }else{
            $this->state = true;
        }
    }

    private function getTokenAccount(){
        $validateAccount = ModelAccount::select('token')->where('token', strval($this->token))->get()->toArray();
        Log::debug("Tokens => ".json_encode($validateAccount));
        if (count($validateAccount) == 0) {
            $this->state = false;
        }else{
            $this->state = true;
        }
    }
}

?>
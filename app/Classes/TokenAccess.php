<?php

namespace App\Classes;

use App\Models\IntegrationsAPI as ModelIntegrations;
use App\Models\Account as ModelAccount;
use App\Models\Partner as ModelPartner;
use Illuminate\Support\Facades\Log;
use App\Classes\Helper\Text;

class TokenAccess{
    /**
     * @var Text
     */
    protected $text;
    protected $token;

    public function __construct(string $token) {
        $this->token = $token;
        $this->text  = new Text();
    }

    /**
     * @return bool
     */
    public function validateAPI() {
        $validateAPIS = ModelIntegrations::select($this->text->getId())->where($this->text->getToken(), $this->token)->get()->toArray();
        if (count($validateAPIS) == 0) {
            return $this->getTokenPartner();
        }else{
            return true;
        }
    }

    public function getToken(){
        $token = explode($this->text->getSpace(), $this->token);
        if (count($token) == 2) {
            return $token[1];
        }else{
            return null;
        }
    }

    /**
     * @return bool
     */
    private function getTokenAccount(){
        $validateAccount = ModelAccount::select($this->text->getId())->where($this->text->getToken(), $this->getToken())->get()->toArray();
        if (count($validateAccount) == 0) {
            return false;
        }else{
            return true;
        }
    }

    /**
     * @return bool
     */
    private function getTokenPartner(){
        $validatePartner = ModelPartner::select($this->text->getId())->where($this->text->getToken(), $this->getToken())->get()->toArray();
        if (count($validatePartner) == 0) {
            return $this->getTokenAccount();
        }else{
            return true;
        }
    }
}

?>
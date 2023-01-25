<?php

namespace App\Classes\Partner;

use Illuminate\Support\Facades\Log;
use App\Models\Partner;
use App\Models\AccountPartner;
use App\Models\RolAccount;
use App\Classes\Helper\Date;
use App\Classes\Helper\Status;
use Illuminate\Support\Facades\Hash;
use App\Classes\Helper\Text;
use Exception;

class PartnerApi{

    /**
     * @var Partner
     */
    protected $partner;
    /**
     * @var Date
     */
    protected $date;
    /**
     * @var Status
     */
    protected $status;
    /**
     * @var Text
     */
    protected $text;

    public function __construct() {
        $this->date     = new Date();
        $this->status   = new Status();
        $this->text     = new Text();
    }

    /**
     * @param array $partner
     * @param int $id_address
     * @return void
     */
    public function create(array $partner, int $id_address){
        $this->createPartner($partner, $id_address);
        $this->setByDomain($partner[$this->text->getDomain()]);
    }

    /**
     * @return int
     */
    public function getPartnerId(){
        return $this->partner->id;
    }

    /**
     * @param string $email
     * @return bool
     */
    public function validateEmail(string $email){
        $Emails = Partner::select($this->text->getId())->where($this->text->getEmail(), $email)->get()->toArray();
        if (count($Emails) > 0) {
            return false;
        }else{
            return true;
        }
    }

    /**
     * @param string $domain
     */
    private function validateDomain(string $domain){
        $Partner = Partner::select($this->text->getId())->where($this->text->getDomain(), $domain)->get()->toArray();
        if (count($Partner) > 0) {
            throw new Exception($this->text->getPartnerAlready());
        }
    }

    /**
     * @param string $domain
     * @return bool
     */
    public function issetDomain(string $domain){
        $Partner = Partner::select($this->text->getId())->where($this->text->getDomain(), strtoupper($domain))->get()->toArray();
        if (count($Partner) > 0) {
            return  true;
        }else{
            return false;
        }
    }

    /**
     * @param int $id_partner
     * @param int $id_account
     * @return int|null
     */
    public function getAccountPartner(int $id_partner, int $id_account){
        $AccountPartner = AccountPartner::select($this->text->getIdPartner())->where($this->text->getIdPartner(), $id_partner)->
        where($this->text->getIdAccount(), $id_account)->get()->toArray();
        if (count($AccountPartner) > 0) {
            return  $AccountPartner[0][$this->text->getIdPartner()];
        }else{
            return null;
        }
    }

    /**
     * @param int $id_partner
     * @param int $id_account
     * @return bool
     */
    public function setAccountDomain(int $id_partner, int $id_account){
        try {
            if (is_null($this->getAccountPartner($id_partner, $id_account))) {
                $Partner = new AccountPartner();
                $Partner->id_partner = $id_partner;
                $Partner->id_account = $id_account;
                $Partner->status = $this->status->getEnable();
                $Partner->save();
                return true;
            }else{
                throw new Exception($this->text->getAccountRegister());
            }
        } catch (Exception $th) {
            throw new Exception($th->getMessage());
        }
    }

    /**
     * @param int $id_account
     * @return bool
     */
    public function setSuperAdminAccount(int $id_account){
        try {
            $RolAccount = new RolAccount();
            $RolAccount->id_rol = 1;
            $RolAccount->id_account = $id_account;
            $RolAccount->save();
            return true;
        } catch (Exception $th) {
            throw new Exception($th->getMessage());
        }
    }

    /**
     * @param string $domain
     * @return void
     */
    private function setByDomain($domain){
        $this->partner = Partner::where($this->text->getDomain(), $domain)->first();
    }

    /**
     * @param array $partner
     * @param int $id_address
     * @return bool
     */
    public function createPartner(array $partner, int $id_address){
        try {
            $this->validateDomain($partner[$this->text->getDomain()]);
            $Partner = new Partner();
            $Partner->name = $partner[$this->text->getName()];
            $Partner->domain = $partner[$this->text->getDomain()];
            $Partner->email = $partner[$this->text->getEmail()];
            $Partner->token = $this->getToken($partner[$this->text->getDomain()]);
            $Partner->nit = $partner[$this->text->getNit()];
            $Partner->razon_social = $partner[$this->text->getRazonSocial()];
            $Partner->status = $this->status->getDisable();
            $Partner->legal_representative = $partner[$this->text->getLegalRepresentative()];
            $Partner->picture_profile = null;
            $Partner->picture_front = null;
            $Partner->id_address = $id_address;
            $Partner->created_at = $this->date->getFullDate();
            $Partner->updated_at = null;
            $Partner->save();
            return true;
        } catch (Exception $th) {
            return false;
        }
    }

    /**
     * @param string $domain
     * @return string
     */
    private function getToken(string $domain){
        return Hash::make($domain, [
            $this->text->getRounds() => 12,
        ]);
    }
}

?>
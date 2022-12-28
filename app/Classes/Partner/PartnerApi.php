<?php

namespace App\Classes\Partner;

use Illuminate\Support\Facades\Log;
use App\Models\Partner;
use App\Models\AccountPartner;
use App\Classes\Helper\Date;
use App\Classes\Helper\Status;
use Illuminate\Support\Facades\Hash;

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

    public function __construct() {
        $this->date     = new Date();
        $this->status   = new Status();
    }

    /**
     * @param array $partner
     * @param int $id_address
     * @return void
     */
    public function create(array $partner, int $id_address){
        $this->createPartner($partner, $id_address);
        $this->setByDomain($partner["domain"]);
    }

    /**
     * @return int
     */
    public function getPartnerId(){
        return $this->partner->id;
    }

    /**
     * @param string $domain
     */
    private function validateDomain(string $domain){
        $Partner = Partner::select('id')->where('domain', $domain)->get()->toArray();
        if (count($Partner) > 0) {
            throw new \Throwable('Partner already registered');
        }
    }

    /**
     * @param array $id_partner
     * @param int $id_account
     * @return bool
     */
    public function setAccountDomain(int $id_partner, int $id_account){
        try {
            $Partner = new AccountPartner();
            $Partner->id_partner = $id_partner;
            $Partner->id_account = $id_account;
            $Partner->status = $this->status->getEnable();
            $Partner->save();
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    /**
     * @param string $domain
     * @return void
     */
    private function setByDomain($domain){
        $this->partner = Partner::where("domain", $domain)->first();
    }

    /**
     * @param array $partner
     * @param int $id_address
     * @return bool
     */
    public function createPartner(array $partner, int $id_address){
        try {
            $this->validateDomain($partner["domain"]);
            $Partner = new Partner();
            $Partner->name = $partner["name"];
            $Partner->domain = $partner["domain"];
            $Partner->email = $partner["email"];
            $Partner->token = $this->getToken($partner["domain"]);
            $Partner->nit = $partner["nit"];
            $Partner->razon_social = $partner["razon_social"];
            $Partner->status = $this->status->getDisable();
            $Partner->legal_representative = $partner["legal_representative"];
            $Partner->picture_profile = null;
            $Partner->picture_front = null;
            $Partner->id_address = $id_address;
            $Partner->created_at = $this->date->getFullDate();
            $Partner->updated_at = null;
            $Partner->save();
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    /**
     * @param string $domain
     * @return string
     */
    private function getToken(string $domain){
        return Hash::make($domain, [
            'rounds' => 12,
        ]);
    }
}

?>
<?php

namespace App\Classes\Address;

use Illuminate\Support\Facades\Log;
use App\Models\Address;
use App\Models\AddressExtra;
use App\Models\Localization;
use App\Classes\Helper\Date;
use App\Classes\Helper\Status;

class AddressApi{

    /**
     * @var Address
     */
    protected $address;
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
     * @param array $address
     * @param array $geo
     * @return void
     */
    public function create(array $address, array $geo){
        $this->createAddressExtra($address["address_extra"]);
        $ExtraAddress = $this->getAddressExtra($address["address_extra"]);
        $this->createGeo($geo);
        $GEO = $this->getLocalization($geo);
        $this->createAddress($address, $ExtraAddress, $GEO);
        $this->setAddress($address, $ExtraAddress, $GEO);
    }

    /**
     * @return int
     */
    public function getAddressId(){
        return $this->address->id;
    }

    /**
     * @param array $address
     * @param int $id_address_extra
     * @param int $id_localization
     * @return void
     */
    private function setAddress(array $address, int $id_address_extra, int $id_localization){
        $this->address = Address::where('id_municipality', $address["id_municipality"])->
        where('id_country', $address["id_country"])->where('id_city', $address["id_city"])->
        where('id_address_extra', $id_address_extra)->where('id_localization', $id_localization)->first();
    }

    /**
     * @param array $GEO
     * @return int
     */
    private function getLocalization(array $GEO){
        $GEO = Localization::select('id')->where('latitud', $GEO["latitude"])->where('longitud', $GEO["longitude"])->get()->toArray();
        if (count($GEO) > 0) {
            return $GEO[0]["id"];
        }else{
            return 0;
        }
    }

    /**
     * @param array $address_extra
     * @return int
     */
    private function getAddressExtra(array $address_extra){
        $ExtraAddress = AddressExtra::select('id')->where('address', $address_extra["address"])->where('extra', $address_extra["extra"])->get()->toArray();
        if (count($ExtraAddress) > 0) {
            return $ExtraAddress[0]["id"];
        }else{
            return 0;
        }
    }

    /**
     * @param array $address
     * @param int $id_address_extra
     * @param int $id_localization
     * @return bool
     */
    private function createAddress(array $address, int $id_address_extra, int $id_localization){
        try {
            $Address = new Address();
            $Address->id_municipality = $address["id_municipality"];
            $Address->id_country = $address["id_country"];
            $Address->id_city = $address["id_city"];
            $Address->id_address_extra = $id_address_extra;
            $Address->id_localization = $id_localization;
            $Address->created_at = $this->date->getFullDate();
            $Address->updated_at = null;
            $Address->save();
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    /**
     * @param array $addressExtra
     * @return bool
     */
    private function createAddressExtra(array $addresExtra){
        try {
            $AddressExtra = new AddressExtra();
            $AddressExtra->address = $addresExtra["address"];
            $AddressExtra->extra = $addresExtra["extra"];
            $AddressExtra->save();
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }
    
    /**
     * @param array $geo
     * @return bool
     */
    private function createGeo(array $geo){
        try {
            $Localization = new Localization();
            $Localization->latitud = $geo["latitude"];
            $Localization->longitud = $geo["longitude"];
            $Localization->save();
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }
}

?>
<?php

namespace App\Classes\Address;

use Illuminate\Support\Facades\Log;
use App\Models\Address;
use App\Models\AddressExtra;
use App\Models\Localization;
use App\Classes\Helper\Date;
use App\Classes\Helper\Status;
use App\Classes\Helper\Text;
use Throwable;

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
     * @param array $address
     * @param array $geo
     * @return void
     */
    public function create(array $address, array $geo){
        $this->createAddressExtra($address[$this->text->getAddressExtra()]);
        $ExtraAddress = $this->getAddressExtra($address[$this->text->getAddressExtra()]);
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
        $this->address = Address::where($this->text->getIdMunicipality(), $address[$this->text->getIdMunicipality()])->
        where($this->text->getIdCountry(), $address[$this->text->getIdCountry()])->
        where($this->text->getIdCity(), $address[$this->text->getIdCity()])->
        where($this->text->getIdAddressExtra(), $id_address_extra)->where($this->text->getIdLocalization(), $id_localization)->first();
    }

    /**
     * @param array $GEO
     * @return int
     */
    private function getLocalization(array $GEO){
        $GEO = Localization::select($this->text->getId())->where($this->text->getLatitud(), $GEO[$this->text->getLatitude()])->
        where($this->text->getLongitud(), $GEO[$this->text->getLongitude()])->get()->toArray();
        if (count($GEO) > 0) {
            return $GEO[0][$this->text->getId()];
        }else{
            return 0;
        }
    }

    /**
     * @param array $address_extra
     * @return int
     */
    private function getAddressExtra(array $address_extra){
        $ExtraAddress = AddressExtra::select($this->text->getId())->
        where($this->text->getAddress(), $address_extra[$this->text->getAddress()])->
        where($this->text->getExtra(), $address_extra[$this->text->getExtra()])->get()->toArray();
        if (count($ExtraAddress) > 0) {
            return $ExtraAddress[0][$this->text->getId()];
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
            $Address->id_municipality = $address[$this->text->getIdMunicipality()];
            $Address->id_country = $address[$this->text->getIdCountry()];
            $Address->id_city = $address[$this->text->getIdCity()];
            $Address->id_address_extra = $id_address_extra;
            $Address->id_localization = $id_localization;
            $Address->created_at = $this->date->getFullDate();
            $Address->updated_at = null;
            $Address->save();
            return true;
        } catch (Throwable $th) {
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
            $AddressExtra->address = $addresExtra[$this->text->getAddress()];
            $AddressExtra->extra = $addresExtra[$this->text->getExtra()];
            $AddressExtra->save();
            return true;
        } catch (Throwable $th) {
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
            $Localization->latitud = $geo[$this->text->getLatitude()];
            $Localization->longitud = $geo[$this->text->getLongitude()];
            $Localization->save();
            return true;
        } catch (Throwable $th) {
            return false;
        }
    }
}

?>
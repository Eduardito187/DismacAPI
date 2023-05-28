<?php

namespace App\Classes\Address;

use Illuminate\Support\Facades\Log;
use App\Models\Address;
use App\Models\AddressExtra;
use App\Models\Localization;
use App\Classes\Helper\Date;
use App\Classes\Helper\Status;
use App\Classes\Helper\Text;
use App\Models\City;
use App\Models\Country;
use App\Models\Municipality;
use Exception;

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
     * @return Address
     */
    public function getAddress(array $address, int $id_address_extra, int $id_localization){
        return Address::where($this->text->getIdMunicipality(), $address[$this->text->getIdMunicipality()])->
        where($this->text->getIdCountry(), $address[$this->text->getIdCountry()])->
        where($this->text->getIdCity(), $address[$this->text->getIdCity()])->
        where($this->text->getIdAddressExtra(), $id_address_extra)->where($this->text->getIdLocalization(), $id_localization)->first();
    }

    /**
     * @param string $name
     * @return Country
     */
    public function getCountryByName(string $name){
        $Country = Country::where($this->text->getName(), $name)->first();
        if (!$Country) {
            throw new Exception($this->text->getCountryNone());
        }
        return $Country;
    }

    /**
     * @param string $name
     * @return City
     */
    public function getCityByName(string $name){
        $City = City::where($this->text->getName(), $name)->first();
        if (!$City) {
            throw new Exception($this->text->getCityNone());
        }
        return $City;
    }

    /**
     * @param string $name
     * @return Municipality
     */
    public function getMunicipalityByName(string $name){
        $Municipality = Municipality::where($this->text->getName(), $name)->first();
        if (!$Municipality) {
            throw new Exception($this->text->getMunicipalityNone());
        }
        return $Municipality;
    }

    /**
     * @param array $GEO
     * @return int
     */
    public function getLocalization(array $GEO){
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
    public function getAddressExtra(array $address_extra){
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
    public function createAddress(array $address, int $id_address_extra, int $id_localization){
        try {
            $addressM = $this->getAddress($address, $id_address_extra, $id_localization);
            if (!$addressM) {
                $Address = new Address();
                $Address->id_municipality = $address[$this->text->getIdMunicipality()];
                Log::debug("@1");
                $Address->id_country = $address[$this->text->getIdCountry()];
                Log::debug("@2");
                $Address->id_city = $address[$this->text->getIdCity()];
                Log::debug("@3");
                $Address->id_address_extra = $id_address_extra;
                Log::debug("@4");
                $Address->id_localization = $id_localization;
                Log::debug("@5");
                $Address->created_at = $this->date->getFullDate();
                Log::debug("@6"); 
                $Address->updated_at = null;
                Log::debug("@7");
                $Address->save();
                Log::debug("###FFF###");
                $this->address = $Address;
            }else{
                Log::debug("###---###");
                $this->address = $addressM;
            }
            Log::debug("######");
            return true;
        } catch (Exception $th) {
            return false;
        }
    }

    /**
     * @param array $addressExtra
     * @return bool
     */
    public function createAddressExtra(array $addresExtra){
        try {
            $AddressExtra = new AddressExtra();
            $AddressExtra->address = $addresExtra[$this->text->getAddress()];
            $AddressExtra->extra = $addresExtra[$this->text->getExtra()];
            $AddressExtra->save();
            return true;
        } catch (Exception $th) {
            return false;
        }
    }
    
    /**
     * @param array $geo
     * @return bool
     */
    public function createGeo(array $geo){
        try {
            $Localization = new Localization();
            $Localization->latitud = $geo[$this->text->getLatitude()];
            $Localization->longitud = $geo[$this->text->getLongitude()];
            $Localization->save();
            return true;
        } catch (Exception $th) {
            return false;
        }
    }
}

?>
<?php

namespace App\Classes\Import\Store;

use App\Classes\Helper\Date;
use App\Classes\Helper\Status;
use App\Classes\Helper\Text;
use App\Models\Store;

class Convert{
    CONST STORE_SCZ = "Tiendas SCZ";
    CONST STORE_CBA = "Tienda CBA";
    CONST STORE_LPZ = "Tiendas LPZ";
    CONST STORE_SCE = "Tiendas SCE";
    CONST STORE_TRJ = "Tiendas TRJ";
    CONST STORE_SCZ_ID = 2;
    CONST STORE_CBA_ID = 3;
    CONST STORE_LPZ_ID = 1;
    CONST STORE_SCE_ID = 8;
    CONST STORE_TRJ_ID = 5;
    CONST AUTH = "Wagento:wagento2021";
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
    /**
     * @var array
     */
    protected $stores;

    public function __construct() {
        $this->date     = new Date();
        $this->status   = new Status();
        $this->text     = new Text();
        $this->stores   = Store::all()->toArray();
    }

    /**
     * @param string $code
     * @return int|null
     */
    private function getStoreByCode(string $code){
        foreach ($this->stores as $store) {
            if ($store->code = $code) {
                return $store->id;
            }
        }
        return null;
    }

    /**
     * @param string $name
     * @return int|null
     */
    public function getStore(string $name){
        switch ($name) {
            case SELF::STORE_SCZ:
                return SELF::STORE_SCZ_ID;
                break;
            case SELF::STORE_CBA:
                return SELF::STORE_CBA_ID;
                break;
            case SELF::STORE_LPZ:
                return SELF::STORE_LPZ_ID;
                break;
            case SELF::STORE_SCE:
                return SELF::STORE_SCE_ID;
                break;
            case SELF::STORE_TRJ:
                return SELF::STORE_TRJ_ID;
                break;
        }
    }
}

?>
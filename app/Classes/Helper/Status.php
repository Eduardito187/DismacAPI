<?php

namespace App\Classes\Helper;

class Status{

    CONST VALUE_DISABLE = 0;
    CONST VALUE_ENABLE = 1;
    CONST BOOL_DISABLE = false;
    CONST BOOL_ENABLE = true;

    public function __construct() {
        //
    }

    /**
     * @param int $value
     * @return bool
     */
    public function convertBool($value){
        if ($value == self::VALUE_DISABLE) {
            return self::BOOL_DISABLE;
        }else{
            return self::BOOL_ENABLE;
        }
    }

    /**
     * @return bool
     */
    public function getEnable(){
        return self::BOOL_ENABLE;
    }

    /**
     * @return bool
     */
    public function getDisable(){
        return self::BOOL_DISABLE;
    }
}

?>
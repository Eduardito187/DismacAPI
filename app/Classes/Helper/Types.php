<?php

namespace App\Classes\Helper;

class Types{

    CONST INTEGER = "int";
    CONST STRING = "string";
    CONST BOOL = "bool";
    CONST LITROS = "lts";
    CONST DECIMAL = "decimal";
    CONST FLOAT = "float";
    CONST ARRAY = "array";
    CONST OBJECT = "object";
    CONST DATE = "date";
    CONST TIME = "time";
    CONST DATETIME = "datetime";
    CONST GIGABYTE = "gigabyte";

    public function __construct() {
        //
    }

    /**
     * @param string $type
     * @param string $value
     * @return true
     */
    public function validateType(string $type, string $value){
        switch($type){
            case self::INTEGER || self::LITROS || self::DECIMAL || self::FLOAT || self::GIGABYTE:
                return is_numeric($value);
                break;
            case self::STRING || self::DATE || self::TIME || self::DATETIME:
                echo $type."_".$value;
                return is_string($value);
                break;
            case self::BOOL:
                return is_bool($this->validateBool($value));
                break;
            case self::ARRAY:
                return is_array($value);
                break;
            case self::OBJECT:
                return is_object($value);
                break;
            default:
                return false;
                break;
        }
    }

    /**
     * @param string $value
     * @return bool
     */
    public function validateBool($value){
        if (is_numeric($value)){
            if (intval($value) == 1){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
}
?>
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
        if ($type == self::INTEGER || $type == self::LITROS || $type == self::DECIMAL || $type == self::FLOAT || $type == self::GIGABYTE){
            return is_numeric($value);
        }else if ($type == self::STRING || $type == self::DATE || $type == self::TIME || $type == self::DATETIME){
            return is_string($value);
        }else if ($type == self::BOOL){
            return is_bool($this->validateBool($value));
        }else if ($type == self::ARRAY){
            return is_array($value);
        }else if ($type == self::OBJECT){
            return is_object($value);
        }else{
            return false;
        }
    }

    /**
     * @param string $type
     * @param string $value
     * @return string|int|float|array|object
     */
    public function convertType(string|int|float|array|object $type, string $value){
        if ($type == self::INTEGER || $type == self::GIGABYTE){
            return intval($value);
        }else if ($type == self::LITROS || $type == self::DECIMAL || $type == self::FLOAT){
            return floatval($value);
        }else if ($type == self::STRING || $type == self::DATE || $type == self::TIME || $type == self::DATETIME){
            return strval($value);
        }else if ($type == self::BOOL){
            return $value == 1 ? true : false;
        }else if ($type == self::ARRAY){
            return json_encode($value, true);
        }else if ($type == self::OBJECT){
            return json_encode($value, true);
        }else{
            return strval($value);
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
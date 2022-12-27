<?php

namespace App\Classes\Helper;

class Date{

    public function __construct() {
        //
    }

    /**
     * @return int
     */
    public function getDay(){
        return date("d");
    }
    
    /**
     * @return int
     */
    public function getMonth(){
        return date("m");
    }
    
    /**
     * @return int
     */
    public function getYear(){
        return date("Y");
    }

    /**
     * @return string
     */
    public function getDate(){
        return date("Y-m-d");
    }

    /**
     * @return string
     */
    public function getFullDate(){
        return date("Y-m-d H:i:s");
    }

    /**
     * @return string
     */
    public function getTime(){
        return date("H:i");
    }

    /**
     * @return string
     */
    public function getFullTime(){
        return date("H:i:s");
    }
}

?>
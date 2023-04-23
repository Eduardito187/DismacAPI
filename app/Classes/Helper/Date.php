<?php

namespace App\Classes\Helper;

use Carbon\Carbon;
use App\Classes\Helper\Text;

class Date{
    /**
     * @var Text
     */
    protected $text;

    public function __construct() {
        $this->text = new Text();
    }
    
    /**
     * @param string $date
     * @param string|null $date_
     * @return string|null
     */
    public function getDiferenceInDates(string $date, string|null $date_){
        if (is_null($date_) || strlen($date_) == 0) {
            return null;
        }
        
        $Year = $this->getDiferenceYear($date, $date_);
        $Month = $this->getDiferenceMonth($date, $date_);
        $Days = $this->getDiferenceDays($date, $date_);
        if ($Year > 0) {
            return $this->text->getDiferenceYear($Year);
        }else if ($Month > 0){
            return $this->text->getDiferenceMonth($Month);
        }else {
            if ($Days > 0) {
                return $this->text->getDiferenceDays($Days);
            }else{
                return "Modificado recientemente.";
            }
        }
    }

    /**
     * @param string $date
     * @param string $date_
     * @return string
     */
    public function getDiferenceYear(string $date, string $date_){
        $toDate = Carbon::parse($date);
        $fromDate = Carbon::parse($date_);
        return $toDate->diffInYears($fromDate);  
    }

    /**
     * @param string $date
     * @param string $date_
     * @return string
     */
    public function getDiferenceMonth(string $date, string $date_){
        $toDate = Carbon::parse($date);
        $fromDate = Carbon::parse($date_);
        return $toDate->diffInMonths($fromDate);  
    }

    /**
     * @param string $date
     * @param string $date_
     * @return string
     */
    public function getDiferenceDays(string $date, string $date_){
        $toDate = Carbon::parse($date);
        $fromDate = Carbon::parse($date_);
        return $toDate->diffInDays($fromDate);  
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
     * @param string $date_time
     * @param string $date
     * @return string
     */
    public function addDateToDate(string $date_time, string $date){
        return date('Y-m-d H:i:s', strtotime($date_time.$date));
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
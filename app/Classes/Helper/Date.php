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
     * @param bool|null $status
     * @return string|null
     */
    public function getDiferenceInDates(string $date, string|null $date_, bool|null $status){
        if (is_null($date_) || strlen($date_) == 0) {
            return null;
        }

        $Year = $this->getDiferenceYear($date, $date_);
        $Month = $this->getDiferenceMonth($date, $date_);
        $Days = $this->getDiferenceDays($date, $date_);
        if ($status === true){
            return $this->getDiferenceCreated($Year, $Month, $Days);
        }else if($status === false) {
            return $this->getDiferenceUpdated($Year, $Month, $Days);
        }else{
            $Hours = $this->getDiferenceHours($date, $date_);
            $Minutes = $this->getDiferenceMinutes($date, $date_);
            return $this->getDiferenceUpdatedInit($Year, $Month, $Days, $Hours, $Minutes);
        }
    }

    /**
     * @param int $Year
     * @param int $Month
     * @param int $Days
     * @return string
     */
    public function getDiferenceCreated(string $Year, string $Month, string $Days){
        if ($Year > 0) {
            return $this->text->getDiferenceYear("Creado", $Year);
        }else if ($Month > 0){
            return $this->text->getDiferenceMonth("Creado", $Month);
        }else {
            if ($Days > 0) {
                return $this->text->getDiferenceDays("Creado", $Days);
            }else{
                return "Creado recientemente.";
            }
        }
    }

    /**
     * @param int $Year
     * @param int $Month
     * @param int $Days
     * @return string
     */
    public function getDiferenceUpdated(string $Year, string $Month, string $Days){
        if ($Year > 0) {
            return $this->text->getDiferenceYear("Modificado", $Year);
        }else if ($Month > 0){
            return $this->text->getDiferenceMonth("Modificado", $Month);
        }else {
            if ($Days > 0) {
                return $this->text->getDiferenceDays("Modificado", $Days);
            }else{
                return "Modificado recientemente.";
            }
        }
    }

    /**
     * @param int $Year
     * @param int $Month
     * @param int $Days
     * @param int $Hours
     * @param int $Minutes
     * @return string
     */
    public function getDiferenceUpdatedInit(int $Year, int $Month, int $Days, int $Hours, int $Minutes){
        if ($Year > 0) {
            return $this->text->concatTwoString($Year, "Y");
        }else if ($Month > 0){
            return $this->text->concatTwoString($Month, "M");
        }else {
            if ($Days > 0) {
                return $this->text->concatTwoString($Days, "D");
            }else{
                if ($Hours > 0) {
                    return $this->text->concatTwoString($Hours, "H");
                }else{
                    return $this->text->concatTwoString($Minutes, "min");
                }
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
     * @param string $date
     * @param string $date_
     * @return string
     */
    public function getDiferenceHours(string $date, string $date_){
        $toDate = Carbon::parse($date);
        $fromDate = Carbon::parse($date_);
        return $toDate->diffInHours($fromDate);  
    }

    /**
     * @param string $date
     * @param string $date_
     * @return string
     */
    public function getDiferenceMinutes(string $date, string $date_){
        $toDate = Carbon::parse($date);
        $fromDate = Carbon::parse($date_);
        return $toDate->diffInMinutes($fromDate);  
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
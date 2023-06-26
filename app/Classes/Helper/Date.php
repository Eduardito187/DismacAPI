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
        date_default_timezone_set($this->text->getTimeZone());
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
            return $this->text->getDiferenceYear($this->text->getCreado(), $Year);
        }else if ($Month > 0){
            return $this->text->getDiferenceMonth($this->text->getCreado(), $Month);
        }else {
            if ($Days > 0) {
                return $this->text->getDiferenceDays($this->text->getCreado(), $Days);
            }else{
                return $this->text->getCreadoNow();
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
            return $this->text->getDiferenceYear($this->text->getModificado(), $Year);
        }else if ($Month > 0){
            return $this->text->getDiferenceMonth($this->text->getModificado(), $Month);
        }else {
            if ($Days > 0) {
                return $this->text->getDiferenceDays($this->text->getModificado(), $Days);
            }else{
                return $this->text->getModificadoNow();
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
            return $this->text->concatTwoString($Year, $this->text->getYearPhp());
        }else if ($Month > 0){
            return $this->text->concatTwoString($Month, $this->text->getMonthPhp());
        }else {
            if ($Days > 0) {
                return $this->text->concatTwoString($Days, $this->text->getDayPhp());
            }else{
                if ($Hours > 0) {
                    return $this->text->concatTwoString($Hours, $this->text->getHoursPhp());
                }else{
                    return $this->text->concatTwoString($Minutes, $this->text->getMinutesPhp());
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
        return date($this->text->getDayPhp());
    }
    
    /**
     * @return int
     */
    public function getMonth(){
        return date($this->text->getMonthPhp());
    }
    
    /**
     * @return int
     */
    public function getYear(){
        return date($this->text->getYearPhp());
    }

    /**
     * @return string
     */
    public function getDate(){
        return date($this->text->getDatePhp());
    }

    /**
     * @return string
     */
    public function getFullDate(){
        return date($this->text->getZoneFull());
    }

    /**
     * @param string $date_time
     * @param string $date
     * @return string
     */
    public function addDateToDate(string $date_time, string $date){
        return date($this->text->getZoneFull(), strtotime($date_time.$date));
    }

    /**
     * @return string
     */
    public function getTime(){
        return date($this->text->getTimePhp());
    }

    /**
     * @return string
     */
    public function getFullTime(){
        return date($this->text->getDateTimePhp());
    }
}

?>
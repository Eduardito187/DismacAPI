<?php

namespace App\Classes\Tools;

use App\Classes\Helper\Text;
use App\Models\RestrictIp;
use Exception;
use \Illuminate\Http\Request;
use App\Classes\Helper\Date;

class IpLocker{
    /**
     * @var Text
     */
    protected $text;
    /**
     * @var Date
     */
    protected $date;

    public function __construct() {
        $this->text = new Text();
        $this->date = new Date();
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function lockerIp(Request $request){
        $params = $request->all();
        if (array_key_exists($this->text->getIp(), $params)){
            return $this->createIpLocker($params[$this->text->getIp()]);
        }else{
            throw new Exception($this->text->getParametersNone());
        }
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function unlockerIp(Request $request){
        $params = $request->all();
        if (array_key_exists($this->text->getIp(), $params)){
            return $this->deleteIpLocker($params[$this->text->getIp()]);
        }else{
            throw new Exception($this->text->getParametersNone());
        }
    }

    /**
     * @param string $ip
     * @return int|null
     */
    public function createIpLocker(string $ip){
        try {
            $RestrictIp = new RestrictIp();
            $RestrictIp->ip = $ip;
            $RestrictIp->created_at = $this->date->getFullDate();
            $RestrictIp->updated_at = null;
            return $RestrictIp->save();
        } catch (Exception $th) {
            throw new Exception($this->text->getNoBlockIp());
        }
    }

    /**
     * @param string $ip
     * @return bool
     */
    public function deleteIpLocker(string $ip){
        return RestrictIp::where($this->text->getIp(), $ip)->delete();
    }
}
?>
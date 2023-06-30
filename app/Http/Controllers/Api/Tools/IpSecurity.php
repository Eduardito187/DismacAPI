<?php

namespace App\Http\Controllers\Api\Tools;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Classes\Tools\IpLocker;
use App\Classes\Helper\Text;
use \Exception;

class IpSecurity extends Controller
{
    /**
     * @var Text
     */
    protected $text;
    /**
     * @var IpLocker
     */
    protected $IpLocker;

    public function __construct() {
        $this->text = new Text();
        $this->IpLocker = new IpLocker();
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function lockIp(Request $request)
    {
        try {
            $response = $this->text->getResponseApi(
                $this->IpLocker->lockerIp($request),
                $this->text->getDataProcessSuccess()
            );
        } catch (Exception $th) {
            $response = $this->text->getResponseApi(false, $th->getMessage());
        }
        return response()->json($response);
    }
    
    /**
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function unlockIp(Request $request)
    {
        try {
            $response = $this->text->getResponseApi(
                $this->IpLocker->unlockerIp($request),
                $this->text->getDataProcessSuccess()
            );
        } catch (Exception $th) {
            $response = $this->text->getResponseApi(false, $th->getMessage());
        }
        return response()->json($response);
    }
}
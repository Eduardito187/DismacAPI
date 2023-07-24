<?php

namespace App\Http\Controllers\Api\Tools;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Classes\Tools\PlatformApi;
use App\Classes\Helper\Text;
use \Exception;

class System extends Controller
{
    /**
     * @var Text
     */
    protected $text;
    /**
     * @var PlatformApi
     */
    protected $PlatformApi;

    public function __construct() {
        $this->text = new Text();
        $this->PlatformApi = new PlatformApi();
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $response = $this->text->getResponseApi(
                $this->PlatformApi->storeProcess($request),
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
    public function delimitation(Request $request)
    {
        try {
            $response = $this->text->getResponseApi(
                $this->PlatformApi->delimitationProcess($request),
                $this->text->getDataProcessSuccess()
            );
        } catch (Exception $th) {
            $response = $this->text->getResponseApi(false, $th->getMessage());
        }
        return response()->json($response);
    }
}
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
    public function warehouse(Request $request)
    {
        try {
            $response = $this->text->getResponseApi(
                $this->PlatformApi->warehouseProcess($request),
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
    public function municipality(Request $request)
    {
        try {
            $response = $this->text->getResponseApi(
                $this->PlatformApi->municipalityProcess($request),
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
    
    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function storeEnable(Request $request, $id)
    {
        try {
            $response = $this->text->getResponseApi(
                $this->PlatformApi->enableStore($id),
                $this->text->getDataProcessSuccess()
            );
        } catch (Exception $th) {
            $response = $this->text->getResponseApi(false, $th->getMessage());
        }
        return response()->json($response);
    }
    
    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function storeDisable(Request $request, $id)
    {
        try {
            $response = $this->text->getResponseApi(
                $this->PlatformApi->disableStore($id),
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
    public function modifyPermissions(Request $request)
    {
        try {
            $response = $this->text->getResponseApi(
                $this->PlatformApi->modifyPermissions($request),
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
    public function addPermission(Request $request)
    {
        try {
            $response = $this->text->getResponseApi(
                $this->PlatformApi->addPermission($request),
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
    public function removePermission(Request $request)
    {
        try {
            $response = $this->text->getResponseApi(
                $this->PlatformApi->removePermission($request),
                $this->text->getDataProcessSuccess()
            );
        } catch (Exception $th) {
            $response = $this->text->getResponseApi(false, $th->getMessage());
        }
        return response()->json($response);
    }
}
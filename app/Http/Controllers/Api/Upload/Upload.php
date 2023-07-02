<?php

namespace App\Http\Controllers\Api\Upload;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Classes\Import\Import;
use App\Classes\Helper\Text;
use \Exception;
use App\Classes\Partner\PartnerApi;

class Upload extends Controller
{
    /**
     * @var Import
     */
    protected $Import;
    /**
     * @var Text
     */
    protected $text;
    /**
     * @var PartnerApi
     */
    protected $PartnerApi;

    public function __construct() {
        $this->Import     = new Import();
        $this->text       = new Text();
        $this->PartnerApi = new PartnerApi();
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function deletePicture(Request $request)
    {
        try {
            $response = $this->text->getResponseApi(
                $this->PartnerApi->deletePicture($request),
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
    public function actionFile(Request $request)
    {
        try {
            $params = $request->all();
            $response = $this->text->getResponseApi(
                $params[$this->text->getType()] == $this->text->getFotos() ? $this->PartnerApi->uploadZipPicture($request) :$this->Import->setActionProgram($request),
                $this->text->getQuerySuccess()
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
    public function uploadPictures(Request $request)
    {
        try {
            $response = $this->text->getResponseApi(
                $this->PartnerApi->uploadPictures($request),
                $this->text->getQuerySuccess()
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
    public function uploadZipImages(Request $request)
    {
        try {
            $response = $this->text->getResponseApi(
                $this->PartnerApi->uploadZipPicture($request),
                $this->text->getQuerySuccess()
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
    public function changeProfile(Request $request)
    {
        try {
            $response = $this->text->getResponseApi(
                $this->PartnerApi->uploadPicture($request),
                $this->text->getQuerySuccess()
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
    public function changeCover(Request $request)
    {
        try {
            $response = $this->text->getResponseApi(
                $this->PartnerApi->uploadCover($request),
                $this->text->getQuerySuccess()
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
    public function process(Request $request)
    {
        try {
            $response = $this->text->getResponseApi(
                $this->Import->runProcess($request->all()),
                $this->text->getDataProcessSuccess()
            );
        } catch (Exception $th) {
            $response = $this->text->getResponseApi(false, $th->getMessage());
        }
        return response()->json($response);
    }
    
    /**
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $response = $this->text->getResponseApi(
                $this->Import->getAllProcessPending(),
                $this->text->getQuerySuccess()
            );
        } catch (Exception $th) {
            $response = $this->text->getResponseApi(false, $th->getMessage());
        }
        return response()->json($response);
    }
}
<?php

namespace App\Http\Controllers\Api\Upload;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Classes\Import\Import;
use App\Classes\Helper\Text;
use \Exception;

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

    public function __construct() {
        $this->Import     = new Import();
        $this->text       = new Text();
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function actionFile(Request $request)
    {
        try {
            $response = $this->text->getResponseApi(
                $this->Import->setActionProgram($request),
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
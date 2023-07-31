<?php

namespace App\Http\Controllers\Api\Account\Session;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Classes\Account\AccountApi;
use App\Classes\Helper\Text;
use App\Classes\Helper\Status;
use \Illuminate\Support\Facades\Log;
use \Illuminate\Http\Response;
use \Exception;

class Account extends Controller
{
    protected $accountApi;
    protected $text;
    protected $status;

    public function __construct() {
        $this->accountApi = new AccountApi();
        $this->text       = new Text();
        $this->status     = new Status();
    }

    /**
     * Display a listing of the resource.
     *
     * @param  Request  $request
     * @return Response
     */
    public function index(Request $request)
    {
        try {
            $response = $this->text->getResponseApi(
                $this->accountApi->currentAccountArray($request->header($this->text->getAuthorization())),
                $this->text->getAccountExist()
            );
        } catch (Exception $th) {
            $response = $this->text->getResponseApi(null, $th->getMessage());
        }
        return response()->json($response);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  Request  $request
     * @return Response
     */
    public function allrol(Request $request){
        try {
            $response = $this->text->getResponseApi(
                $this->accountApi->getAllRols(),
                $this->text->getQuerySuccess()
            );
        } catch (Exception $th) {
            $response = $this->text->getResponseApi(null, $th->getMessage());
        }
        return response()->json($response);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  Request  $request
     * @return Response
     */
    public function rolsAccount(Request $request)
    {
        try {
            $response = $this->text->getResponseApi(
                $this->accountApi->getRolAccount($request->header($this->text->getAuthorization())),
                $this->text->getAccountExist()
            );
        } catch (Exception $th) {
            $response = $this->text->getResponseApi(null, $th->getMessage());
        }
        return response()->json($response);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function improvements(Request $request)
    {
        try {
            $response = $this->text->getResponseApi(
                $this->accountApi->createImprovement($request->header($this->text->getAuthorization()), $request->all()),
                $this->text->getQuerySuccess()
            );
        } catch (Exception $th) {
            $response = $this->text->getResponseApi(null, $th->getMessage());
        }
        return response()->json($response);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function getImprovementsActive(Request $request)
    {
        try {
            $response = $this->text->getResponseApi(
                $this->accountApi->getImprovementsApi($request->header($this->text->getAuthorization()), $this->status->getEnable()),
                $this->text->getQuerySuccess()
            );
        } catch (Exception $th) {
            $response = $this->text->getResponseApi(null, $th->getMessage());
        }
        return response()->json($response);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function getImprovementsInactive(Request $request)
    {
        try {
            $response = $this->text->getResponseApi(
                $this->accountApi->getImprovementsApi($request->header($this->text->getAuthorization()), $this->status->getDisable()),
                $this->text->getQuerySuccess()
            );
        } catch (Exception $th) {
            $response = $this->text->getResponseApi(null, $th->getMessage());
        }
        return response()->json($response);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function getTicketsAccount(Request $request)
    {
        try {
            $response = $this->text->getResponseApi(
                $this->accountApi->getTicketsAccount($request->header($this->text->getAuthorization())),
                $this->text->getQuerySuccess()
            );
        } catch (Exception $th) {
            $response = $this->text->getResponseApi(null, $th->getMessage());
        }
        return response()->json($response);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function getTicketsPartner(Request $request)
    {
        try {
            $response = $this->text->getResponseApi(
                $this->accountApi->getTicketsPartner($request->header($this->text->getAuthorization())),
                $this->text->getQuerySuccess()
            );
        } catch (Exception $th) {
            $response = $this->text->getResponseApi(null, $th->getMessage());
        }
        return response()->json($response);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function support(Request $request)
    {
        try {
            $response = $this->text->getResponseApi(
                $this->accountApi->createSupport($request->header($this->text->getAuthorization()), $request->all()),
                $this->text->getQuerySuccess()
            );
        } catch (Exception $th) {
            $response = $this->text->getResponseApi(null, $th->getMessage());
        }
        return response()->json($response);
    }
}
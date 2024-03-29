<?php

namespace App\Http\Controllers\Api\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Classes\Account\AccountApi;
use App\Classes\Helper\Text;
use App\Classes\Helper\Status;
use Exception;
use Illuminate\Support\Facades\Log;
use App\Classes\Partner\Inventory\Catalog as CatalogApi;

class Catalog extends Controller
{
    protected $addressApi;
    protected $accountApi;
    protected $partnerApi;
    protected $text;
    protected $status;
    protected $catalogApi;

    public function __construct() {
        $this->accountApi = new AccountApi();
        $this->text       = new Text();
        $this->status     = new Status();
        $this->catalogApi = new CatalogApi();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $response = array();
        try {
            if (!is_null($request->all()[$this->text->getName()]) && !is_null($request->all()[$this->text->getCode()])) {
                $this->catalogApi->newCatalog(
                    $request->all()[$this->text->getName()],
                    $request->all()[$this->text->getCode()],
                    $this->accountApi->getAccountToken($request->header($this->text->getAuthorization()))
                );
                $response = $this->text->getResponseApi($this->status->getEnable(), $this->text->getAddSuccess());
            }else{
                throw new Exception($this->text->getErrorParametros());
            }
        } catch (Exception $th) {
            $response = $this->text->getResponseApi($this->status->getDisable(), $th->getMessage());
        }
        return response()->json($response);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $response = array();
        try {
            $response = $this->text->getResponseApi($this->catalogApi->getCatalog($id), $this->text->getQuerySuccess());
        } catch (Exception $th) {
            $response = $this->text->getResponseApi($this->status->getDisable(), $th->getMessage());
        }
        return response()->json($response);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function info($id)
    {
        $response = array();
        try {
            $response = $this->text->getResponseApi($this->catalogApi->getCatalogBasicInfo($id), $this->text->getQuerySuccess());
        } catch (Exception $th) {
            $response = $this->text->getResponseApi($this->status->getDisable(), $th->getMessage());
        }
        return response()->json($response);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $response = array();
        try {
            if (!is_null($request->all()[$this->text->getName()]) && !is_null($request->all()[$this->text->getCode()])) {
                $Account = $this->accountApi->getAccountByToken($request->header($this->text->getAuthorization()));
                $this->catalogApi->updateCatalog(
                    $id,
                    $request->all()[$this->text->getName()],
                    $request->all()[$this->text->getCode()],
                    $Account->accountPartner->Partner
                );
                $response = $this->text->getResponseApi($this->status->getEnable(), $this->text->getUpdateSuccess());
            }else{
                throw new Exception($this->text->getErrorParametros());
            }
        } catch (Exception $th) {
            $response = $this->text->getResponseApi($this->status->getDisable(), $th->getMessage());
        }
        return response()->json($response);
    }
}

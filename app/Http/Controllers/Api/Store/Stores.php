<?php

namespace App\Http\Controllers\Api\Store;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Store;
use App\Classes\Helper\Text;
use App\Classes\Account\AccountApi;
use App\Classes\Partner\PartnerApi;
use App\Classes\Helper\Status;
use Exception;

class Stores extends Controller
{
    /**
     * @var Text
     */
    protected $text;
    /**
     * @var AccountApi
     */
    protected $accountApi;
    /**
     * @var PartnerApi
     */
    protected $partnerApi;
    /**
     * @var Status
     */
    protected $status;

    public function __construct() {
        $this->text = new Text();
        $this->accountApi = new AccountApi();
        $this->partnerApi = new PartnerApi();
        $this->status = new Status();
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $Store = Store::select($this->text->getId(),$this->text->getName(),$this->text->getCode())->get()->toArray();
        return response()->json($Store);
    }

    /**
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function getStores(Request $request)
    {
        $response = array();
        try {
            $Account = $this->accountApi->getAccountByPublic($request->header($this->text->getAuthorization()));
            $response = $this->text->getResponseApi(
                $this->partnerApi->getStorePartner($Account),
                $this->text->getQuerySuccess()
            );
        } catch (Exception $th) {
            $response = $this->text->getResponseApi(
                $this->status->getDisable(),
                $th->getMessage()
            );
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
        $Store = Store::select($this->text->getId(),$this->text->getName(),$this->text->getCode())->where($this->text->getId(),$id)->get()->toArray();
        return response()->json($Store);
    }
}

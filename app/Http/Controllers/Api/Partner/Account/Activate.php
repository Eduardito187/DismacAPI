<?php

namespace App\Http\Controllers\Api\Partner\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Classes\Account\AccountApi;
use App\Classes\Helper\Text;
use App\Classes\Helper\Status;
use Exception;

class Activate extends Controller
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $response = array();
        try {
            $Account = null;
            if ($request->all()[$this->text->getType()] == $this->text->getKey()) {
                $Account = $this->accountApi->getAccountKey($request->all()[$this->text->getValue()]);
            }else if ($request->all()[$this->text->getType()] == $this->text->getToken()) {
                $Account = $this->accountApi->getAccountToken($request->header($this->text->getAuthorization()));
            }else if ($request->all()[$this->text->getType()] == $this->text->getEmail()) {
                $Account = $this->accountApi->getAccountEmail($request->all()[$this->text->getValue()]);
            }
            if ($Account != null) {
                $this->accountApi->statusAccount($Account, true);
                $response = $this->text->getResponseApi($this->status->getEnable(), $this->text->getAccountEnable());
            }else{
                $response = $this->text->getResponseApi($this->status->getDisable(), $this->text->invalidFormatUser());
            }
        } catch (Exception $th) {
            $response = $this->text->getResponseApi($this->status->getDisable(), $th->getMessage());
        }
        return response()->json($response);
    }
}
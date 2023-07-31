<?php

namespace App\Http\Controllers\Api\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Classes\Account\AccountApi;
use App\Classes\Helper\Text;
use App\Classes\Helper\Status;
use \Illuminate\Support\Facades\Log;
use Exception;

class Search extends Controller
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
        $accounts = array();
        try {
            $accounts = $this->accountApi->searchAccount($request);
            $response = $this->text->getResponseApi($accounts, $this->text->getSuccessSearch());
        } catch (Exception $th) {
            $response = $this->text->getResponseApi($accounts, $th->getMessage());
        }
        return response()->json($response);
    }
}

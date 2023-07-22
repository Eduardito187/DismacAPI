<?php

namespace App\Http\Controllers\Api\Login;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Classes\Account\AccountApi;

class Login extends Controller
{
    protected $accountApi;

    public function __construct() {
        $this->accountApi = new AccountApi();
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        return response()->json($this->accountApi->validateLogin($request));
    }
}
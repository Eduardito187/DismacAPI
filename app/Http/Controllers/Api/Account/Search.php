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
    protected $request;

    public function __construct() {
        $this->accountApi = new AccountApi();
        $this->text       = new Text();
        $this->status     = new Status();
        $this->request    = new Request;
    }
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $accounts = array();
        try {
            Log::debug("Token ON => ".$this->request->header($this->text->getAuthorization()));
            $accounts = [];
            $response = $this->text->getResponseApi($accounts, $this->text->getSuccessSearch());
        } catch (Exception $th) {
            $response = $this->text->getResponseApi(null, $th->getMessage());
        }
        return response()->json($response);
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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json([]);
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
        return response()->json([]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return response()->json([]);
    }
}

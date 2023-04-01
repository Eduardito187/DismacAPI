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
                $this->accountApi->getCurrentAccount($request->header($this->text->getAuthorization())),
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
    public function store(Request $request)
    {
        return response()->json([]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        return response()->json([]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        return response()->json([]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        return response()->json([]);
    }
}
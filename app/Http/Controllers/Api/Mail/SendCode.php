<?php

namespace App\Http\Controllers\Api\Mail;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Classes\MailCode;
use App\Classes\Partner\PartnerApi;
use App\Classes\Account\AccountApi;

class SendCode extends Controller
{
    protected $accountApi;
    protected $partnerApi;

    public function __construct() {
        $this->accountApi = new AccountApi();
        $this->partnerApi = new PartnerApi();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json([]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $state = null;
        if (!is_null($request->all()["email"]) && !is_null($request->all()["code"]) && !is_null($request->all()["type"])) {
            $email = null;
            if ($request->all()["type"] == "partner") {
                if (!$this->partnerApi->validateEmail($request->all()["email"])) {
                    $email = true;
                }else{
                    $email = false;
                }
            }else if ($request->all()["type"] == "account") {
                if (!$this->accountApi->verifyEmail($request->all()["email"])) {
                    $email = true;
                }else{
                    $email = false;
                }
            }
            if ($email == false) {
                $newEmail = new MailCode($request->all()["email"], "Código de verificación", $request->all()["code"]);
                $state = $newEmail->createMail();
            }
        }else{
            $state = false;
        }
        $response = array("status" => $state);
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

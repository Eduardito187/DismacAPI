<?php

namespace App\Http\Controllers\Api\Mail;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Classes\MailCode;
use App\Classes\Partner\PartnerApi;
use App\Classes\Account\AccountApi;
use Exception;

class SendCode extends Controller
{
    protected $accountApi;
    protected $partnerApi;

    public function __construct() {
        $this->accountApi = new AccountApi();
        $this->partnerApi = new PartnerApi();
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
        try {
            if (!is_null($request->all()["email"]) && !is_null($request->all()["code"]) && !is_null($request->all()["type"])) {
                $email = null;
                if ($request->all()["type"] == "partner") {
                    $email = $this->partnerApi->validateEmail($request->all()["email"]);
                }else if ($request->all()["type"] == "account") {
                    $email = $this->accountApi->verifyEmail($request->all()["email"]);
                }
                if ($email == true) {
                    $newEmail = new MailCode($request->all()["email"], "Código de verificación", $request->all()["code"]);
                    $state = $newEmail->createMail();
                }else{
                    if ($request->all()["restore"] != "Si") {
                        $state = false;
                    }else{
                        $newEmail = new MailCode($request->all()["email"], "Código de restauración", $request->all()["code"]);
                        $state = $newEmail->createMail();
                    }
                }
            }else{
                $state = false;
            }
        } catch (Exception $th) {
            //throw $th;
            $state = null;
        }
        $response = array("status" => $state);
        return response()->json($response);
    }
}

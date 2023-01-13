<?php

namespace App\Http\Controllers\Api\Mail;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Classes\MailCode;
use App\Classes\Partner\PartnerApi;

class SendCode extends Controller
{
    protected $partnerApi;

    public function __construct() {
        $this->partnerApi = new PartnerApi();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        if (!is_null($request->all()["email"]) && !is_null($request->all()["code"])) {
            if ($this->partnerApi->validateEmail($request->all()["email"])) {
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}

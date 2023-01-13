<?php

namespace App\Http\Controllers\Api\Mail;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Mail\Code;
use Mail as Mailer;
use App\Classes\ListClass;
use Illuminate\Mail\Mailables\Content;

class SendCode extends Controller
{
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
        $response = array();
        if (!is_null($request->all()["email"]) && !is_null($request->all()["code"])) {
            //Mailer::to($request->all()["email"])->send(new Code($request->all()["code"]));
            
            $newEmail = new ListClass($request->all()["email"], "platformdismac@grazcompany.com", null, "Código de verificación", new Content(view: 'mail.account.validate',with: ['code' => $request->all()["code"]]));
            $newEmail->createMail();
            $response = array("status" => true);
        }else{
            $response = array("status" => false);
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

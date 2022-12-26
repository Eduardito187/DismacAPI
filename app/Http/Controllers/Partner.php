<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Partner as PartnerModel;
use App\Mail\RegisterAccount;
use Mail;

class Partner extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //find(1)->name
        //Mail::to("andyaguilera712@gmail.com")->send(new SendLogin());
        //Mail::to("eduardchavez302@gmail.com")->send(new RegisterAccount());
        ini_set( 'display_errors', 1 );
        error_reporting( E_ALL );
        $from = "test@hostinger-tutorials.com";
        $to = "eduardchavez302@gmail.com";
        $subject = "Checking PHP mail";
        $message = "PHP mail works just fine";
        $headers = "From:" . $from;
        mail($to,$subject,$message, $headers);
        echo "The email message was sent.";

        return response()->json(PartnerModel::all());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
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
        //
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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
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

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Partner as PartnerModel;
use App\Mail\RegisterAccount;
use Mail;
use App\Classes\ListClass;
use App\Http\Requests\Partner\CreateRequest;

class Partner extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //find(1)->name
        //Mail::to("andyaguilera712@gmail.com")->send(new SendLogin());
        //Mail::to("andyaguilera712@gmail.com")->send(new RegisterAccount());
        echo $request->header('Authorization');
        $newEmail = new ListClass("eduardchavez302@gmail.com", "platformdismac@grazcompany.com", "andyaguilera712@gmail.com", "Registro de cuenta", "<h1>HOLA</h1>");
        $newEmail->createMail();
        return response()->json(PartnerModel::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateRequest $request)
    {
        print_r($request);
        return response()->json(PartnerModel::all());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json(["action" => "show"]);
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
        return response()->json(["action" => "update"]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return response()->json(["action" => "destroy"]);
    }
}

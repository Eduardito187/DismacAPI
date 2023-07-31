<?php

namespace App\Http\Controllers\Api\Address;

use App\Http\Controllers\Controller;
use App\Models\Municipality as ModelsMunicipality;
use Illuminate\Http\Request;

class Municipality extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!is_null($request->all()["id_city"])) {
            return response()->json(ModelsMunicipality::where("id_city", $request->all()["id_city"])->get());
        }else{
            return [];
        }
    }
}
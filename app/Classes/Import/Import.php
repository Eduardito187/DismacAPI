<?php

namespace App\Classes\Import;

use App\Classes\Helper\Date;
use App\Classes\Helper\Status;
use App\Classes\Helper\Text;

class Import{
    CONST AUTH = "Wagento:wagento2021";
    /**
     * @var Date
     */
    protected $date;
    /**
     * @var Status
     */
    protected $status;
    /**
     * @var Text
     */
    protected $text;

    public function __construct() {
        $this->date     = new Date();
        $this->status   = new Status();
        $this->text     = new Text();
    }

    /**
     * @param array $request
     * @return array $response
     */
    public function importCategory(array $request){
        
        $url = 'https://posapi.dismac.com.bo/v2/Product/GetItems';
        $data = [
            "GrupoArticulo"  => $request["GrupoArticulo"],
            "Disponibilidad" => $request["Disponibilidad"],
            "Precios"        => $request["Precios"],
            "SubCategoria"   => $request["SubCategoria"]
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPGET, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json; charset=utf-8','Authorization: Basic '. base64_encode(SELF::AUTH)));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response, true);
    }
}

?>
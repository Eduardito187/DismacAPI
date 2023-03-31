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
            $this->text->getGrupoArticulo()  => $request[$this->text->getGrupoArticulo()],
            $this->text->getDisponibilidad() => $request[$this->text->getDisponibilidad()],
            $this->text->getPrecios()        => $request[$this->text->getPrecios()],
            $this->text->getSubCategoria()   => $request[$this->text->getSubCategoria()]
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPGET, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $this->text->getMethodGet());
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array($this->text->getPosParamOne(),$this->text->getPosAuth(). base64_encode(SELF::AUTH)));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response, true);
    }
}

?>
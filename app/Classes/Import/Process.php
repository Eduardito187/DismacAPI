<?php

namespace App\Classes\Import;

class Process{
    /**
     * @var array
     */
    protected $Headers = [];
    /**
     * @var array
     */
    protected $Types = array(
        
    );

    public function __construct() {
        //
    }

    public function setHeaders(string $code){
        $this->Headers[] = $code;
    }
}
?>
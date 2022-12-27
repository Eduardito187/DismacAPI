<?php

namespace App\Classes;

class ListClass{

    protected $to       = "";
    protected $from     = "";
    protected $title    = "";
    protected $message  = "";
    protected $headers  = "";

    public function __construct(string $to, string $from, array|string|null $cc, string $title, string $message) {
        $this->to = $to;
        $this->from = $from;
        $this->title = $title;
        $this->message = $message;
        $this->headers = "From:".$this->from."\r\n";
        $this->headers = "Reply-To:".$this->from."\r\n";
        if ($cc != null) {
            if (is_array($cc)) {
                $this->headers .= 'Cc:'.implode(', ', $cc)."\r\n";
            }else{
                $this->headers .= 'Cc: '.$cc."\r\n";
            }
        }
        $this->headers .= "MIME-Version: 1.0\r\n";
        $this->headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    }

    public function createMail() {
        try {
            ini_set( 'display_errors', 1 );
            error_reporting( E_ALL );
            mail($this->to,$this->title,$this->message, $this->headers);
        } catch (\Throwable $th) {
            //
        }
    }
}

?>
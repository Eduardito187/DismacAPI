<?php

namespace App\Classes;

use App\Classes\Helper\Text;

class ListClass{

    protected $to       = "";
    protected $from     = "";
    protected $title    = "";
    protected $message  = "";
    protected $headers  = "";
    /**
     * @var Text
     */
    protected $text;

    public function __construct(string $to, string $from, array|string|null $cc, string $title, string $message) {
        $this->text = new Text();
        $this->to = $to;
        $this->from = $from;
        $this->title = $title;
        $this->message = $message;
        $this->headers = $this->text->getMailFrom().$this->from.$this->text->getLine();
        $this->headers = $this->text->getMailReply().$this->from.$this->text->getLine();
        if ($cc != null) {
            if (is_array($cc)) {
                $this->headers .= $this->text->getMailCc().implode(', ', $cc).$this->text->getLine();
            }else{
                $this->headers .= $this->text->getMailCc().$cc.$this->text->getLine();
            }
        }
        $this->headers .= $this->text->getMailHeaders();
    }

    public function createMail() {
        try {
            ini_set($this->text->getDisplayError(), 1 );
            error_reporting( E_ALL );
            mail($this->to,$this->title,$this->message, $this->headers);
        } catch (\Throwable $th) {
            //
        }
    }
}

?>
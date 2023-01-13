<?php

namespace App\Classes;

use App\Classes\Helper\Text;

class MailCode{

    protected $to       = "";
    protected $from     = "";
    protected $title    = "";
    protected $message  = "";
    protected $headers  = [];
    /**
     * @var Text
     */
    protected $text;

    public function __construct(string $to, string $from, array|string|null $cc, string $title, string $message) {
        $this->text = new Text();
        $this->to = $to;
        $this->from = $from;
        $this->title = $title;
        $this->message = view('mail.account.validate', ['code' => $message]);
        /*
        if ($cc != null) {
            if (is_array($cc)) {
                $this->headers[] = "Cc: ".$this->text->getMailCc().implode(', ', $cc).$this->text->getLine();
            }else{
                $this->headers[] = "Cc: ".$this->text->getMailCc().$cc.$this->text->getLine();
            }
        }
        */
        $this->headers = [
            'MIME-Version' => 'MIME-Version: 1.0',
            'Content-type' => 'text/html; charset=UTF-8',
            'From' => "PlatformDismac <platformdismac@grazcompany.com>",
            'Reply-To' => "platformdismac@grazcompany.com",
            'X-Mailer' => 'PHP/' . phpversion()
        ];
    }

    public function createMail() {
        try {
            ini_set($this->text->getDisplayError(), 1 );
            error_reporting( E_ALL );
            mail($this->to, $this->title, (string)$this->message, $this->headers);
        } catch (\Throwable $th) {
            //
        }
    }
}

?>
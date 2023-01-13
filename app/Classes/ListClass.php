<?php

namespace App\Classes;

use App\Classes\Helper\Text;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Support\Facades\Log;

class ListClass{

    protected $to       = "";
    protected $from     = "";
    protected $title    = "";
    protected $message  = "";
    protected $headers  = [];
    /**
     * @var Text
     */
    protected $text;

    public function __construct(string $to, string $from, array|string|null $cc, string $title, Content $message) {
        $this->text = new Text();
        $this->to = $to;
        $this->from = $from;
        $this->title = $title;
        $this->message = $message;

        $this->headers[] = 'MIME-Version: 1.0';
        $this->headers[] = 'Content-type: text/html; charset=utf-8';
        $this->headers[] = 'To: User <eduardchavez302@gmail.com>';
        $this->headers[] = 'From: Birthday Reminder <supportclient@grazcompany.com>';
        if ($cc != null) {
            if (is_array($cc)) {
                $this->headers[] = "Cc: ".$this->text->getMailCc().implode(', ', $cc).$this->text->getLine();
            }else{
                $this->headers[] = "Cc: ".$this->text->getMailCc().$cc.$this->text->getLine();
            }
        }
        $this->headers[] = "Date: ".date("r (T)");
        $this->headers[] = "Sensitivity: Personal";
    }

    public function createMail() {
        try {
            ini_set($this->text->getDisplayError(), 1 );
            error_reporting( E_ALL );
            mail($this->to, $this->title, "OK", $this->headers);
            Log::debug("OK Message");
        } catch (\Throwable $th) {
            Log::debug("Message => ".$th->getMessage());
            //
        }
    }
}

?>
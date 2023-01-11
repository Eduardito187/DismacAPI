<?php

namespace App\Classes\Helper;

use App\Classes\Helper\Text;

class MailCode{
    private $para = "";
    private $de = "";
    private $body = "";
    private $title = "";
    private $code = "";
    protected $headers  = "";
    /**
     * @var Text
     */
    protected $text;
    public function __construct(string $para, string $title, string $code, string $de = "platformdismac@grazcompany.com") {
        $this->text = new Text();
        $this->para = $para;
        $this->de = $de;
        $this->title = $title;
        $this->code = $code;

        $this->headers = $this->text->getMailFrom().$this->de.$this->text->getLine();
        $this->headers = $this->text->getMailReply().$this->de.$this->text->getLine();
        $this->headers .= $this->text->getMailHeaders();
    }

    /**
     * @return string
     */
    private function getHeader(){
        return `
            <!DOCTYPE html>
            <html>
            <title`+$this->title+`</title>
            <head>
                <style>
                    @import url('https://fonts.googleapis.com/css?family=Muli&display=swap');
                    @import url('https://fonts.googleapis.com/css?family=Quicksand&display=swap');
                    body {
                        font-family: 'Muli', sans-serif;
                        color: rgba(0, 0, 0, 0.8);
                        font-weight: 400;
                        line-height: 1.58;
                        letter-spacing: -.003em;
                        font-size: 20px;
                        padding: 40px;
                    }
                </style>
            </head>
        `;
    }

    /**
     * @return string
     */
    private function getBody(){
        return `
        <body>
            <div style="width: 100%;background-color: white;">
                <img src="https://dismacapi.grazcompany.com/storage/dismac_clasic.png" style="width: 100px;" />
            </div>
            <div style="width: 100%;background-color: white;text-align: center;justify-content: center;">
                <p>Copie el codigo para poder acceder al siguiente paso en el registro de la cuenta.</p>
                <h2>`.$this->code.`</h2>
            </div>
            <div style="width: 100%;background-color: white;text-align: center;justify-content: center;">
                <a href="https://www.facebook.com/DismacBolivia/" style="display: inline-block;">
                    <img src="https://dismacapi.grazcompany.com/storage/fbnew.png" style="width: 40px;height: 40px;border-radius: 20px;"/>
                </a>
                <a href="https://www.instagram.com/dismacbolivia/" style="display: inline-block;">
                    <img src="https://dismacapi.grazcompany.com/storage/instanew.png" style="width: 40px;height: 40px;border-radius: 20px;"/>
                </a>
                <a href="https://www.youtube.com/channel/UCa_XVvWTrq7C0IIq_Oiswqw/videos" style="display: inline-block;">
                    <img src="https://dismacapi.grazcompany.com/storage/ytnew.png" style="width: 40px;height: 40px;border-radius: 20px;"/>
                </a>
            </div>
        </body>
        </html>
        `;
    }


    public function createMail() {
        try {
            ini_set($this->text->getDisplayError(), 1 );
            error_reporting( E_ALL );
            $this->body = $this->getHeader().$this->getBody();
            mail($this->para,$this->title,$this->body, $this->headers);
        } catch (\Throwable $th) {
            //
        }
    }
}
?>
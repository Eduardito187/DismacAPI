<?php

namespace App\Classes\Helper;

class Text{

    CONST ADD_SUCCESS    = "Registro exitoso.";
    CONST COLUMN_ADDRESS = "address";
    CONST COLUMN_PARTNER = "partner";
    CONST COLUMN_ACCOUNT = "account";
    CONST RESPONSE       = "response";
    CONST RESPONSE_TEXT  = "responseText";
    CONST MAIL_FROM      = "From:";
    CONST MAIL_REPLY     = "Reply-To:";
    CONST MAIL_HEADERS   = "MIME-Version: 1.0\r\nContent-Type: text/html; charset=UTF-8\r\n";
    CONST LINE_LINE      = "\r\n";
    CONST MAIL_CC        = "Cc:";
    CONST DISPLAY_ERROR  = "display_errors";
    CONST AUTHORIZATION  = "Authorization";
    CONST TOKEN_DECLINE  = "TOKEN decline.";
    CONST ACCESS_DECLINE = "Access decline.";

    public function __construct() {
        //
    }

    /**
     * @param bool $status
     * @param string $response
     * @return array
     */
    public function getResponseApi(bool $status, string $response){
        return array(
            SELF::RESPONSE => $status,
            SELF::RESPONSE_TEXT => $response
        );
    }

    /**
     * @return string
     */
    public function getAccessDecline(){
        return SELF::ACCESS_DECLINE;
    }

    /**
     * @return string
     */
    public function getTokenDecline(){
        return SELF::TOKEN_DECLINE;
    }

    /**
     * @return string
     */
    public function getAuthorization(){
        return SELF::AUTHORIZATION;
    }

    /**
     * @return string
     */
    public function getDisplayError(){
        return SELF::DISPLAY_ERROR;
    }

    /**
     * @return string
     */
    public function getMailCc(){
        return SELF::MAIL_CC;
    }

    /**
     * @return string
     */
    public function getLine(){
        return SELF::LINE_LINE;
    }

    /**
     * @return string
     */
    public function getMailFrom(){
        return SELF::MAIL_FROM;
    }

    /**
     * @return string
     */
    public function getMailReply(){
        return SELF::MAIL_REPLY;
    }

    /**
     * @return string
     */
    public function getMailHeaders(){
        return SELF::MAIL_HEADERS;
    }

    /**
     * @return string
     */
    public function getAddSuccess(){
        return SELF::ADD_SUCCESS;
    }
    
    /**
     * @return string
     */
    public function getAddress(){
        return SELF::COLUMN_ADDRESS;
    }
    
    /**
     * @return string
     */
    public function getPartner(){
        return SELF::COLUMN_PARTNER;
    }
    
    /**
     * @return string
     */
    public function getAccount(){
        return SELF::COLUMN_ACCOUNT;
    }
}

?>
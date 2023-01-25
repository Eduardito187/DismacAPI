<?php

namespace App\Classes\Helper;

class Text{

    CONST ADD_SUCCESS    = "Registro exitoso.";
    CONST COLUMN_ADDRESS = "address";
    CONST COLUMN_EXTRA   = "extra";
    CONST COLUMN_PARTNER = "partner";
    CONST COLUMN_ACCOUNT = "account";
    CONST RESPONSE       = "response";
    CONST RESPONSE_TEXT  = "responseText";
    CONST MAIL_FROM      = "From => ";
    CONST MAIL_REPLY     = "Reply-To => ";
    CONST MAIL_HEADERS   = "MIME-Version: 1.0\r\nContent-Type: text/html; charset=UTF-8\r\n";
    CONST LINE_LINE      = "\r\n";
    CONST MAIL_CC        = "Cc:";
    CONST DISPLAY_ERROR  = "display_errors";
    CONST AUTHORIZATION  = "Authorization";
    CONST TOKEN_DECLINE  = "TOKEN decline.";
    CONST ACCESS_DECLINE = "Access decline.";
    CONST COLUMN_ID      = "id";
    CONST COLUMN_NAME    = "name";
    CONST COLUMN_ID_CITY = "id_city";
    CONST COLUMN_TOKEN   = "token";
    CONST COLUMN_DOMAIN  = "domain";
    CONST COLUMN_CREATED = "created_at";
    CONST COLUMN_UPDATED = "updated_at";
    CONST LONGITUD       = "longitud";
    CONST LONGITUDE      = "longitude";
    CONST LATITUD        = "latitud";
    CONST LATITUDE       = "latitude";
    CONST ADDRESS_EXTRA  = "address_extra";
    CONST ID_MUNICIPALITY= "id_municipality";
    CONST ID_COUNTRY     = "id_country";
    CONST IDADDRESS_EXTRA= "id_address_extra";
    CONST ID_LOCALIZATION= "id_localization";
    CONST LOCALHOST      = "127.0.0.1";
    CONST IP_HOST        = "http://ipinfo.io/";
    CONST BARRA_JSON     = "/json";
    CONST CERO           = 0;
    CONST COMA           = ",";
    CONST ROUNDS         = "rounds";
    CONST PARTNER_ALREADY= "Partner already registered.";
    CONST NIT            = "nit";
    CONST EMAIL          = "email";
    CONST RAZON_SOCIAL   = "razon_social";
    CONST L_REPRESENTA   = "legal_representative";
    CONST USERNAME       = "username";
    CONST PASSWORD       = "password";
    CONST EMAIL_ALREADY  = "Email already registered.";
    CONST CITY           = "city";
    CONST COUNTRY        = "country";
    CONST MUNICIPALITY   = "municipality";
    CONST INTEGRATION    = "integrations_api";
    CONST CODE           = "code";
    CONST ID_ROL         = "id_rol";
    CONST ID_PERMISSIONS = "id_permissions";
    CONST ARROBA         = "@";
    CONST MESSAGES_LOGN  = [
        "Bienvenido.",
        "Contraseña erronea.",
        "La cuenta se encuentra desactivada.",
        "El usuario no se encuentra registrado.",
        "El partner ingresado no existe.",
        "Formato invalido de usuario.",
        "La cuenta no existe."
    ];
    CONST ENCRYP_METHOD  = "sha256";
    CONST ENCRYP_KEY     = "ENCRYPTION_KEY";
    CONST ID_ACCOUNT     = "id_account";
    CONST STATUS         = "status";
    CONST STORE          = "store";
    CONST Enable         = "Cuenta habilitada.";
    CONST Disable        = "Cuenta deshabilitada.";
    CONST Type           = "type";
    CONST Key            = "key";
    CONST Value          = "value";
    CONST Partner_None   = "La cuenta no esta asignada a un partner.";
    CONST ID_CATALOG     = "id_catalog";
    CONST ID_PARTNER     = "id_partner";
    CONST ID_STORE       = "id_store";
    CONST SKU            = "sku";
    CONST IMPORT_SUCCESS = "Importacion exitosa.";
    CONST OBJECT         = "object";
    CONST ERROR_PARAMETRO= "Error de parametros.";

    public function __construct() {
        //
    }

    /**
     * @param bool|array $status
     * @param string $response
     * @return array
     */
    public function getResponseApi(bool|array $status, string $response){
        return array(
            SELF::RESPONSE => $status,
            SELF::RESPONSE_TEXT => $response
        );
    }

    /**
     * @return string
     */
    public function getErrorParametros(){
        return SELF::ERROR_PARAMETRO;
    }

    /**
     * @return string
     */
    public function getObject(){
        return SELF::OBJECT;
    }

    /**
     * @return string
     */
    public function getImportSuccess(){
        return SELF::IMPORT_SUCCESS;
    }

    /**
     * @return string
     */
    public function getSku(){
        return SELF::SKU;
    }

    /**
     * @return string
     */
    public function getIdStore(){
        return SELF::ID_STORE;
    }

    /**
     * @return string
     */
    public function getIdCatalog(){
        return SELF::ID_CATALOG;
    }

    /**
     * @return string
     */
    public function getIdPartner(){
        return SELF::ID_PARTNER;
    }

    /**
     * @return string
     */
    public function getNonePartner(){
        return SELF::Partner_None;
    }

    /**
     * @return string
     */
    public function getValue(){
        return SELF::Value;
    }

    /**
     * @return string
     */
    public function getKey(){
        return SELF::Key;
    }

    /**
     * @return string
     */
    public function getType(){
        return SELF::Type;
    }

    /**
     * @return string
     */
    public function getAccountDisable(){
        return SELF::Disable;
    }

    /**
     * @return string
     */
    public function getAccountEnable(){
        return SELF::Enable;
    }

    /**
     * @return string
     */
    public function invalidFormatUser(){
        return SELF::MESSAGES_LOGN[5];
    }

    /**
     * @return string
     */
    public function AccountNotExist(){
        return SELF::MESSAGES_LOGN[6];
    }

    /**
     * @return string
     */
    public function getStore(){
        return SELF::STORE;
    }

    /**
     * @return string
     */
    public function getStatus(){
        return SELF::STATUS;
    }

    /**
     * @return string
     */
    public function getIdAccount(){
        return SELF::ID_ACCOUNT;
    }

    /**
     * @return string
     */
    public function getEncryptMethod(){
        return SELF::ENCRYP_METHOD;
    }

    /**
     * @return string
     */
    public function getEncryptKey(){
        return SELF::ENCRYP_KEY;
    }

    /**
     * @param bool $status
     * @param int $position
     * @param string $token
     * @return array
     */
    public function messageLogin(bool $status,int $position, string $token = null){
        return array(
            "status" => $status,
            "text" => SELF::MESSAGES_LOGN[$position],
            "token" => $token
        );
    }

    /**
     * @return string
     */
    public function getArroba(){
        return SELF::ARROBA;
    }

    /**
     * @return string
     */
    public function getIdRolPermissions(){
        return SELF::ID_PERMISSIONS;
    }

    /**
     * @return string
     */
    public function getIdRol(){
        return SELF::ID_ROL;
    }

    /**
     * @return string
     */
    public function getCode(){
        return SELF::CODE;
    }

    /**
     * @return string
     */
    public function getIntegrationsApi(){
        return SELF::INTEGRATION;
    }

    /**
     * @return string
     */
    public function getMunicipality(){
        return SELF::MUNICIPALITY;
    }

    /**
     * @return string
     */
    public function getCountry(){
        return SELF::COUNTRY;
    }

    /**
     * @return string
     */
    public function getCity(){
        return SELF::CITY;
    }

    /**
     * @return string
     */
    public function getEmailAlready(){
        return SELF::EMAIL_ALREADY;
    }

    /**
     * @return string
     */
    public function getUsername(){
        return SELF::USERNAME;
    }

    /**
     * @return string
     */
    public function getPassword(){
        return SELF::PASSWORD;
    }

    /**
     * @return string
     */
    public function getRazonSocial(){
        return SELF::RAZON_SOCIAL;
    }

    /**
     * @return string
     */
    public function getLegalRepresentative(){
        return SELF::L_REPRESENTA;
    }

    /**
     * @return string
     */
    public function getNit(){
        return SELF::NIT;
    }

    /**
     * @return string
     */
    public function getEmail(){
        return SELF::EMAIL;
    }

    /**
     * @return string
     */
    public function getPartnerAlready(){
        return SELF::PARTNER_ALREADY;
    }

    /**
     * @return string
     */
    public function getRounds(){
        return SELF::ROUNDS;
    }

    /**
     * @return string
     */
    public function getCero(){
        return SELF::CERO;
    }

    /**
     * @return string
     */
    public function getComa(){
        return SELF::COMA;
    }

    /**
     * @return string
     */
    public function getBarraJson(){
        return SELF::BARRA_JSON;
    }

    /**
     * @return string
     */
    public function getLocalhost(){
        return SELF::LOCALHOST;
    }

    /**
     * @return string
     */
    public function getIpHost(){
        return SELF::IP_HOST;
    }

    /**
     * @return string
     */
    public function getIdLocalization(){
        return SELF::ID_LOCALIZATION;
    }

    /**
     * @return string
     */
    public function getIdAddressExtra(){
        return SELF::IDADDRESS_EXTRA;
    }

    /**
     * @return string
     */
    public function getAddressExtra(){
        return SELF::ADDRESS_EXTRA;
    }

    /**
     * @return string
     */
    public function getIdMunicipality(){
        return SELF::ID_MUNICIPALITY;
    }

    /**
     * @return string
     */
    public function getIdCountry(){
        return SELF::ID_COUNTRY;
    }

    /**
     * @return string
     */
    public function getLongitud(){
        return SELF::LONGITUD;
    }

    /**
     * @return string
     */
    public function getLongitude(){
        return SELF::LONGITUDE;
    }

    /**
     * @return string
     */
    public function getLatitud(){
        return SELF::LATITUD;
    }

    /**
     * @return string
     */
    public function getLatitude(){
        return SELF::LATITUDE;
    }

    /**
     * @return string
     */
    public function getUpdated(){
        return SELF::COLUMN_UPDATED;
    }

    /**
     * @return string
     */
    public function getCreated(){
        return SELF::COLUMN_CREATED;
    }

    /**
     * @return string
     */
    public function getDomain(){
        return SELF::COLUMN_DOMAIN;
    }

    /**
     * @return string
     */
    public function getToken(){
        return SELF::COLUMN_TOKEN;
    }

    /**
     * @return string
     */
    public function getIdCity(){
        return SELF::COLUMN_ID_CITY;
    }

    /**
     * @return string
     */
    public function getId(){
        return SELF::COLUMN_ID;
    }

    /**
     * @return string
     */
    public function getName(){
        return SELF::COLUMN_NAME;
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
    public function getExtra(){
        return SELF::COLUMN_EXTRA;
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
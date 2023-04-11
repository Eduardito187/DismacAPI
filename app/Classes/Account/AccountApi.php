<?php

namespace App\Classes\Account;

use App\Models\Account;
use App\Models\AccountLogin;
use App\Classes\Helper\Date;
use App\Classes\Helper\Status;
use App\Classes\Helper\Text;
use App\Classes\Partner\PartnerApi;
use \Illuminate\Http\Request;
use Exception;
use App\Classes\TokenAccess;
use App\Models\AccountPartner;
use App\Models\Catalog;
use App\Models\CatalogPartner;

class AccountApi{

    /**
     * @var Account
     */
    protected $account;
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
    /**
     * @var PartnerApi
     */
    protected $partner;
    /**
     * @var TokenAccess
     */
    protected $tokenAccess;

    public function __construct() {
        $this->date        = new Date();
        $this->status      = new Status();
        $this->text        = new Text();
        $this->partner     = new PartnerApi();
    }

    /**
     * @param int $id
     * @return array|null
     */
    public function byId(int $id){
        $Account = Account::find($id);
        return $Account;
    }

    public function byEmail(){
    }

    public function byUser(){
    }

    public function login(){
    }

    /**
     * @param array $account
     * @return void
     */
    public function create(array $account){
        if (is_null($this->getAccountByEmail($account[$this->text->getEmail()]))) {
            $this->createAccount($account);
        }
        $this->setAccount($account);
        if (is_null($this->getAccountLogin())) {
            $this->createAccountLoggin($account);
        }
    }

    /**
     * @return int|null
     */
    public function getAccountLogin(){
        $AccountLogin = AccountLogin::select($this->text->getId())->where($this->text->getIdAccount(), $this->account->id)->get()->toArray();
        if (count($AccountLogin) > 0) {
            return $AccountLogin[0][$this->text->getId()];
        }else{
            return null;
        }
    }

    /**
     * @return int
     */
    public function getAccountId(){
        return $this->account->id;
    }

    /**
     * @param array $account
     * @return void
     */
    private function setAccount(array $account){
        $this->account = Account::where($this->text->getEmail(), $account[$this->text->getEmail()])->
        where($this->text->getToken(), $this->generate64B($account[$this->text->getEmail()]))->first();
    }

    /**
     * @param array $account
     * @return bool
     */
    public function createAccount(array $account){
        try {
            $this->validateEmail($account[$this->text->getEmail()]);
            $Account = new Account();
            $Account->name = $account[$this->text->getName()];
            $Account->email = $account[$this->text->getEmail()];
            $Account->token = $this->generate64B($account[$this->text->getEmail()]);
            $Account->created_at = $this->date->getFullDate();
            $Account->updated_at = null;
            $Account->save();
            return true;
        } catch (Exception $th) {
            return false;
        }
    }

    /**
     * @param string $email
     * @return string
     */
    private function generate64B(string $email){
        return base64_encode($email);
    }

    /**
     * @param string $email
     */
    private function validateEmail(string $email){
        $Emails = Account::select($this->text->getId())->where($this->text->getEmail(), $email)->get()->toArray();
        if (count($Emails) > 0) {
            throw new Exception($this->text->getEmailAlready());
        }
    }

    /**
     * @param string $token
     * @return array
     */
    public function getCurrentAccount($token){
        $id_Account = $this->getAccountToken($token);
        $Account = Account::find($id_Account);
        if (!$Account) {
            throw new Exception($this->text->AccountNotExist());
        }
        return $this->requestAccount($Account);
    }

    /**
     * @param Account $Account
     * @return array
     */
    public function requestAccount(Account $Account){
        $Partner = $Account->accountPartner;
        return $Partner->toArray();
        return array(
            "id" => $Account->id,
            "name" => $Account->name,
            "email" => $Account->email,
            "profile" => $Partner->Profile->url,
            "cover" => $Partner->Front->url
        );
    }

    /**
     * @param Request $request
     * @return array
     */
    public function searchAccount(Request $request){
        $param = $request->all()[$this->text->getQuery()];
        $id_Account = $this->getAccountToken($request->header($this->text->getAuthorization()));
        $AccountPartner = AccountPartner::select($this->text->getIdAccount())->where($this->text->getIdAccount(), $this->text->getDistinctSymbol(), $id_Account)->where($this->text->getIdPartner(), $this->getPartnerId($id_Account))->get()->toArray();
        $Accounts = Account::select($this->text->getId(),$this->text->getName(),$this->text->getEmail())->whereIn($this->text->getId(), $AccountPartner)->where($this->text->getName(), $this->text->getLike(), $this->text->getPercent().$param.$this->text->getPercent())->
        orwhere($this->text->getEmail(), $this->text->getLike(), $this->text->getPercent().$param.$this->text->getPercent())->whereIn($this->text->getId(), $AccountPartner)->with([$this->text->getAccountStatus(), $this->text->getRolAccount() => function ($query) {
            $query->with([$this->text->getRol()]);
        }])->get()->toArray();
        return $Accounts;
    }

    /**
     * @param Request $request
     * @return array
     */
    public function searchCatalog(Request $request){
        $param = $request->all()[$this->text->getQuery()];
        $id_Account = $this->getAccountToken($request->header($this->text->getAuthorization()));
        $CatalogPartner = CatalogPartner::select($this->text->getIdCatalog())->where($this->text->getIdPartner(), $this->getPartnerId($id_Account))->get()->toArray();
        $Catalogs = Catalog::select($this->text->getId(),$this->text->getName(),$this->text->getCode())->whereIn($this->text->getId(), $CatalogPartner)->where($this->text->getName(), $this->text->getLike(), $this->text->getPercent().$param.$this->text->getPercent())->
        orwhere($this->text->getCode(), $this->text->getLike(), $this->text->getPercent().$param.$this->text->getPercent())->whereIn($this->text->getId(), $CatalogPartner)->get()->toArray();
        return $Catalogs;
    }

    /**
     * @param int $ID
     * @param bool $status
     */
    public function statusAccount(int $ID, bool $status){
        $Account=Account::find($ID);
        if ($Account!=null) {
            $time = $this->date->getFullDate();
            Account::where($this->text->getId(), $ID)->update([
                $this->text->getUpdated() => $time
            ]);
            AccountLogin::where($this->text->getIdAccount(), $ID)->update([
                $this->text->getStatus() => $status,
                $this->text->getUpdated() => $time
            ]);
        }else{
            throw new Exception($this->text->AccountNotExist());
        }
    }

    /**
     * @param int $idAccount
     * @return int|null
     */
    public function getPartnerId(int $idAccount){
        $AccountPartner = AccountPartner::select($this->text->getIdPartner())->where($this->text->getIdAccount(), $idAccount)->get()->toArray();
        if (count($AccountPartner) > 0) {
            return $AccountPartner[0][$this->text->getIdPartner()];
        }else{
            throw new Exception($this->text->getNonePartner());
        }
    }

    /**
     * @param string $value
     * @return int $ID
     */
    public function getAccountToken(string $value){
        $this->tokenAccess = new TokenAccess($value);
        $Account = Account::select($this->text->getId())->where($this->text->getToken(), $this->tokenAccess->getToken())->get()->toArray();
        if (count($Account) > 0) {
            return $Account[0][$this->text->getId()];
        }else{
            throw new Exception($this->text->AccountNotExist());
        }
    }

    /**
     * @param string $value
     * @return int $ID
     */
    public function getAccountKey(string $value){
        $Account = Account::select($this->text->getId())->where($this->text->getId(), $value)->get()->toArray();
        if (count($Account) > 0) {
            return $Account[0][$this->text->getId()];
        }else{
            throw new Exception($this->text->AccountNotExist());
        }
    }

    /**
     * @param string $value
     * @return int|null
     */
    public function getAccountByEmail(string $value){
        $Account = Account::select($this->text->getId())->where($this->text->getEmail(), $value)->get()->toArray();
        if (count($Account) > 0) {
            return $Account[0][$this->text->getId()];
        }else{
            return null;
        }
    }

    /**
     * @param string $value
     * @return int $ID
     */
    public function getAccountEmail(string $value){
        $Account = Account::select($this->text->getId())->where($this->text->getEmail(), $value)->get()->toArray();
        if (count($Account) > 0) {
            return $Account[0][$this->text->getId()];
        }else{
            throw new Exception($this->text->AccountNotExist());
        }
    }

    /**
     * @param string $email
     * @return bool
     */
    public function verifyEmail(string $email){
        $Emails = Account::select($this->text->getId())->where($this->text->getEmail(), $email)->get()->toArray();
        if (count($Emails) > 0) {
            return false;
        }else{
            return true;
        }
    }

    /**
     * @param string $username
     * @return array|null
     */
    private function getByUsernameLogin(string $username){
        $AccountLogin = AccountLogin::select($this->text->getId(), $this->text->getPassword(), $this->text->getStatus(), $this->text->getIdAccount())->
        where($this->text->getUsername(), $username)->get()->toArray();
        if (count($AccountLogin) > 0) {
            return $AccountLogin;
        }else{
            return null;
        }
    }

    /**
     * @param array $account
     * @return bool
     */
    public function createAccountLoggin(array $account){
        try {
            $AccountLogin = new AccountLogin();
            $AccountLogin->username = $account[$this->text->getUsername()];
            $AccountLogin->password = $this->encriptionPawd($account[$this->text->getPassword()]);
            $AccountLogin->status = $this->status->getEnable();
            $AccountLogin->id_account = $this->account->id;
            $AccountLogin->created_at = $this->date->getFullDate();
            $AccountLogin->updated_at = null;
            $AccountLogin->save();
            return true;
        } catch (Exception $th) {
            return false;
        }
    }

    /**
     * @param string $password
     * @return string
     */
    private function encriptionPawd(string $password){
        return hash_hmac($this->text->getEncryptMethod(), $password, env($this->text->getEncryptKey()));
    }

    /**
     * @param Request $request
     * @return array
     */
    public function validateLogin(Request $request){
        return $this->validateAccountLogin($request->all()[$this->text->getUsername()], $request->all()[$this->text->getPassword()]);
    }

    /**
     * @param string $username
     * @param string $password
     * @return array
     */
    private function validateAccountLogin(string $username, string $password){
        $arrayUser = explode ($this->text->getArroba(), $username);
        if (count($arrayUser) == 2) {
            if ($this->partner->issetDomain($arrayUser[0])) {
                $response = $this->getByUsernameLogin($arrayUser[1]);
                if ($response != null) {
                    if ($response[0][$this->text->getStatus()] == 1) {
                        if ($response[0][$this->text->getPassword()] == $this->encriptionPawd($password)) {
                            $Account = $this->byId($response[0][$this->text->getIdAccount()]);
                            if ($Account != null) {
                                return $this->text->messageLogin(true, 0, $Account[$this->text->getToken()]);
                            }else{
                                return $this->text->messageLogin(false, 6);
                            }
                        }else{
                            return $this->text->messageLogin(false, 1);
                        }
                    }else{
                        return $this->text->messageLogin(false, 2);
                    }
                }else{
                    return $this->text->messageLogin(false, 3);
                }
            }else{
                return $this->text->messageLogin(false, 4);
            }
        }else{
            return $this->text->messageLogin(false, 5);
        }
    }
}

?>
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
     * @param string $domain
     * @return void
     */
    public function create(array $account){
        $this->createAccount($account);
        $this->setAccount($account);
        $this->createAccountLoggin($account);
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
     * @param Request $request
     * @return array
     */
    public function searchAccount(Request $request){
        $param = $request->all()["query"];
        $token = $request->header($this->text->getAuthorization());
        $AccountPartner = AccountPartner::select("id_account")->where("id_partner", $this->getAccountToken($token))->get()->toArray();
        $Accounts = Account::whereIn("id", $AccountPartner)->where("name", "like", "%".$param."%")->orwhere("email", "like", "%".$param."%")->whereIn("id", $AccountPartner)->get()->toArray();
        return $Accounts;
    }

    /**
     * @param int $ID
     * @param bool $status
     */
    public function statusAccount(int $ID, bool $status){
        $Account=Account::find($ID);
        if ($Account!=null) {
            $time = date("Y-m-d H:i:s");
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
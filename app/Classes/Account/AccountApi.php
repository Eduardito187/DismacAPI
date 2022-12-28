<?php

namespace App\Classes\Account;

use App\Models\Account;
use App\Models\AccountLogin;
use App\Classes\Helper\Date;
use App\Classes\Helper\Status;
use Illuminate\Support\Facades\Hash;

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

    public function __construct() {
        $this->date     = new Date();
        $this->status   = new Status();
    }

    public function byId(){
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
        $this->createAccountLoggin($account);
        $this->setAccount($account);
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
        $this->account = Account::where("email", $account["email"])->where('token', $this->generate64B($account["email"]))->first();
    }

    /**
     * @param array $account
     * @return bool
     */
    public function createAccount(array $account){
        try {
            $this->validateEmail($account["email"]);
            $Account = new Account();
            $Account->name = $account["name"];
            $Account->email = $account["email"];
            $Account->token = $this->generate64B($account["email"]);
            $Account->created_at = $this->date->getFullDate();
            $Account->updated_at = null;
            $Account->save();
            return true;
        } catch (\Throwable $th) {
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
        $Emails = Account::select('id')->where('email', $email)->get()->toArray();
        if (count($Emails) > 0) {
            throw new \Throwable('Email already registered');
        }
    }

    /**
     * @param array $account
     * @return bool
     */
    public function createAccountLoggin(array $account){
        try {
            $AccountLogin = new AccountLogin();
            $AccountLogin->username = $account["username"];
            $AccountLogin->password = $this->encriptionPawd($account["password"]);
            $AccountLogin->status = $this->status->getEnable();
            $AccountLogin->id_account = $this->account->id;
            $AccountLogin->created_at = $this->date->getFullDate();
            $AccountLogin->updated_at = null;
            $AccountLogin->save();
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    /**
     * @param string $password
     * @return string
     */
    private function encriptionPawd(string $password){
        return Hash::make($password);
    }
}

?>
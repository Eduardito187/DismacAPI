<?php

namespace App\Classes\Account;

use App\Models\Account;
use App\Models\AccountLogin;
use App\Classes\Helper\Date;
use App\Classes\Helper\Status;

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
     * @param string $name
     * @param string $email
     * @param string $username
     * @param string $password
     * @param string $domain
     * @return boolean
     */
    public function create(string $name, string $email, string $username, string $password, string $domain){
        $message = $this->createAccount($name, $email);
        if ($message == "Successful") {
            $this->account = Account::where("email", $email)->first();
            $message = $this->createAccountLoggin($username, $password, $domain);
            if ($message == "Successful") {
                # code...
            }
        }
        return $message;
    }

    /**
     * @param string $name
     * @param string $email
     * @return string
     */
    public function createAccount(string $name, string $email){
        try {
            $this->validateEmail($email);
            $account = new Account();
            $account->name = $name;
            $account->email = $email;
            $account->created_at = $this->date->getFullDate();
            $account->updated_at = null;
            $account->save();
            return "Successful";
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    private function validateEmail(string $email){
        $Emails = Account::select('id')->where('email', $email)->get()->toArray();
        if (count($Emails) > 0) {
            throw new \Throwable('Email already registered');
        }
    }

    /**
     * @param string $username
     * @param string $password
     * @return string
     */
    public function createAccountLoggin(string $username, string $password, $domain){
        try {
            $account = new AccountLogin();
            $account->username = $username;
            $account->password = $password;
            $account->status = $this->status->getEnable();
            $account->id_account = $this->account->id;
            $account->created_at = $this->date->getFullDate();
            $account->updated_at = null;
            $account->save();
            return "Successful";
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }
}

?>
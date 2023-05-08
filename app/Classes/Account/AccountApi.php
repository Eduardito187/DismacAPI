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
use App\Models\Mejoras;
use App\Models\Partner;
use App\Models\SupportTechnical;

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
     * @return Account
     */
    public function getCurrentAccount(string $token){
        $id_Account = $this->getAccountToken($token);
        $Account = Account::find($id_Account);
        if (!$Account) {
            throw new Exception($this->text->AccountNotExist());
        }
        return $Account;
    }

    /**
     * @param string $token
     * @return array
     */
    public function currentAccountArray(string $token){
        return $this->requestAccount($this->getCurrentAccount($token));
    }

    /**
     * @param string $token
     * @param array $data
     * @return bool
     */
    public function createImprovement(string $token, array $data){
        $Account = $this->getCurrentAccount($token);
        return $this->newImprovement($Account->id, $data);
    }

    /**
     * @param string $token
     * @return array
     */
    public function getTicketsAccount(string $token){
        $Account = $this->getCurrentAccount($token);
        $Support = $this->getTicketByAccount($Account->id);
        return $this->convertSupportTechnical($Support);
    }

    /**
     * @param string $token
     * @return array
     */
    public function getTicketsPartner(string $token){
        $Account = $this->getCurrentAccount($token);
        $Support = $this->getTicketByPartner($Account->accountPartner->id_partner);
        return $this->convertSupportTechnical($Support);
    }

    public function convertSupportTechnical($Supports){
        $data = array();
        foreach ($Supports as $key => $support) {
            $data[] = array(
                $this->text->getId() => $support->id,
                $this->text->getAccount() => $this->accountSimple($support->Account),
                $this->text->getPartner() => $this->getPartner($support->Partner),
                $this->text->getTitle() => $support->title,
                $this->text->getDescription() => $support->description,
                $this->text->getStatus() => $support->status,
                $this->text->getTime() => "1 D",
                $this->text->getCreated() => $support->created_at,
                $this->text->getUpdated() => $support->updated_at
            );
        }
        return $data;
    }

    /**
     * @param int $id_account
     * @return SupportTechnical[]
     */
    public function getTicketByAccount(int $id_account){
        return SupportTechnical::where($this->text->getIdAccount(), $id_account)->get();
    }

    /**
     * @param int $id_partner
     * @return SupportTechnical[]
     */
    public function getTicketByPartner(int $id_partner){
        return SupportTechnical::where($this->text->getIdPartner(), $id_partner)->get();
    }

    /**
     * @param string $token
     * @param bool $status
     * @return array
     */
    public function getImprovementsApi(string $token, bool $status){
        $Account = $this->getCurrentAccount($token);
        $Mejoras = $this->getImprovement($Account->id, $status);
        return $this->convertImprovementArray($Mejoras, $Account);
    }

    public function convertImprovementArray($Mejoras, Account $Account){
        $data = array();
        foreach ($Mejoras as $key => $mejora) {
            $data[] = array(
                $this->text->getId() => $mejora->id,
                $this->text->getAccount() => $this->accountSimple($Account),
                $this->text->getTitle() => $mejora->title,
                $this->text->getDescription() => $mejora->description,
                $this->text->getStatus() => $mejora->status,
                $this->text->getTime() => "1 D",
                $this->text->getCreated() => $mejora->created_at,
                $this->text->getUpdated() => $mejora->updated_at
            );
        }
        return $data;
    }

    /**
     * @param int $id_account
     * @param bool $status
     * @return Mejoras[]
     */
    public function getImprovement(int $id_account, bool $status){
        return Mejoras::where($this->text->getIdAccount(), $id_account)->where($this->text->getStatus(), $status)->get();
    }

    /**
     * @param int $idAccount
     * @param array $data
     * @return bool
     */
    public function newImprovement(int $idAccount, array $data){
        try {
            $Mejora = new Mejoras();
            $Mejora->id_account = $idAccount;
            $Mejora->title = $data[$this->text->getTitle()];
            $Mejora->description = $data[$this->text->getDescription()];
            $Mejora->status = $this->status->getDisable();
            $Mejora->created_at = $this->date->getFullDate();
            $Mejora->updated_at = null;
            $Mejora->save();
            return true;
        } catch (Exception $th) {
            return false;
        }
    }
    
    /**
     * @param string $token
     * @param array $data
     * @return bool
     */
    public function createSupport(string $token, array $data){
        $Account = $this->getCurrentAccount($token);
        $id_partner = $Account->accountPartner->id_partner;
        return $this->newSupport($Account->id, $id_partner, $data);
    }

    
    /**
     * @param int $idAccount
     * @param int $id_partner
     * @param array $data
     * @return bool
     */
    public function newSupport(int $idAccount, int $id_partner, array $data){
        try {
            $SupportTechnical = new SupportTechnical();
            $SupportTechnical->id_account = $idAccount;
            $SupportTechnical->id_partner = $id_partner;
            $SupportTechnical->title = $data[$this->text->getTitle()];
            $SupportTechnical->description = $data[$this->text->getDescription()];
            $SupportTechnical->status = $this->status->getDisable();
            $SupportTechnical->created_at = $this->date->getFullDate();
            $SupportTechnical->updated_at = null;
            $SupportTechnical->save();
            return true;
        } catch (Exception $th) {
            return false;
        }
    }

    /**
     * @param Account $Account
     * @return array
     */
    public function requestAccount(Account $Account){
        $Partner = $Account->accountPartner->Partner;
        return array(
            $this->text->getId() => $Account->id,
            $this->text->getName() => $Account->name,
            $this->text->getEmail() => $Account->email,
            $this->text->getProfile() => $Partner->Profile->url,
            $this->text->getCover() => $Partner->Front->url
        );
    }

    /**
     * @param Account $Account
     * @return array
     */
    public function accountSimple(Account $Account){
        return array(
            $this->text->getId() => $Account->id,
            $this->text->getName() => $Account->name,
            $this->text->getEmail() => $Account->email
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
     * @return int
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
     * @return Account
     */
    public function getAccountByToken(string $value){
        $this->tokenAccess = new TokenAccess($value);
        $Account = Account::where($this->text->getToken(), $this->tokenAccess->getToken())->first();
        if (!$Account) {
            throw new Exception($this->text->AccountNotExist());
        }
        return $Account;
    }
    
    /**
     * @param Partner $partner
     * @return int
     */
    public function getAccountsPartner(Partner $partner){
        return $this->countAccountPartner($partner->id);
    }

    /**
     * @param int $id_partner
     * @return int
     */
    public function countAccountPartner(int $id_partner){
        return AccountPartner::select($this->text->getIdAccount())->where($this->text->getIdPartner(), $id_partner)->distinct()->count($this->text->getIdAccount());
    }

    /**
     * @param Partner $partner
     * @return array
     */
    public function getPartner(Partner $partner){
        return array(
            $this->text->getId() => $partner->id,
            $this->text->getName() => $partner->name,
            $this->text->getDomain() => $partner->domain,
            $this->text->getEmail() => $partner->email,
            $this->text->getProfile() => $partner->Profile->url,
            $this->text->getCover() => $partner->Front->url,
            $this->text->getToken() => $partner->token
        );
    }

    /**
     * @param string $value
     * @return int
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
     * @return int
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
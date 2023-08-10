<?php

namespace App\Classes\Account;

use App\Models\Account;
use App\Models\AccountLogin;
use App\Classes\Helper\Date;
use App\Classes\Helper\Ip;
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
use App\Classes\Address\AddressApi;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\PartnerSession;
use App\Models\Picture;
use App\Models\Rol;
use App\Models\RolAccount;
use App\Models\Session;
use App\Classes\Analytics\Analytics;
use App\Models\SessionToken;
use App\Classes\Tools\Sockets;

class AccountApi{
    const DEFAULT_IMAGE = 3;
    const TYPE_ANALYTICS_ACCOUNT = "Account";
    const TYPE_ANALYTICS_COUPON = "Coupon";
    const TYPE_ANALYTICS_CATEGORY = "Category";
    const TYPE_ANALYTICS_CATALOG = "Catalog";
    const SEARCH_ACCOUNT = "SEARCH_ACCOUNT";
    const SEARCH_ACCOUNT_RESPONSE = "SEARCH_ACCOUNT_RESPONSE";
    const SEARCH_COUPON = "SEARCH_COUPON";
    const SEARCH_COUPON_RESPONSE = "SEARCH_COUPON_RESPONSE";
    const SEARCH_CATEGORY = "SEARCH_CATEGORY";
    const SEARCH_CATEGORY_RESPONSE = "SEARCH_CATEGORY_RESPONSE";
    const SEARCH_CATALOG = "SEARCH_CATALOG";
    const SEARCH_CATALOG_RESPONSE = "SEARCH_CATALOG_RESPONSE";
    const VALUE_ANALYTICS = 1;
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
    /**
     * @var AddressApi
     */
    protected $addressApi;/**
     * @var Analytics
     */
    protected $Analytics;
    /**
     * @var Sockets
     */
    protected $Sockets;

    public function __construct() {
        $this->date        = new Date();
        $this->status      = new Status();
        $this->text        = new Text();
        $this->partner     = new PartnerApi();
        $this->addressApi  = new AddressApi();
        $this->Analytics   = new Analytics();
        $this->Sockets     = new Sockets();
    }

    /**
     * @param int $id
     * @return Account
     */
    public function byId(int $id){
        return Account::find($id);
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
        $Account = $this->getAccountById($id_Account);
        if (!$Account) {
            throw new Exception($this->text->AccountNotExist());
        }
        return $Account;
    }

    /**
     * @param int|null|string $id
     * @return Account
     */
    public function getAccountById(string $id){
        $Account = Account::find($id);
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
     * @return array
     */
    public function getRolAccount(string $token){
        $Account = $this->getCurrentAccount($token);
        return $this->rolAccountArray($Account->rolAccount);
    }

    public function rolAccountArray($rolAccount){
        return $this->getRolsAccountArray($rolAccount);
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
     * @param array $data
     * @return bool
     */
    public function registerToken(string $token, array $data){
        $Account = $this->getCurrentAccount($token);
        if (array_key_exists($this->text->getToken(), $data)){
            $TOKEN = $data[$this->text->getToken()];
            $this->validateSessionToken($Account->id, $TOKEN);
            return $this->createSessionToken($Account->id, $TOKEN);
        }else{
            throw new Exception($this->text->getParametersNone());
        }
    }
    
    /**
     * @param int $idAccount
     * @param string $token
     * @return bool
     */
    public function createSessionToken(int $idAccount, string $token){
        try {
            $SessionToken = new SessionToken();
            $SessionToken->id_account = $idAccount;
            $SessionToken->token = $token;
            $SessionToken->status = $this->status->getEnable();
            $SessionToken->created_at = $this->date->getFullDate();
            $SessionToken->updated_at = null;
            $SessionToken->save();
            return true;
        } catch (Exception $th) {
            return false;
        }
    }

    /**
     * @param int $idAccount
     * @param string $TOKEN
     * @return void
     */
    public function validateSessionToken(int $idAccount, string $TOKEN){
        //$SessionToken = SessionToken::where($this->text->getIdAccount(), $idAccount)->first();
        SessionToken::where($this->text->getIdAccount(), $idAccount)->delete();
        $data = array($this->text->getIdAccount() => $idAccount, $this->text->getToken() => $TOKEN);
        $this->Sockets->sendQueryPost($this->text->getCloseAccount(), $data);
    }

    /**
     * @return Rol[]
     */
    public function getRols(){
        return Rol::all();
    }

    /**
     * @return array
     */
    public function getAllRols(){
        $rol = $this->getRols();
        return $this->getRolArray($rol);
    }

    public function getRolArray($rol){
        $data = array();
        foreach ($rol as $key => $ROL) {
            $data[] = $this->rolArray($ROL);
        }
        return $data;
    }
    
    public function getRolsAccountArray($rol){
        $data = array();
        foreach ($rol as $key => $ROL) {
            $data[] = $this->rolArrayPermissions($ROL->rol);
        }
        return $data;
    }
    
    public function rolArrayPermissions($ROL){
        return array(
            $this->text->getId() => $ROL->id,
            $this->text->getName() => $ROL->name,
            $this->text->getCode() => $ROL->code,
            $this->text->getPermissions() => $this->getRolPermissions($ROL->rolPermissions)
        );
    }

    public function getRolPermissions($rolPermissions){
        if (is_null($rolPermissions)){
            return [];
        }
        $data = array();
        foreach ($rolPermissions as $key => $rolPermission) {
            $data[] = $this->getPermissionsArray($rolPermission->permissions);
        }
        return $data;
    }

    public function getPermissionsArray($permissions){
        if (is_null($permissions)){
            return null;
        }
        return $this->rolArray($permissions);
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
                $this->text->getTime() => $this->date->getDiferenceInDates($this->date->getFullDate(), $support->created_at, null),
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
                $this->text->getTime() => $this->date->getDiferenceInDates($this->date->getFullDate(), $mejora->created_at, null),
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
            $Mejora->status = $this->status->getEnable();
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
            $SupportTechnical->status = $this->status->getEnable();
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
            $this->text->getCover() => $Partner->Front->url,
            $this->text->getIdPartner() => $Partner->id,
            $this->text->getRoles() => $this->rolAccountArray($Account->rolAccount)
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
        $idPartner = $this->getPartnerId($id_Account);
        $this->Analytics->registerAnalytics(null, null, self::TYPE_ANALYTICS_ACCOUNT, self::SEARCH_ACCOUNT, null, $param, $idPartner);
        return $this->getAccountSearch($id_Account, $param, $idPartner);
    }

    /**
     * @param int $id_Account
     * @param int|null $idPartner
     * @return array
     */
    private function getAccountPartner(int $id_Account, int|null $idPartner){
        return AccountPartner::select($this->text->getIdAccount())->where($this->text->getIdAccount(), $this->text->getDistinctSymbol(), $id_Account)->where($this->text->getIdPartner(), $idPartner)->get()->toArray();
    }

    /**
     * @param array $AccountPartner
     * @param string $quer
     * @return Account[]
     */
    private function getAccountFilters(array $AccountPartner, string $query){
        return Account::whereIn($this->text->getId(), $AccountPartner)->where($this->text->getName(), $this->text->getLike(), $this->text->getPercent().$query.$this->text->getPercent())->
        orwhere($this->text->getEmail(), $this->text->getLike(), $this->text->getPercent().$query.$this->text->getPercent())->whereIn($this->text->getId(), $AccountPartner)->get();
    }

    /**
     * @param int $id_Account
     * @param string $query
     * @return array
     */
    private function getAccountSearch(int $id_Account, string $query, int|null $idPartner){
        $AccountPartner = $this->getAccountPartner($id_Account, $idPartner);
        $accounts = $this->getAccountFilters($AccountPartner, $query);
        return $this->convertAccountArray($accounts, $idPartner);
    }

    private function convertAccountArray($accounts, $idPartner){
        $data = array();
        foreach ($accounts as $key => $account) {
            $data[] = $this->accountToArray($account, true, $idPartner);
        }
        return $data;
    }

    public function accountToArray($account, $type = false, $idPartner = null){
        if ($type){
            $this->Analytics->registerAnalytics(null, null, self::TYPE_ANALYTICS_ACCOUNT, self::SEARCH_ACCOUNT_RESPONSE, $account->id, self::VALUE_ANALYTICS, $idPartner);
        }
        return array(
            $this->text->getId() => $account->id,
            $this->text->getName() => $account->name,
            $this->text->getEmail() => $account->email,
            $this->text->getStatus() => $this->getStatusAccount($account->accountStatus),
            $this->text->getRolAccountParam() => $this->getRolAccountArray($account->rolAccount),
            $this->text->getPartner() => $this->getPartnerAccount($account->accountPartner->Partner ?? null)
        );
    }

    /**
     * @param Partner|null $partner
     * @return array
     */
    public function getPartnerAccount(Partner|null $partner){
        if (is_null($partner)){
            return null;
        }
        return array(
            $this->text->getId() => $partner->id,
            $this->text->getName() => $partner->name,
            $this->text->getDomain() => $partner->domain,
            $this->text->getEmail() => $partner->email,
            $this->text->getProfile() => $this->getPictureById($partner->picture_profile),
            $this->text->getCover() => $this->getPictureById($partner->picture_front),
            $this->text->getToken() => $partner->token
        );
    }

    /**
     * @param int|null $id_picture
     * @return string
     */
    public function getPictureById(int|null $id_picture){
        $Picture = $this->getImageById($id_picture ?? $this::DEFAULT_IMAGE);
        return $this->getPublicUrlImage($Picture);
    }

    /**
     * @param int $id
     * @return Picture
     */
    public function getImageById(int $id){
        return Picture::find($id);
    }

    /**
     * @param Picture $Picture
     * @return string
     */
    public function getPublicUrlImage(Picture $Picture){
        return $Picture->url;
    }

    /**
     * @param int $ID
     * @return array
     */
    public function getAccountQuery(int $ID){
        $Account = $this->getAccountById($ID);
        if ($Account != null) {
            return $this->accountToArray($Account);
        }else{
            throw new Exception($this->text->AccountNotExist());
        }
    }

    private function getRolAccountArray($rolAccount){
        $data = array();
        foreach ($rolAccount as $key => $rol) {
            $ROL = $rol->rol;
            $data[] = $this->rolArray($ROL);
        }
        return $data;
    }

    public function rolArray($ROL){
        return array(
            $this->text->getId() => $ROL->id,
            $this->text->getName() => $ROL->name,
            $this->text->getCode() => $ROL->code
        );
    }

    private function getStatusAccount($accountStatus){
        if (!$accountStatus){
            return false;
        }
        return $accountStatus->status;
    }

    /**
     * @param Request $request
     * @return array
     */
    public function searchCoupon(Request $request){
        $param = $request->all()[$this->text->getQuery()];
        $id_Account = $this->getAccountToken($request->header($this->text->getAuthorization()));
        $idPartner = $this->getPartnerId($id_Account);
        $this->Analytics->registerAnalytics(null, null, self::TYPE_ANALYTICS_COUPON, self::SEARCH_COUPON, null, $param, $idPartner);
        $Coupons = Coupon::select($this->text->getId(),$this->text->getName(),$this->text->getCouponCode())->where($this->text->getIdPartner(), $idPartner)->where($this->text->getName(), $this->text->getLike(), $this->text->getPercent().$param.$this->text->getPercent())->
        orwhere($this->text->getCouponCode(), $this->text->getLike(), $this->text->getPercent().$param.$this->text->getPercent())->where($this->text->getIdPartner(), $idPartner)->get();
        return $this->processResponseCoupon($Coupons, $idPartner);
    }

    private function processResponseCoupon($Coupons, $idPartner){
        $data = array();
        foreach($Coupons as $key => $Coupon){
            $this->Analytics->registerAnalytics(null, null, self::TYPE_ANALYTICS_COUPON, self::SEARCH_COUPON_RESPONSE, $Coupon->id, self::VALUE_ANALYTICS, $idPartner);
            $data[] = array(
                $this->text->getId() => $Coupon->id,
                $this->text->getName() => $Coupon->name,
                $this->text->getCouponCode() => $Coupon->coupon_code
            );
        }
        return $data;
    }

    /**
     * @param Request $request
     * @return array
     */
    public function searchCategory(Request $request){
        $param = $request->all()[$this->text->getQuery()];
        $id_Account = $this->getAccountToken($request->header($this->text->getAuthorization()));
        $idPartner = $this->getPartnerId($id_Account);
        $this->Analytics->registerAnalytics(null, null, self::TYPE_ANALYTICS_CATEGORY, self::SEARCH_CATEGORY, null, $param, $idPartner);
        $Categorys = Category::select($this->text->getId(),$this->text->getName(),$this->text->getCode())->where($this->text->getIdPartner(), $idPartner)->where($this->text->getName(), $this->text->getLike(), $this->text->getPercent().$param.$this->text->getPercent())->
        orwhere($this->text->getCode(), $this->text->getLike(), $this->text->getPercent().$param.$this->text->getPercent())->where($this->text->getIdPartner(), $idPartner)->get();
        return $this->processResponseCategory($Categorys, $idPartner);
    }

    private function processResponseCategory($Categorys, $idPartner){
        $data = array();
        foreach($Categorys as $key => $Category){
            $this->Analytics->registerAnalytics(null, null, self::TYPE_ANALYTICS_CATEGORY, self::SEARCH_CATEGORY_RESPONSE, $Category->id, self::VALUE_ANALYTICS, $idPartner);
            $data[] = array(
                $this->text->getId() => $Category->id,
                $this->text->getName() => $Category->name,
                $this->text->getCode() => $Category->code
            );
        }
        return $data;
    }

    /**
     * @param Request $request
     * @return array
     */
    public function searchCatalog(Request $request){
        $param = $request->all()[$this->text->getQuery()];
        $id_Account = $this->getAccountToken($request->header($this->text->getAuthorization()));
        $idPartner = $this->getPartnerId($id_Account);
        $CatalogPartner = CatalogPartner::select($this->text->getIdCatalog())->where($this->text->getIdPartner(), $idPartner)->get()->toArray();
        $this->Analytics->registerAnalytics(null, null, self::TYPE_ANALYTICS_CATALOG, self::SEARCH_CATALOG, null, $param, $idPartner);
        $Catalogs = Catalog::select($this->text->getId(),$this->text->getName(),$this->text->getCode())->whereIn($this->text->getId(), $CatalogPartner)->where($this->text->getName(), $this->text->getLike(), $this->text->getPercent().$param.$this->text->getPercent())->
        orwhere($this->text->getCode(), $this->text->getLike(), $this->text->getPercent().$param.$this->text->getPercent())->whereIn($this->text->getId(), $CatalogPartner)->get();
        return $this->processResponseCatalog($Catalogs, $idPartner);
    }

    private function processResponseCatalog($Catalogs, $idPartner){
        $data = array();
        foreach($Catalogs as $key => $Catalog){
            $this->Analytics->registerAnalytics(null, null, self::TYPE_ANALYTICS_CATALOG, self::SEARCH_CATALOG_RESPONSE, $Catalog->id, self::VALUE_ANALYTICS, $idPartner);
            $data[] = array(
                $this->text->getId() => $Catalog->id,
                $this->text->getName() => $Catalog->name,
                $this->text->getCode() => $Catalog->code
            );
        }
        return $data;
    }

    /**
     * @param int $ID
     * @param bool $status
     */
    public function statusAccount(int $ID, bool $status){
        $Account = $this->getAccountById($ID);
        if ($Account != null) {
            $time = $this->date->getFullDate();

            $this->updateAccountAt($ID, $time);

            if ($status == $this->status->getDisable()){
                $data = array($this->text->getIdAccount() => $ID);
                $this->Sockets->sendQueryPost($this->text->getDisableAccount(), $data);
            }

            $this->updateAccountLoginStatus($ID, $time, $status);
        }else{
            throw new Exception($this->text->AccountNotExist());
        }
    }

    /**
     * @param int $id
     * @param string $time
     * @param string $name
     * @return void
     */
    public function updateAccountName(int $id, string $time, string $name){
        $UpdateAccount = Account::where($this->text->getId(), $id)->first();
        $UpdateAccount->name = $name;
        $UpdateAccount->updated_at = $time;
        $UpdateAccount->save();
    }

    /**
     * @param int $id
     * @param string $time
     * @return void
     */
    public function updateAccountAt(int $id, string $time){
        $UpdateAccount = Account::where($this->text->getId(), $id)->first();
        $UpdateAccount->updated_at = $time;
        $UpdateAccount->save();
    }

    /**
     * @param int $id
     * @param string $time
     * @param bool $status
     * @return void
     */
    public function updateAccountLoginStatus(int $id, string $time, bool $status){
        $UpdateAccountLogin = AccountLogin::where($this->text->getIdAccount(), $id)->first();
        $UpdateAccountLogin->updated_at = $time;
        $UpdateAccountLogin->status = $status;
        $UpdateAccountLogin->save();
    }

    /**
     * @param int $id
     * @param string $time
     * @param string $password
     * @return void
     */
    public function updateAccountLoginPassword(int $id, string $time, string $password){
        $UpdateAccountLogin = AccountLogin::where($this->text->getIdAccount(), $id)->first();
        $UpdateAccountLogin->updated_at = $time;
        $UpdateAccountLogin->password = $password;
        $UpdateAccountLogin->save();
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
     * @param int $idAccount
     * @param array $params
     * @return bool
     */
    public function changeStatus(int $idAccount, array $params){
        $account = $this->getAccountById($idAccount);
        if (!is_null($params[$this->text->getAccount()])){
            $this->changeStatusAccount($account, $params[$this->text->getAccount()]);
        }
        return true;
    }

    /**
     * @param Account $account
     * @param array $cuenta
     * @return void
     */
    public function changeStatusAccount(Account $account, array $cuenta){
        try {
            if ($account != null) {
                $time= $this->date->getFullDate();

                $this->updateAccountAt($account->id, $time);
                $status = $cuenta[$this->text->getStatus()];
                if ($status == $this->status->getDisable()){
                    $data = array($this->text->getIdAccount() => $account->id);
                    $this->Sockets->sendQueryPost($this->text->getDisableAccount(), $data);
                }

                $this->updateAccountLoginStatus($account->id, $time, $status);
            }else{
                throw new Exception($this->text->AccountNotExist());
            }
        } catch (\Throwable $th) {
            throw new Exception($th->getMessage());
        }
    }

    /**
     * @param int $idAccount
     * @param array $params
     * @return bool
     */
    public function updatePasswordAccount(int $idAccount, array $params){
        $account = $this->getAccountById($idAccount);
        if (!is_null($params[$this->text->getAccount()])){
            $this->changePassword($account, $params[$this->text->getAccount()]);
        }
        return true;
    }

    /**
     * @param int $idAccount
     * @param array $params
     * @return bool
     */
    public function updateAccount(int $idAccount, array $params){
        $account = $this->getAccountById($idAccount);
        if (!is_null($params[$this->text->getAccount()])){
            $this->changeAccountDate($account, $params[$this->text->getAccount()]);
        }
        if (!is_null($params[$this->text->getRol()])){
            $this->updateRolAccount($account, $params[$this->text->getRol()]);
        }
        return true;
    }
    
    /**
     * @param Account $account
     * @param array $rol
     * @return void
     */
    public function updateRolAccount(Account $account, array $rol){
        try {
            if (is_array($rol)){
                $this->clearAllRolsAccount($account->id);
                $this->assingRol($account->id, $rol);
            }
        } catch (\Throwable $th) {
            throw new Exception($th->getMessage());
        }
    }

    /**
     * @param int $idAccount
     * @param array $rols
     * @return void
     */
    public function assingRol(int $idAccount, array $rols){
        try {
            foreach ($rols as $key => $rol) {
                $this->setRolAccount($idAccount, $rol);
            }
        } catch (\Throwable $th) {
            throw new Exception($th->getMessage());
        }
    }

    /**
     * @param int $idAccount
     * @param int $idRol
     * @return void
     */
    public function setRolAccount(int $idAccount, int $idRol){
        try {
            $RolAccount = new RolAccount();
            $RolAccount->id_rol = $idRol;
            $RolAccount->id_account = $idAccount;
            $RolAccount->save();
            return true;
        } catch (Exception $th) {
            throw new Exception($th->getMessage());
        }
    }

    /**
     * @param int $accountId
     * @return bool
     */
    public function clearAllRolsAccount(int $accountId){
        return RolAccount::where($this->text->getIdAccount(), $accountId)->delete();
    }
    
    /**
     * @param Account $account
     * @param array $cuenta
     * @return void
     */
    public function changeAccountDate(Account $account, array $cuenta){
        try {
            if ($account != null) {
                $time = $this->date->getFullDate();
                $this->updateAccountName($account->id, $cuenta[$this->text->getName()], $time);
            }else{
                throw new Exception($this->text->AccountNotExist());
            }
        } catch (\Throwable $th) {
            throw new Exception($th->getMessage());
        }
    }

    /**
     * @param Account $account
     * @param array $cuenta
     * @return void
     */
    public function changePassword(Account $account, array $cuenta){
        try {
            if ($account != null) {
                $time = $this->date->getFullDate();
                $this->updateAccountAt($account->id, $time);
                $this->updateAccountLoginPassword($account->id, $time, $this->encriptionPawd($cuenta[$this->text->getPassword()]));
            }else{
                throw new Exception($this->text->AccountNotExist());
            }
        } catch (\Throwable $th) {
            throw new Exception($th->getMessage());
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
     * @return Account|null
     */
    public function getAccountByPublic(string $value){
        $this->tokenAccess = new TokenAccess($value);
        $Account = Account::where($this->text->getToken(), $this->tokenAccess->getToken())->first();
        if (!$Account) {
            return null;
        }
        return $Account;
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
    public function getDelimitations(Partner $partner){
        $delimitation = array();
        $municipios = array();
        foreach($partner->Stores as $key => $store){
            $Store = $store->Store;
            $Delimitations = $Store->Delimitations;
            foreach ($Delimitations as $key2 => $Delimitation){
                $Localization = $Delimitation->Localization;
                $delimitation[] = array($this->text->getLatitude() => floatval($Localization->latitud), $this->text->getLongitude() => floatval($Localization->longitud));
                $municipios[$Delimitation->id_municipality_pos][] = array($this->text->getLatitude() => floatval($Localization->latitud), $this->text->getLongitude() => floatval($Localization->longitud));
            }
        }
        return array("delimitation" => $delimitation,"municipios" => $this->convertMunicipios($municipios));
    }

    /**
     * @param array $municipios
     * @return array
     */
    private function convertMunicipios(array $municipios){
        $data = array();
        foreach($municipios as $key => $municipio){
            $data[] = $municipio;
        }
        return $data;
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
        return $this->validateAccountLogin($request->all()[$this->text->getUsername()], $request->all()[$this->text->getPassword()], $request->ip());
    }

    /**
     * @param string $username
     * @param string $password
     * @param string $ip
     * @return array
     */
    private function validateAccountLogin(string $username, string $password, string $ip){
        $arrayUser = explode ($this->text->getArroba(), $username);
        if (count($arrayUser) == 2) {
            if ($this->partner->issetDomain($arrayUser[0])) {
                $response = $this->getByUsernameLogin($arrayUser[1]);
                if ($response != null) {
                    if ($response[0][$this->text->getStatus()] == 1) {
                        if ($response[0][$this->text->getPassword()] == $this->encriptionPawd($password)) {
                            $Account = $this->byId($response[0][$this->text->getIdAccount()]);
                            if ($Account != null) {
                                $api_ip = new Ip($ip);
                                $this->setPartnerSession(
                                    $Account->accountPartner->id_partner,
                                    $this->setSession($api_ip->validIp(), $this->addressApi->createGeo($api_ip->getGeo()))
                                );
                                return $this->text->messageLogin(true, 0, $Account->token);
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

    /**
     * @param int|null $id_ip
     * @param int|null $id_localization
     * @return int|null
     */
    public function setSession(int|null $id_ip, int|null $id_localization){
        try {
            $Session = new Session();
            $Session->date = $this->date->getFullDate();
            $Session->id_ip = $id_ip;
            $Session->id_localization = $id_localization;
            $Session->save();
            return $Session->id;
        } catch (Exception $th) {
            return null;
        }
    }

    /**
     * @param int|null $id_partner
     * @param int|null $id_session
     * @return int|null
     */
    public function setPartnerSession(int|null $id_partner, int|null $id_session){
        try {
            $PartnerSession = new PartnerSession();
            $PartnerSession->id_partner = $id_partner;
            $PartnerSession->id_session = $id_session;
            $PartnerSession->status = $this->status->getEnable();
            $PartnerSession->save();
            return $PartnerSession->id;
        } catch (Exception $th) {
            return null;
        }
    }
}

?>
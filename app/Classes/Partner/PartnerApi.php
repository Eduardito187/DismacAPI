<?php

namespace App\Classes\Partner;

use Illuminate\Support\Facades\Log;
use App\Models\Partner;
use App\Models\AccountPartner;
use App\Models\RolAccount;
use App\Classes\Helper\Date;
use App\Classes\Helper\Status;
use Illuminate\Support\Facades\Hash;
use App\Classes\Helper\Text;
use App\Models\Account;
use App\Models\Category;
use App\Models\Product;
use App\Models\SocialPartner;
use App\Models\StorePartner;
use App\Classes\Picture\PictureApi;
use App\Models\ProductPriceStore;
use App\Models\Store;
use Exception;

class PartnerApi{
    CONST HISTOY_LAST = 8;
    /**
     * @var Partner
     */
    protected $partner;
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
     * @var PictureApi
     */
    protected $pictureApi;

    public function __construct() {
        $this->date       = new Date();
        $this->status     = new Status();
        $this->text       = new Text();
        $this->pictureApi = new PictureApi();
    }

    /**
     * @param array $partner
     * @param int $id_address
     * @return void
     */
    public function create(array $partner, int $id_address){
        $this->createPartner($partner, $id_address);
        $this->setByDomain($partner[$this->text->getDomain()]);
    }

    /**
     * @return int
     */
    public function getPartnerId(){
        return $this->partner->id;
    }

    /**
     * @param string $email
     * @return bool
     */
    public function validateEmail(string $email){
        $Emails = Partner::select($this->text->getId())->where($this->text->getEmail(), $email)->get()->toArray();
        if (count($Emails) > 0) {
            return false;
        }else{
            return true;
        }
    }

    /**
     * @param string $domain
     */
    private function validateDomain(string $domain){
        $Partner = Partner::select($this->text->getId())->where($this->text->getDomain(), $domain)->get()->toArray();
        if (count($Partner) > 0) {
            throw new Exception($this->text->getPartnerAlready());
        }
    }

    /**
     * @param string $domain
     * @return bool
     */
    public function issetDomain(string $domain){
        $Partner = Partner::select($this->text->getId())->where($this->text->getDomain(), strtoupper($domain))->get()->toArray();
        if (count($Partner) > 0) {
            return  true;
        }else{
            return false;
        }
    }

    /**
     * @param int $id_partner
     * @param int $id_account
     * @return int|null
     */
    public function getAccountPartner(int $id_partner, int $id_account){
        $AccountPartner = AccountPartner::select($this->text->getIdPartner())->where($this->text->getIdPartner(), $id_partner)->
        where($this->text->getIdAccount(), $id_account)->get()->toArray();
        if (count($AccountPartner) > 0) {
            return  $AccountPartner[0][$this->text->getIdPartner()];
        }else{
            return null;
        }
    }

    /**
     * @param int $id_partner
     * @param int $id_account
     * @return bool
     */
    public function setAccountDomain(int $id_partner, int $id_account){
        try {
            if (is_null($this->getAccountPartner($id_partner, $id_account))) {
                $Partner = new AccountPartner();
                $Partner->id_partner = $id_partner;
                $Partner->id_account = $id_account;
                $Partner->status = $this->status->getEnable();
                $Partner->save();
                return true;
            }else{
                throw new Exception($this->text->getAccountRegister());
            }
        } catch (Exception $th) {
            throw new Exception($th->getMessage());
        }
    }

    /**
     * @param int $id_account
     * @return bool
     */
    public function setSuperAdminAccount(int $id_account){
        try {
            $RolAccount = new RolAccount();
            $RolAccount->id_rol = 1;
            $RolAccount->id_account = $id_account;
            $RolAccount->save();
            return true;
        } catch (Exception $th) {
            throw new Exception($th->getMessage());
        }
    }

    /**
     * @param string $domain
     * @return void
     */
    private function setByDomain($domain){
        $this->partner = Partner::where($this->text->getDomain(), $domain)->first();
    }

    /**
     * @param array $partner
     * @param int $id_address
     * @return bool
     */
    public function createPartner(array $partner, int $id_address){
        try {
            $this->validateDomain($partner[$this->text->getDomain()]);
            $Partner = new Partner();
            $Partner->name = $partner[$this->text->getName()];
            $Partner->domain = $partner[$this->text->getDomain()];
            $Partner->email = $partner[$this->text->getEmail()];
            $Partner->token = $this->getToken($partner[$this->text->getDomain()]);
            $Partner->nit = $partner[$this->text->getNit()];
            $Partner->razon_social = $partner[$this->text->getRazonSocial()];
            $Partner->status = $this->status->getDisable();
            $Partner->legal_representative = $partner[$this->text->getLegalRepresentative()];
            $Partner->picture_profile = null;
            $Partner->picture_front = null;
            $Partner->id_address = $id_address;
            $Partner->created_at = $this->date->getFullDate();
            $Partner->updated_at = null;
            $Partner->save();
            return true;
        } catch (Exception $th) {
            return false;
        }
    }
    
    /**
     * @param Partner $partner
     * @return int
     */
    public function getCountProduct(Partner $partner){
        return $this->countProductPartner($partner->id);
    }
    
    /**
     * @param int $id_partner
     * @return int
     */
    public function countProductPartner(int $id_partner){
        return Product::select($this->text->getId())->where($this->text->getIdPartner(), $id_partner)->distinct()->count($this->text->getId());
    }
    
    /**
     * @param Partner $partner
     * @return int
     */
    public function getCountStorePartner(Partner $partner){
        return $this->countStorePartner($partner->id);
    }

    /**
     * @param Partner $partner
     * @return int
     */
    public function countSocialNetworkPartner(Partner $partner){
        return $this->countSocialPartner($partner->id);
    }

    /**
     * @param Partner $partner
     * @return array
     */
    public function getLastHistoryCategory(Partner $partner){
        try {
            $response = array();
            $response = $this->getCategoryLastModify($partner->id);
            if (count($response) == 0) {
                $response = $this->getCategoryLastCreate($partner->id);
            }
            return $response;
        } catch (\Throwable $th) {
            //
        }
    }

    /**
     * @param Partner $partner
     * @return array
     */
    public function getLastHistoryProducts(Partner $partner){
        try {
            $response = array();
            $response = $this->getProductLastModify($partner->id);
            if (count($response) == 0) {
                $response = $this->getProductLastCreate($partner->id);
            }
            return $response;
        } catch (\Throwable $th) {
            //
        }
    }

    /**
     * @return Store[]
     */
    public function getAllStoreEntity(){
        return Store::all();
    }

    /**
     * @param Partner $partner
     * @return float
     */
    public function valuePartner(Partner $partner){
        $Stores = $this->getAllStoreEntity();
        $Products = $this->listProductPartnerStock($partner->id);
        Log::debug(count($Products));
        $value = 0;
        try {
            $value = $this->getPricesProducts($Stores, $Products);
        } catch (\Throwable $th) {
            //
        }
        return $value;
    }

    /**
     * @param int $id_partner
     * @return Product[]
     */
    public function listProductPartnerStock(int $id_partner){
        return Product::select("id", "stock")->where($this->text->getIdPartner(), $id_partner)->where($this->text->getStock(), ">", 0)->get();
    }

    /**
     * @param int $id_partner
     * @param int $id_product
     */
    public function calculoPriceProduct(int $id_partner, int $id_product){
        $ProductPriceStore = ProductPriceStore::where($this->text->getIdPartner(), $id_partner)->where($this->text->getIdProduct(), $id_product)->first();
        if (!$ProductPriceStore) {
            return 0;
        }else{
            $Price = $ProductPriceStore->Price;
            if (!$Price) {
                return 0;
            }else{
                if (is_null($Price->special_price)) {
                    return floatval($Price->price);
                }else{
                    return floatval($Price->special_price);
                }
            }
        }
    }

    public function getPricesProducts($stores, $products){
        $value = 0;
        foreach ($stores as $s => $store) {
            foreach ($products as $p => $product) {
                $value += $this->calculoPriceProduct($store->id, $product->id) * $product->stock;
            }
        }
        return $value;
    }

    /**
     * @param Product[] $Products
     * @return array
     */
    public function convertListProductToArray($Products){
        $response = array();
        foreach ($Products as $key => $Product) {
            $response[] = $this->convertProductToArray($Product);
        }
        return $response;
    }

    /**
     * @param Product $Product
     * @return array
     */
    public function convertProductToArray(Product $Product){
        return array(
            $this->text->getId() => $Product->id,
            $this->text->getSku() => $Product->sku,
            $this->text->getImage() =>$this->pictureApi->productFirstPicture($Product->id)
        );
    }

    /**
     * @param int $id_partner
     * @return array
     */
    public function getProductLastModify(int $id_partner){
        $Product = Product::where($this->text->getIdPartner(), $id_partner)->orderBy($this->text->getUpdated(), 'desc')->offset(0)->limit(self::HISTOY_LAST)->get();
        return $this->convertListProductToArray($Product);
    }

    /**
     * @param int $id_partner
     * @return array
     */
    public function getProductLastCreate(int $id_partner){
        $Product = Product::where($this->text->getIdPartner(), $id_partner)->orderBy($this->text->getCreated(), 'desc')->offset(0)->limit(self::HISTOY_LAST)->get();
        return $this->convertListProductToArray($Product);
    }

    /**
     * @param int $id_partner
     * @return array
     */
    public function getCategoryLastModify(int $id_partner){
        $Category = Category::where($this->text->getIdPartner(), $id_partner)->orderBy($this->text->getUpdated(), 'desc')->offset(0)->limit(self::HISTOY_LAST)->get();
        return $this->convertListCategoryToArray($Category);
    }

    /**
     * @param int $id_partner
     * @return array
     */
    public function getCategoryLastCreate(int $id_partner){
        $Category = Category::where($this->text->getIdPartner(), $id_partner)->orderBy($this->text->getCreated(), 'desc')->offset(0)->limit(self::HISTOY_LAST)->get();
        return $this->convertListCategoryToArray($Category);
    }

    /**
     * @param Category[] $Categorys
     * @return array
     */
    public function convertListCategoryToArray($Categorys){
        $response = array();
        foreach ($Categorys as $key => $Category) {
            $response[] = $this->convertCategoryToArray($Category);
        }
        return $response;
    }

    /**
     * @param Category $Category
     * @return array
     */
    public function convertCategoryToArray(Category $Category){
        return array(
            $this->text->getId() => $Category->id,
            $this->text->getName() => $Category->name,
            $this->text->getImage() => $this->categoryFirstPicture($Category)
        );
    }
    
    /**
     * @param Category $Category
     * @return string
     */
    public function categoryFirstPicture(Category $Category){
        $Picture = null;
        if ($Category->id_info_category != null) {
            if ($Category->CatInfo->id_picture != null){
                $Picture = $Category->CatInfo->Picture;
            }
        }
        if (!$Picture) {
            $Picture = $this->pictureApi->getImageById($this->pictureApi::DEFAULT_IMAGE);
        }
        return $this->pictureApi->getPublicUrlImage($Picture);
    }

    /**
     * @param int $id_partner
     * @return int
     */
    public function countSocialPartner(int $id_partner){
        return SocialPartner::select($this->text->getSocialNetwork())->where($this->text->getIdPartner(), $id_partner)->distinct()->count($this->text->getSocialNetwork());
    }
    
    /**
     * @param int $id_partner
     * @return int
     */
    public function countStorePartner(int $id_partner){
        return StorePartner::select($this->text->getIdStore())->where($this->text->getIdPartner(), $id_partner)->distinct()->count($this->text->getIdStore());
    }

    /**
     * @param Account $Account
     * @param array $stores_id
     */
    public function setStores(Account $Account, array $stores_id){
        $id_partner = $Account->accountPartner->id_partner;
        $this->clearStoresPartner($id_partner);
        $this->setMultipleStorePartner($id_partner, $stores_id);
    }

    /**
     * @param int $id_partner
     * @return void
     */
    public function clearStoresPartner(int $id_partner){
        StorePartner::where($this->text->getIdPartner(), $id_partner)->delete();
    }

    /**
     * @param int $id_partner
     * @param array $stores_id
     * @return void
     */
    public function setMultipleStorePartner(int $id_partner, array $stores_id){
        foreach ($stores_id as $key => $store) {
            $this->setStorePartner($store, $id_partner);
        }
    }

    /**
     * @param int $id_store
     * @param int $id_partner
     * @return bool
     */
    public function setStorePartner(int $id_store, int $id_partner){
        try {
            $StorePartner = new StorePartner();
            $StorePartner->id_store = $id_store;
            $StorePartner->id_partner = $id_partner;
            $StorePartner->save();
            return true;
        } catch (Exception $th) {
            return false;
        }
    }
    
    /**
     * @param Partner $partner
     * @return int
     */
    public function getCountWareHouse(Partner $partner){
        return $this->countWarehousesPartner($partner->id);
    }
    
    /**
     * @param int $id_partner
     * @return int
     */
    public function countWarehousesPartner(int $id_partner){
        return Product::select($this->text->getTablePWIdWarehouse())->where($this->text->getIdPartner(), $id_partner)
        ->join($this->text->getTablePW(), $this->text->getTablePWProductId(), $this->text->getEquals(), $this->text->getPwhIdProduct())->distinct()->count($this->text->getPwhIdWarehouse());
    }

    /**
     * @param string $domain
     * @return string
     */
    private function getToken(string $domain){
        return Hash::make($domain, [
            $this->text->getRounds() => 12,
        ]);
    }
}

?>
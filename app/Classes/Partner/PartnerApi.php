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
use App\Models\Campaign;
use App\Models\ProductPriceStore;
use App\Models\Store;
use App\Classes\Address\AddressApi;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\TipoDocumento;
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
    /**
     * @var AddressApi
     */
    protected $addressApi;

    public function __construct() {
        $this->date       = new Date();
        $this->status     = new Status();
        $this->text       = new Text();
        $this->pictureApi = new PictureApi();
        $this->addressApi = new AddressApi();
    }

    public function createOrder($request){
        if ($this->existTokenPartner($request[$this->text->getTokenPartner()])) {
            $Partner = $this->getById($request[$this->text->getIdPartnerApi()]);
            if ($this->validateDetailProforma($request[$this->text->getTotal()], $request[$this->text->getSubTotal()], $request[$this->text->getTotalDescuento()], $request[$this->text->getCantidadProductos()], $request[$this->text->getDetalleOrden()])){
                Log::debug("###1###");
                $idAddress = $this->verifyShippingAddress($request[$this->text->getDatosClientes()]);
                Log::debug("###2###");
                $idCustomer = $this->verifyCustomer($request[$this->text->getDatosClientes()]);
                Log::debug("###3###");
                $this->saveCustomerAddress($idCustomer, $idAddress);
                Log::debug("###4###");
            }
        }else{
            throw new Exception($this->text->getPartnerTokenNone());
        }
    }

    /**
     * @param int $customer
     * @param int $address
     */
    public function saveCustomerAddress(int $customer, int $address){
        if (!$this->getCustomerAddress($customer, $address)){
            $CustomerAddress = new CustomerAddress();
            $CustomerAddress->customer = $customer;
            $CustomerAddress->address = $address;
            $CustomerAddress->save();
        }
    }

    /**
     * @param int $customer
     * @param int $address
     * @return CustomerAddress
     */
    public function getCustomerAddress(int $customer, int $address){
        return CustomerAddress::where($this->text->getCustomer(), $customer)->where($this->text->getAddress(), $address)->first();
    }

    /**
     * @param array $clientAddress
     * @return int
     */
    public function verifyShippingAddress(array $clientAddress){
        $this->addressApi->createAddressExtra($this->convertAddressExtra($clientAddress[$this->text->getDireccion()], $clientAddress[$this->text->getDireccionExtra()]));
        $ExtraAddress = $this->addressApi->getAddressExtra($this->convertAddressExtra($clientAddress[$this->text->getDireccion()], $clientAddress[$this->text->getDireccionExtra()]));
        $this->addressApi->createGeo($clientAddress[$this->text->getLocalizacion()]);
        $GEO = $this->addressApi->getLocalization($clientAddress[$this->text->getLocalizacion()]);
        Log::debug("###FIN 1###");
        $this->addressApi->createAddress($this->convertAddress($clientAddress[$this->text->getPais()], $clientAddress[$this->text->getCiudad()], $clientAddress[$this->text->getMunicipio()]), $ExtraAddress, $GEO);
        Log::debug("###FIN###");
        return $this->addressApi->getAddressId();
    }

    /**
     * @param array $clientAddress
     * @return int
     */
    public function verifyCustomer(array $clientAddress){
        Log::debug("###01###");
        $TipoDocumento = $this->getTipoDocumentoId($clientAddress[$this->text->getTipoDocumento()]);
        Log::debug("###02###");
        $customer = $this->validateCustomer(
            $clientAddress[$this->text->getNombreApi()],
            $clientAddress[$this->text->getApellidoPaternoApi()],
            $clientAddress[$this->text->getApellidoMaternoApi()],
            $clientAddress[$this->text->getEmailApi()],
            $clientAddress[$this->text->getNumTelefonoApi()],
            $TipoDocumento,
            $clientAddress[$this->text->getNumDocumentoApi()]
        );
        if (!$customer) {
            $Customer = new Customer();
            $Customer->nombre = $clientAddress[$this->text->getNombreApi()];
            $Customer->apellido_paterno = $clientAddress[$this->text->getApellidoPaternoApi()];
            $Customer->apellido_materno = $clientAddress[$this->text->getApellidoMaternoApi()];
            $Customer->email = $clientAddress[$this->text->getEmailApi()];
            $Customer->num_telefono = $clientAddress[$this->text->getNumTelefonoApi()];
            $Customer->tipo_documento = $TipoDocumento;
            $Customer->num_documento = $clientAddress[$this->text->getNumDocumentoApi()];
            $Customer->created_at = $this->date->getFullDate();
            $Customer->updated_at = null;
            $Customer->save();
            Log::debug("###11###");
            return $Customer->id;
        }else{
            Log::debug("###12###");
            return $customer->id;
        }
    }

    /**
     * @return Customer
     */
    public function validateCustomer(string $nombre, string $apellido_paterno, string $apellido_materno, string $email, string $num_telefono, int $tipo_documento, string $num_documento){
        return Customer::where($this->text->getNombre(), $nombre)->where($this->text->getColApellidoPaterno(), $apellido_paterno)->
        where($this->text->getColApellidoMaterno(), $apellido_materno)->where($this->text->getEmail(), $email)->
        where($this->text->getColNumTelf(), $num_telefono)->where($this->text->getColTipoDoc(), $tipo_documento)->
        where($this->text->getColNumDoc(), $num_documento)->first();
    }

    /**
     * @param string $TipoDoc
     * @return int
     */
    public function getTipoDocumentoId(string $TipoDoc){
        $TipoDoc = TipoDocumento::where($this->text->getType(), $TipoDoc)->first();
        if (!$TipoDoc) {
            throw new Exception($this->text->getErrorTipoDocumento());
        }
        return $TipoDoc->id;
    }

    /**
     * @param string $Pais
     * @param string $Ciudad
     * @param string $Municipio
     * @return array
     */
    public function convertAddress($Pais, $Ciudad, $Municipio){
        return [
            $this->text->getIdCountry() => $this->addressApi->getCountryByName($Pais),
            $this->text->getIdMunicipality() => $this->addressApi->getCityByName($Ciudad),
            $this->text->getIdCity() => $this->addressApi->getMunicipalityByName($Municipio)
        ];
    }

    /**
     * @param string $Direccion
     * @param string $DireccionExtra
     * @return array
     */
    private function convertAddressExtra(string $Direccion, string $DireccionExtra){
        return [
            $this->text->getAddress() => $Direccion,
            $this->text->getExtra() => $DireccionExtra
        ];
    }
    
    /**
     * @param float $Total
     * @param float $SubTotal
     * @param float $TotalDescuento
     * @param int $CantidadProductos
     * @param array $DetailProforma
     * @return bool
     */
    public function validateDetailProforma(float $Total, float $SubTotal, float $TotalDescuento, int $CantidadProductos, array $DetailProforma){
        if ($CantidadProductos != $this->verifyQtyDetailProforma($DetailProforma)) {
            throw new Exception($this->text->getErrorQtyProforma());
        }
        if ($SubTotal != $this->verifySubTotal($DetailProforma)) {
            throw new Exception($this->text->getErrorSubTotalProforma());
        }
        if ($Total != $this->verifyTotal($DetailProforma)) {
            throw new Exception($this->text->getErrorMontoProforma());
        }
        if ($TotalDescuento != $this->verifyDiscountProforma($DetailProforma)) {
            throw new Exception($this->text->getErrorTotalProforma());
        }
        return true;
    }

    /**
     * @param array $DetailProforma
     * @return float
     */
    public function verifyDiscountProforma(array $DetailProforma){
        $TotalDescuento = 0;
        foreach ($DetailProforma as $key => $Detail) {
            if ($Detail[$this->text->getTotalDescuento()] != $this->detailInfoProforma($Detail[$this->text->getDescuentos()])){
                throw new Exception($this->text->getErrorQtyProforma());
            }else{
                $TotalDescuento += $Detail[$this->text->getTotalDescuento()];
            }
        }
        return $TotalDescuento;
    }

    /**
     * @param array $DetailDiscount
     * @return float
     */
    public function detailInfoProforma(array $DetailDiscount){
        $Monto = 0;
        foreach ($DetailDiscount as $key => $Detail) {
            $Monto += $Detail[$this->text->getMontoApi()];
        }
        return $Monto;
    }

    /**
     * @param array $DetailProforma
     * @return float
     */
    public function verifyQtyDetailProforma(array $DetailProforma){
        $qty = 0;
        foreach ($DetailProforma as $key => $Detail) {
            $qty += $Detail[$this->text->getQty()];
        }
        return $qty;
    }

    /**
     * @param array $DetailProforma
     * @return float
     */
    public function verifySubTotal(array $DetailProforma){
        $SubTotal = 0;
        foreach ($DetailProforma as $key => $Detail) {
            $sub = $Detail[$this->text->getQty()] * $Detail[$this->text->getPrecioUnitario()];
            if ($Detail[$this->text->getSubTotal()] != $sub){
                throw new Exception($this->text->getErrorSubTotalProforma());
            }else{
                $SubTotal += $Detail[$this->text->getQty()] * $Detail[$this->text->getPrecioUnitario()];
            }
        }
        return $SubTotal;
    }

    /**
     * @param array $DetailProforma
     * @return float
     */
    public function verifyTotal(array $DetailProforma){
        $Total = 0;
        foreach ($DetailProforma as $key => $Detail) {
            $Total += $Detail[$this->text->getTotal()];
        }
        return $Total;
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
     * @param string $token
     * @return bool
     */
    public function existTokenPartner(string $token){
        $partner = Partner::select($this->text->getToken())->where($this->text->getToken(), $token)->get()->toArray();
        if (count($partner) == 0) {
            return false;
        }else{
            return true;
        }
    }

    /**
     * @param int $id
     * @return Partner
     */
    public function getById(int $id){
        $partner = Partner::where($this->text->getId(), $id)->first();
        if (!$partner) {
            throw new Exception($this->text->getPartnerNone());
        }
        return $partner;
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
     * @return int
     */
    public function countCampaignsPartner(Partner $partner){
        return $this->countCampaignsSocialPartner($partner->id);
    }

    /**
     * @param int $id_partner
     * @return int
     */
    public function countCampaignsSocialPartner(int $id_partner){
        return Campaign::select($this->text->getSocialNetwork())->where($this->text->getIdPartner(), $id_partner)->distinct()->count($this->text->getSocialNetwork());
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
     * @return float|string
     */
    public function valuePartner(Partner $partner){
        $Stores = $this->getAllStoreEntity();
        $Products = $this->listProductPartnerStock($partner->id);
        $value = 0;
        try {
            $value = $this->getPricesProducts($Stores, $Products);
        } catch (\Throwable $th) {
            //
        }
        return $this->text->convertNumberFormat($value);
    }

    /**
     * @param int $id_partner
     * @return Product[]
     */
    public function listProductPartnerStock(int $id_partner){
        return Product::select($this->text->getId(), $this->text->getStock())->where($this->text->getIdPartner(), $id_partner)->where($this->text->getStock(), $this->text->getSymbolMayor(), 0)->get();
    }

    /**
     * @param int $id_store
     * @param int $id_product
     */
    public function calculoPriceProduct(int $id_store, int $id_product){
        $ProductPriceStore = ProductPriceStore::where($this->text->getIdStore(), $id_store)->where($this->text->getIdProduct(), $id_product)->first();
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
            $this->text->getName() => $Product->name,
            $this->text->getSku() => $Product->sku,
            $this->text->getImage() =>$this->pictureApi->productFirstPicture($Product->id)
        );
    }

    /**
     * @param int $id_partner
     * @return array
     */
    public function getProductLastModify(int $id_partner){
        $Product = Product::where($this->text->getIdPartner(), $id_partner)->orderBy($this->text->getUpdated(), $this->text->getOrderDesc())->offset(0)->limit(self::HISTOY_LAST)->get();
        return $this->convertListProductToArray($Product);
    }

    /**
     * @param int $id_partner
     * @return array
     */
    public function getProductLastCreate(int $id_partner){
        $Product = Product::where($this->text->getIdPartner(), $id_partner)->orderBy($this->text->getCreated(), $this->text->getOrderDesc())->offset(0)->limit(self::HISTOY_LAST)->get();
        return $this->convertListProductToArray($Product);
    }

    /**
     * @param int $id_partner
     * @return array
     */
    public function getCategoryLastModify(int $id_partner){
        $Category = Category::where($this->text->getIdPartner(), $id_partner)->orderBy($this->text->getUpdated(), $this->text->getOrderDesc())->offset(0)->limit(self::HISTOY_LAST)->get();
        return $this->convertListCategoryToArray($Category);
    }

    /**
     * @param int $id_partner
     * @return array
     */
    public function getCategoryLastCreate(int $id_partner){
        $Category = Category::where($this->text->getIdPartner(), $id_partner)->orderBy($this->text->getCreated(), $this->text->getOrderDesc())->offset(0)->limit(self::HISTOY_LAST)->get();
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
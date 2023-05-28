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
use App\Models\Sales;
use App\Models\SalesDetails;
use App\Models\ShippingAddress;
use App\Models\StatusOrder;
use App\Models\TipoDocumento;
use App\Models\Coupon;
use App\Models\ProductWarehouse;
use App\Models\SalesCoupon;
use App\Models\Warehouse;
use Exception;

class PartnerApi{
    CONST HISTOY_LAST = 8;
    CONST PENDIENTE = "PENDIENTE";
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
    /**
     * @var int
     */
    protected $lastIdOrder = null;
    /**
     * @var array
     */
    protected $listDiscount = [];
    /**
     * @var array
     */
    protected $validCoupons = [];

    public function __construct() {
        $this->date       = new Date();
        $this->status     = new Status();
        $this->text       = new Text();
        $this->pictureApi = new PictureApi();
        $this->addressApi = new AddressApi();
    }

    /**
     * @param array $request
     * @param string $ip
     * @return bool
     */
    public function createOrder(array $request, string $ip){
        if ($this->existTokenPartner($request[$this->text->getTokenPartner()])) {
            if (!$this->verifyOrder($request)) {
                $Partner = $this->getById($request[$this->text->getIdPartnerApi()]);
                if ($this->validateDetailProforma($request[$this->text->getDatosClientes()], $request[$this->text->getTotal()], $request[$this->text->getSubTotal()], $request[$this->text->getTotalDescuento()], $request[$this->text->getCantidadProductos()], $request[$this->text->getDetalleOrden()], $Partner)){
                    $idAddress = $this->verifyShippingAddress($request[$this->text->getDatosClientes()]);
                    $idCustomer = $this->verifyCustomer($request[$this->text->getDatosClientes()]);
                    $this->validateCoupons($idCustomer);
                    $this->saveCustomerAddress($idCustomer, $idAddress);
                    $this->registerOrder($Partner, $request, $ip);
                    if (!is_null($this->lastIdOrder)) {
                        $this->createShippingAddress($idCustomer, $idAddress, $this->lastIdOrder);
                        $this->registerDetailsSale($idCustomer, $Partner, $request[$this->text->getDetalleOrden()], $request[$this->text->getDatosClientes()]);
                    }
                }
                return true;
            }else{
                throw new Exception($this->text->getExistSale());
            }
        }else{
            throw new Exception($this->text->getPartnerTokenNone());
        }
    }

    /**
     * @param string $sku
     * @param int $id_Partner
     * @return Product
     */
    public function getProductBySkuPartner(string $sku, int $id_Partner){
        $product = Product::where($this->text->getSku(), $sku)->where($this->text->getIdPartner(), $id_Partner)->first();
        if (!$product) {
            throw new Exception($this->text->getNoneSku($sku));
        }
        return $product;
    }

    /**
     * @param array $request
     * @return Sales
     */
    public function verifyOrder(array $request){
        return Sales::where($this->text->getColumnNroFactura(), $request[$this->text->getNroFactura()])->
        where($this->text->getColumnNroProforma(), $request[$this->text->getNroProforma()])->
        where($this->text->getColumnNroControl(), $request[$this->text->getNroControl()])->first();
    }

    /**
     * @param int $customer
     */
    public function validateCoupons(int $customer){
        foreach ($this->listDiscount as $key => $discount) {
            $Coupon = $this->validateCouponCode($discount, $customer);
            $this->validCoupons[$Coupon->id] = $discount;
        }
    }

    /**
     * @param array $Detalle
     */
    public function createDetailSales(Partner $Partner, array $Detalle){
        try {
            $idProduct = $this->getProductBySkuPartner($Detalle[$this->text->getSkuApi()], $Partner->id)->id;
            $SalesDetails = new SalesDetails();
            $SalesDetails->sales = $this->lastIdOrder;
            $SalesDetails->product = $idProduct;
            $SalesDetails->qty = $Detalle[$this->text->getQty()];
            $SalesDetails->discount = $Detalle[$this->text->getTotalDescuento()];
            $SalesDetails->subtotal = $Detalle[$this->text->getSubTotal()];
            $SalesDetails->total = $Detalle[$this->text->getTotal()];
            $SalesDetails->created_at = $this->date->getFullDate();
            $SalesDetails->updated_at = null;
            $SalesDetails->save();
            return $idProduct;
        } catch (Exception $th) {
            throw new Exception($th->getMessage());
        }
    }

    /**
     * @param int $idCustomer
     * @param Partner $Partner
     * @param array $DetalleOrden
     */
    public function registerDetailsSale(int $idCustomer, Partner $Partner, array $DetalleOrden, array $DetalleCliente){
        $CiudadId = $this->addressApi->getStoreByName($DetalleCliente[$this->text->getStoreMagento()])->id;
        foreach ($DetalleOrden as $key => $Detalle) {
            $idProduct = $this->createDetailSales($Partner, $Detalle);
            $this->registerDiscountSale($idCustomer, $Partner->id, $Detalle[$this->text->getDescuentos()]);
            $this->validateStockSku($CiudadId, $idProduct, $Detalle[$this->text->getAlmacenApi()], $Detalle[$this->text->getQty()]);
        }
    }

    /**
     * @param int $idCity
     * @param int $id_product
     * @param string $almacen
     */
    public function validateStockSku(int $idCity, int $id_product, string $almacen, int $Qty) {
        $idAlmacen = $this->getWarehouseByAlmacen($almacen)->id;
        $ProductWarehouse = ProductWarehouse::where($this->text->getIdProduct(), $id_product)->where($this->text->getIdWarehouse(), $idAlmacen)->where($this->text->getIdStore(), $idCity)->first();
        if (!$ProductWarehouse) {
            throw new Exception($this->text->getWarehouseProductNone());
        }
        if ($ProductWarehouse->stock < $Qty) {
            throw new Exception($this->text->getStockNoneProduct());
        }
        ProductWarehouse::where($this->text->getIdProduct(), $id_product)->where($this->text->getIdWarehouse(), $idAlmacen)->where($this->text->getIdStore(), $idCity)->update([
            $this->text->getStock() => $ProductWarehouse->stock - $Qty,
            $this->text->getUpdated() => $this->date->getFullDate()
        ]);
    }

    /**
     * @param string $almacen
     * @return Warehouse
     */
    public function getWarehouseByAlmacen(string $almacen){
        $Warehouse = Warehouse::where($this->text->getAlmacen(), $almacen)->first();
        if (!$Warehouse){
            throw new Exception($this->text->getWarehouseNone());
        }
        return $Warehouse;
    }

    /**
     * @param int $idCustomer
     * @param int $idPartner
     * @param array $Descuentos
     */
    public function registerDiscountSale(int $idCustomer, int $idPartner, array $Descuentos){
        foreach ($Descuentos as $key => $Descuento) {
            $this->createSalesCoupon($idCustomer, $Descuento);
        }
    }

    /**
     * @param int $idCustomer
     * @param array $Descuento
     */
    public function createSalesCoupon(int $idCustomer, array $Descuento){
        try {
            $SalesCoupon = new SalesCoupon();
            $SalesCoupon->sales = $this->lastIdOrder;
            $SalesCoupon->coupon = $this->getCouponDetail($Descuento[$this->text->getCodigoDescuento()]);
            $SalesCoupon->customer = $idCustomer;
            $SalesCoupon->monto = $Descuento[$this->text->getMontoApi()];
            $SalesCoupon->percent = $Descuento[$this->text->getPorcentajeApi()];
            $SalesCoupon->created_at = $this->date->getFullDate();
            $SalesCoupon->updated_at = null;
            $SalesCoupon->save();
        } catch (Exception $th) {
            throw new Exception($th->getMessage());
        }
    }

    /**
     * @param string $cupon
     * @return int|null
     */
    public function getCouponDetail(string $cupon){
        foreach ($this->validCoupons as $key => $Coupon) {
            if ($cupon == $Coupon) {
                return $key;
            }
        }
        throw new Exception($this->text->getCouponNone());
        return null;
    }

    /**
     * @param string $Codigo
     * @param int $customer
     */
    public function validateCouponCode(string $Codigo, int $customer){
        $Coupon = Coupon::where($this->text->getCouponCode(), $Codigo)->first();
        if (!$Coupon) {
            throw new Exception($this->text->getCouponNone());
        }
        if ($Coupon->status == false) {
            throw new Exception($this->text->getCouponDisable());
        }
        if ($Coupon->limit_usage <= $this->verifyUsageCoupon($Coupon->id, $customer)){
            throw new Exception($this->text->getColumnLimitCoupon());
        }
        return $Coupon;
    }

    /**
     * @param int $customer
     * @param int $coupon
     */
    public function verifyUsageCoupon(int $coupon, int $customer){
        return intval(SalesCoupon::where($this->text->getCustomer(), $customer)->where($this->text->getColumnCoupon(), $coupon)->sum($this->text->getCustomer()));
    }

    /**
     * @param Partner $Partner
     * @param array $request
     * @param string $ip
     */
    public function registerOrder(Partner $Partner, array $request, string $ip){
        try {
            $Sales = new Sales();
            $Sales->id_partner = $Partner->id;
            $Sales->products = $request[$this->text->getCantidadProductos()];
            $Sales->status = $this->getStatusOrderId(self::PENDIENTE);
            $Sales->discount = $request[$this->text->getTotalDescuento()];
            $Sales->subtotal = $request[$this->text->getSubTotal()];
            $Sales->total = $request[$this->text->getTotal()];
            $Sales->nro_factura = $request[$this->text->getNroFactura()];
            $Sales->nro_proforma = $request[$this->text->getNroProforma()];
            $Sales->nro_control = $request[$this->text->getNroControl()];
            $Sales->ip_client = $ip;
            $Sales->created_at = $this->date->getFullDate();
            $Sales->updated_at = null;
            $Sales->save();
            $this->lastIdOrder = $Sales->id;
        } catch (Exception $th) {
            throw new Exception($th->getMessage());
        }
    }

    /**
     * @param int $customer
     * @param int $address
     * @param int $sale
     */
    public function createShippingAddress(int $customer, int $address, int $sale){
        try {
            $ShippingAddress = new ShippingAddress();
            $ShippingAddress->customer = $customer;
            $ShippingAddress->address = $address;
            $ShippingAddress->sale = $sale;
            $ShippingAddress->save();
        } catch (Exception $th) {
            throw new Exception($th->getMessage());
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
        $this->addressApi->createAddress($this->convertAddress($clientAddress[$this->text->getPais()], $clientAddress[$this->text->getCiudad()], $clientAddress[$this->text->getMunicipio()]), $ExtraAddress, $GEO);
        return $this->addressApi->getAddressId();
    }

    /**
     * @param array $clientAddress
     * @return int
     */
    public function verifyCustomer(array $clientAddress){
        $TipoDocumento = $this->getTipoDocumentoId($clientAddress[$this->text->getTipoDocumento()]);
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
            return $Customer->id;
        }else{
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
     * @param string $status
     * @return StatusOrder
     */
    public function getStatusOrderId(string $status){
        $StatusOrder = StatusOrder::where($this->text->getStatus(), $status)->first();
        if (!$StatusOrder) {
            throw new Exception($this->text->getStatusSaleNone());
        }
        return $StatusOrder->id;
    }

    /**
     * @param string $Pais
     * @param string $Ciudad
     * @param string $Municipio
     * @return array
     */
    public function convertAddress($Pais, $Ciudad, $Municipio){
        return [
            $this->text->getIdCountry() => $this->addressApi->getCountryByName($Pais)->id,
            $this->text->getIdMunicipality() => $this->addressApi->getMunicipalityByName($Municipio)->id,
            $this->text->getIdCity() => $this->addressApi->getCityByName($Ciudad)->id
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
     * @param array $DatosClientes
     * @param float $Total
     * @param float $SubTotal
     * @param float $TotalDescuento
     * @param int $CantidadProductos
     * @param array $DetailProforma
     * @param Partner $Partner
     * @return bool
     */
    public function validateDetailProforma(array $DatosClientes, float $Total, float $SubTotal, float $TotalDescuento, int $CantidadProductos, array $DetailProforma, Partner $Partner){
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
        $this->verifyStockProforma($DetailProforma, $DatosClientes, $Partner);
        return true;
    }

    /**
     * @param array $DetailProforma
     * @param array $DatosClientes
     * @param Partner $Partner
     */
    public function verifyStockProforma(array $DetailProforma, array $DatosClientes, Partner $Partner){
        $CiudadId = $this->addressApi->getStoreByName($DatosClientes[$this->text->getStoreMagento()])->id;
        foreach ($DetailProforma as $key => $Detail) {
            $idProduct = $this->getProductBySkuPartner($Detail[$this->text->getSkuApi()], $Partner->id)->id;
            $this->validateStock($CiudadId, $idProduct, $Detail[$this->text->getAlmacenApi()], $Detail[$this->text->getQty()]);
        }
    }
    
    /**
     * @param int $idCity
     * @param int $id_product
     * @param string $almacen
     * @param int $Qty
     */
    public function validateStock(int $idCity, int $id_product, string $almacen, int $Qty) {
        $idAlmacen = $this->getWarehouseByAlmacen($almacen)->id;
        $ProductWarehouse = ProductWarehouse::where($this->text->getIdProduct(), $id_product)->where($this->text->getIdWarehouse(), $idAlmacen)->where($this->text->getIdStore(), $idCity)->first();
        if (!$ProductWarehouse) {
            throw new Exception($this->text->getWarehouseProductNone());
        }
        if ($ProductWarehouse->stock < $Qty) {
            throw new Exception($this->text->getStockNoneProduct());
        }
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
            $this->preSaveDiscount($Detail[$this->text->getDescuentos()]);
        }
        return $TotalDescuento;
    }

    /**
     * @param array $Descuentos
     */
    public function preSaveDiscount(array $Descuentos){
        foreach ($Descuentos as $key => $Descuento) {
            if (!in_array($Descuento[$this->text->getCodigoDescuento()], $this->listDiscount)) {
                $this->listDiscount[] = $Descuento[$this->text->getCodigoDescuento()];
            }
        }
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
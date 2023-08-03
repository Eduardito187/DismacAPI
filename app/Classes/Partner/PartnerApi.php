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
use App\Classes\MailOrder;
use App\Models\CommittedStock;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\Sales;
use App\Models\SalesDetails;
use App\Models\ShippingAddress;
use App\Models\StatusOrder;
use App\Models\TipoDocumento;
use App\Models\Coupon;
use App\Models\HistoryStatusOrder;
use App\Models\ProductWarehouse;
use App\Models\SalesCoupon;
use App\Models\Warehouse;
use Exception;
use Illuminate\Http\Request;
use App\Classes\TokenAccess;
use App\Models\Analytics;
use App\Models\ProductCategory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PartnerApi{
    CONST FOLDER_PROFILES = "Profiles/";
    CONST FOLDER_PROCESS = "Process/";
    CONST FOLDER_COVERS = "Covers/";
    CONST FOLDER_PRODUCTS = "Products/";
    CONST PEDIDO_MARKETPLACE = "Pedido marketplace";
    CONST HISTOY_LAST = 8;
    CONST PENDIENTE = "PENDIENTE";
    CONST CANCELADA = "CANCELADA";
    CONST CANCELADA_ID = 1;
    CONST CERRADA = "CERRADA";
    CONST COMPLETADA = "COMPLETADA";
    CONST DEFAULT_STRING = "";
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
    /**
     * @var TokenAccess
     */
    protected $tokenAccess;

    public function __construct() {
        $this->date       = new Date();
        $this->status     = new Status();
        $this->text       = new Text();
        $this->pictureApi = new PictureApi();
        $this->addressApi = new AddressApi();
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function uploadPicture(Request $request){
        $id_Partner = $this->getPartnerByAccountId($this->getAccountToken($request->header($this->text->getAuthorization())));
        $id_picture = $this->pictureApi->uploadPicture($request, $id_Partner, self::FOLDER_PROFILES);
        return $this->updatePicturePartner($id_picture, $id_Partner, $this->text->getPictureProfile());
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function uploadZipPicture(Request $request){
        $id_Partner = $this->getPartnerByAccountId($this->getAccountToken($request->header($this->text->getAuthorization())));
        $picture = $this->pictureApi->uploadPicture($request, $id_Partner, self::FOLDER_PROCESS, true);
        $this->pictureApi->unZip($picture->path);
        $this->pictureApi->processZipFile($id_Partner, $picture->path);
        return true;
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function deletePicture(Request $request){
        $id_Partner = $this->getPartnerByAccountId($this->getAccountToken($request->header($this->text->getAuthorization())));
        $params = $request->all();
        $Picture = $this->pictureApi->getImageById($params[$this->text->getIdPicture()]);
        $this->pictureApi->deleteFile($Picture->path);
        return $this->pictureApi->deletePictureProduct($Picture->id);
    }
    
    /**
     * @param Request $request
     * @return bool
     */
    public function uploadPictures(Request $request){
        $id_Partner = $this->getPartnerByAccountId($this->getAccountToken($request->header($this->text->getAuthorization())));
        $params = $request->all();
        $Product = $this->getProductBySkuPartner($params[$this->text->getSku()], $id_Partner);
        $this->pictureApi->uploadPictures($request, $id_Partner, self::FOLDER_PRODUCTS, $Product->id);
        return true;
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function uploadCover(Request $request){
        $id_Partner = $this->getPartnerByAccountId($this->getAccountToken($request->header($this->text->getAuthorization())));
        $id_picture = $this->pictureApi->uploadPicture($request, $id_Partner, self::FOLDER_COVERS);
        return $this->updatePicturePartner($id_picture, $id_Partner, $this->text->getPictureFront());
    }

    /**
     * @param int $idAccount
     * @return int|null
     */
    public function getPartnerByAccountId(int $idAccount){
        $AccountPartner = AccountPartner::select($this->text->getIdPartner())->where($this->text->getIdAccount(), $idAccount)->get()->toArray();
        if (count($AccountPartner) > 0) {
            return $AccountPartner[0][$this->text->getIdPartner()];
        }else{
            throw new Exception($this->text->getNonePartner());
        }
    }

    /**
     * @param int $id_picture
     * @param int $id_partner
     * @param string $code
     * @return bool
     */
    public function updatePicturePartner(int $id_picture, int $id_partner, string $code){
        Partner::where($this->text->getId(), $id_partner)->update([
            $code => $id_picture
        ]);
        $Partner = Partner::where($this->text->getId(), $id_partner)->first();
        $Partner->updated_at = $this->date->getFullDate();
        $Partner->save();
        return $this->status->getEnable();
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
     * @return void
     */
    public function runProcessCronCommitedStock(){
        foreach ($this->getListCommietCron() as $key => $commited) {
            if ($commited->status != $this->status->getDisable()) {
                $this->processCommitedRevert($commited, $commited->store);
                $this->updatedOrderStatus($commited->Sales->id, self::CANCELADA_ID);
            }
        }
    }

    /**
     * @return CommittedStock[]
     */
    public function getListCommietCron(){
        return CommittedStock::where($this->text->getStatus(), 1)->whereDate($this->text->getDateLimit(), $this->text->getMinimusIguals(), $this->date->getFullDate())->get();
    }

    /**
     * @param int $sale
     * @param int $status
     */
    public function setHistoryStatusOrder(int $sale, int $status){
        try {
            $HistoryStatusOrder = new HistoryStatusOrder();
            $HistoryStatusOrder->sale = $sale;
            $HistoryStatusOrder->status = $status;
            $HistoryStatusOrder->created_at = $this->date->getFullDate();
            $HistoryStatusOrder->updated_at = null;
            $HistoryStatusOrder->save();
        } catch (Exception $th) {
            throw new Exception($th->getMessage());
        }
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
     * @param string|null $query
     * @param int $status
     * @param string $from_date
     * @param string $to_date
     * @return Sales[]
     */
    public function getFiltersOrder(string|null $query, int $status, string $from_date, string $to_date){
        $Sales = Sales::where($this->text->getStatus(), $status);
        if ($from_date != self::DEFAULT_STRING && $to_date != self::DEFAULT_STRING) {
            $Sales->whereBetween($this->text->getCreated(), [$from_date, $to_date]);
        }else if ($from_date != self::DEFAULT_STRING && $to_date == self::DEFAULT_STRING) {
            $Sales->whereDate($this->text->getCreated(), $this->text->getMaximusIguals() ,$from_date);
        }else if ($from_date == self::DEFAULT_STRING && $to_date != self::DEFAULT_STRING) {
            $Sales->whereDate($this->text->getCreated(), $this->text->getMinimusIguals() ,$to_date);
        }
        return $Sales->get();
    }

    /**
     * @param array $request
     * @return array
     */
    public function searchSale(array $request){
        $Sales = $this->getFiltersOrder(
            $request[$this->text->getQuery()],
            $this->getStatusOrderId($request[$this->text->getStatus()]),
            $request[$this->text->getFilters()][$this->text->getDateIni()] ?? self::DEFAULT_STRING,
            $request[$this->text->getFilters()][$this->text->getDateEnd()] ?? self::DEFAULT_STRING
        );
        return $this->searchArraySale($Sales);
    }

    /**
     * @param int $idSale
     * @return array
     */
    public function getOrder(int $idSale){
        $Sale = $this->getOrderByID($idSale);
        return array(
            $this->text->getId() => $Sale->id,
            $this->text->getDescuentos() => $Sale->discount,
            $this->text->getSubTotal() => $Sale->subtotal,
            $this->text->getTotal() => $Sale->total,
            $this->text->getNroControl() => $Sale->nro_control,
            $this->text->getNroFactura() => $Sale->nro_factura,
            $this->text->getNroProforma() => $Sale->nro_proforma,
            $this->text->getIp() => $Sale->ip_client,
            $this->text->getProducts() => $Sale->products,
            $this->text->getCustomer() => $this->getCustomerOrder($Sale->ShippingAddress),
            $this->text->getAddress() => $this->getAddressOrder($Sale->ShippingAddress),
            $this->text->getDetailOrder() => $this->getDetailOrderArray($Sale->SalesDetails),
            $this->text->getHistoryStatus() => $this->getHistorySale($Sale->HistoryStatusOrder),
            $this->text->getDetailDiscount() => $this->getDetailDiscount($Sale->SalesCoupon),
            $this->text->getDetalleWarehouse() => $this->getDeatilWarehouse($Sale->CommittedStock)
        );
    }

    public function getDeatilWarehouse($CommittedStock){
        $data = array();
        foreach ($CommittedStock as $key => $commited) {
            $data[] = array(
                $this->text->getSku() => $commited->Product->sku,
                $this->text->getName() => $commited->Product->name,
                $this->text->getQty() => $commited->qty,
                $this->text->getWarehouse() => $commited->Warehouse->name
            );
        }
        return $data;
    }

    public function getDetailDiscount($SalesCoupon){
        $data = array();
        foreach ($SalesCoupon as $key => $coupon) {
            $data[] = array(
                $this->text->getMontoApi() => $coupon->monto,
                $this->text->getPorcentajeApi() => $coupon->percent,
                $this->text->getCuponApi() => $coupon->Coupon->name,
                $this->text->getCouponCodeApi() => $coupon->Coupon->coupon_code
            );
        }
        return $data;
    }

    public function getAddressOrder($ShippingAddress){
        $Address = $ShippingAddress->Address;
        return array(
            $this->text->getPais() => $Address->Pais->name,
            $this->text->getCiudad() => $Address->Ciudad->name,
            $this->text->getMunicipio() => $Address->Municipio->name,
            $this->text->getLocalizacion() => $this->getLocalizationAddress($Address->Lozalizacion),
            $this->text->getAddressExtra() => $this->getAddressExtra($Address->AddressExtra)
        );
    }

    public function getAddressExtra($AddressExtra){
        return array(
            $this->text->getExtra() => $AddressExtra->extra,
            $this->text->getAddress() => $AddressExtra->address
        );
    }

    public function getLocalizationAddress($Lozalizacion){
        return array(
            $this->text->getLatitud() => $Lozalizacion->latitud,
            $this->text->getLongitud() => $Lozalizacion->longitud
        );
    }

    public function getDetailOrderArray($SalesDetails){
        $data = array();
        foreach ($SalesDetails as $key => $detail) {
            $data[] = array(
                $this->text->getSku() => $detail->Product->sku,
                $this->text->getName() => $detail->Product->name,
                $this->text->getImage() => $this->pictureApi->productFirstPicture($detail->Product->id),
                $this->text->getQty() => $detail->qty,
                $this->text->getDescuento() => $detail->discount,
                $this->text->getSubTotal() => $detail->subtotal,
                $this->text->getTotal() => $detail->total
            );
        }
        return $data;
    }

    public function getHistorySale($HistoryStatusOrder){
        $data = array();
        foreach ($HistoryStatusOrder as $key => $History) {
            $data[] = array(
                $this->text->getStatus() => $History->StatusOrder->status,
                $this->text->getCreated() => $History->created_at
            );
        }
        return $data;
    }

    /**
     * @param int $id
     * @return Sales
     */
    public function getOrderByID(int $id){
        $Sale = Sales::find($id);
        if (!$Sale) {
            throw new Exception($this->text->getOrdenNone());
        }
        return $Sale;
    }

    /**
     * @param mixed $Sales
     * @return array
     */
    public function searchArraySale($Sales){
        $data = array();
        foreach ($Sales as $key => $Sale) {
            $data[] = array(
                $this->text->getId() => $Sale->id,
                $this->text->getDescuentos() => $Sale->discount,
                $this->text->getSubTotal() => $Sale->subtotal,
                $this->text->getTotal() => $Sale->total,
                $this->text->getNroControl() => $Sale->nro_control,
                $this->text->getNroFactura() => $Sale->nro_factura,
                $this->text->getNroProforma() => $Sale->nro_proforma,
                $this->text->getIp() => $Sale->ip_client,
                $this->text->getProducts() => $Sale->products,
                $this->text->getCustomer() => $this->getCustomerOrder($Sale->ShippingAddress),
                $this->text->getAddress() => $this->getAddressOrder($Sale->ShippingAddress),
            );
        }
        return $data;
    }

    public function getCustomerOrder($ShippingAddress){
        $Customer = $ShippingAddress->Customer;
        return array(
            $this->text->getNombre() => $Customer->nombre,
            $this->text->getColApellidoPaterno() => $Customer->apellido_paterno,
            $this->text->getColApellidoMaterno() => $Customer->apellido_materno,
            $this->text->getEmail() => $Customer->email,
            $this->text->getColNumTelf() => $Customer->num_telefono,
            $this->text->getColNumDoc() => $Customer->num_documento,
            $this->text->getColTipoDoc() => $Customer->TipoDocumento->type,
            $this->text->getId() => $Customer->id
        );
    }

    /**
     * @param string $code
     * @return string
     */
    public function convertColumn(string $code){
        switch ($code) {
            case $this->text->getNroFactura():
                return $this->text->getColumnNroFactura();
            case $this->text->getNroProforma():
                return $this->text->getColumnNroProforma();
            case $this->text->getNroControl():
                return $this->text->getColumnNroControl();
        }
    }

    /**
     * @param string $code
     * @param string $value
     * @return Sales
     */
    public function getOrderByApi(string $code, string $value){
        $Sale = Sales::where($this->convertColumn($code), $value)->first();
        if (!$Sale) {
            throw new Exception($this->text->getOrdenNone());
        }
        return $Sale;
    }

    /**
     * @param array $request
     * @return bool
     */
    public function cancelarOrden(array $request){
        $Sale = $this->getOrderByApi($request[$this->text->getType()], $request[$this->text->getCode()]);
        $this->revertCommmiterProduct($Sale->id, $request[$this->text->getStore()]);
        $this->updateStatusSales($Sale->id, self::CANCELADA);
        return true;
    }

    /**
     * @param int $orderId
     */
    public function getCommitedProductByOrderId(int $orderId){
        return CommittedStock::where($this->text->getColumSale(), $orderId)->get();
    }

    /**
     * @param int $orderId
     * @param int $store
     */
    public function revertCommmiterProduct(int $orderId, int $store){
        $CommittedStocks = $this->getCommitedProductByOrderId($orderId);
        $this->proccessCommittedStock($CommittedStocks, $store);
    }

    /**
     * @param mixed $CommittedStocks
     * @param int $store
     */
    public function proccessCommittedStock(mixed $CommittedStocks, int $store){
        foreach ($CommittedStocks as $key => $CommittedStock) {
            $this->processCommitedRevert($CommittedStock, $store);
        }
    }

    /**
     * @param CommittedStock $CommittedStocks
     * @param int $store
     * @return void
     */
    public function processCommitedRevert(CommittedStock $CommittedStock, int $store){
        $ProductWarehouse = ProductWarehouse::where($this->text->getIdProduct(), $CommittedStock->product)->where($this->text->getIdWarehouse(), $CommittedStock->warehouse)->where($this->text->getIdStore(), $store)->first();
        if (!$ProductWarehouse) {
            throw new Exception($this->text->getWarehouseProductNone());
        }
        $newStock = $ProductWarehouse->stock + $CommittedStock->qty;
        $this->updateProductWarehouse($CommittedStock->product, $CommittedStock->warehouse, $store, $newStock);
        $this->disableCommittedStock($CommittedStock->sales, $CommittedStock->product, $CommittedStock->warehouse, $store);
        $newStock = $CommittedStock->Product->stock + $CommittedStock->qty;
        $this->updateProductStock($CommittedStock->product, $newStock);
    }

    /**
     * @param int $idSale
     * @param int $idProduc
     * @param int $idWarehouse
     * @param int $store
     * @return void
     */
    public function disableCommittedStock(int $idSale, int $idProduc, int $idWarehouse, int $store){
        CommittedStock::where($this->text->getColumSale(), $idSale)->where($this->text->getProduct(), $idProduc)->where($this->text->getWarehouse(), $idWarehouse)->where($this->text->getStore(), $store)->update([
            $this->text->getStatus() => $this->status->getDisable(),
            $this->text->getUpdated() => $this->date->getFullDate()
        ]);
    }
    
    /**
     * @param array $request
     * @return bool
     */
    public function completarOrden(array $request){
        $Sale = $this->getOrderByApi($request[$this->text->getType()], $request[$this->text->getCode()]);
        $this->updateStatusSales($Sale->id, self::COMPLETADA);
        return true;
    }

    /**
     * @param array $request
     * @return bool
     */
    public function cerrarOrden(array $request){
        $Sale = $this->getOrderByApi($request[$this->text->getType()], $request[$this->text->getCode()]);
        $this->updateStatusSales($Sale->id, self::CERRADA);
        return true;
    }

    /**
     * @param int $id_sale
     * @return string $status
     * @return void
     */
    public function updateStatusSales(int $id_sale, string $status){
        $id_Status = $this->getStatusOrderId($status);

        $Sales = Sales::where($this->text->getId(), $id_sale)->first();
        $Sales->status = $id_Status;
        $Sales->save();

        $this->setHistoryStatusOrder($id_sale, $id_Status);
    }

    /**
     * @param int $id_sale
     * @param int $id_Status
     * @return void
     */
    public function updatedOrderStatus(int $id_sale, int $id_Status){
        $Sales = Sales::where($this->text->getId(), $id_sale)->first();
        $Sales->status = $id_Status;
        $Sales->updated_at = $this->date->getFullDate();
        $Sales->save();
        $this->setHistoryStatusOrder($id_sale, $id_Status);
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
     * @param int $Id
     * @return Product
     */
    public function getProductById(int $Id){
        $product = Product::find($Id);
        if (!$product) {
            throw new Exception($this->text->getProductNone());
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
     * @return Product
     */
    public function createDetailSales(Partner $Partner, array $Detalle){
        try {
            $Product = $this->getProductBySkuPartner($Detalle[$this->text->getSkuApi()], $Partner->id);
            $SalesDetails = new SalesDetails();
            $SalesDetails->sales = $this->lastIdOrder;
            $SalesDetails->product = $Product->id;
            $SalesDetails->qty = $Detalle[$this->text->getQty()];
            $SalesDetails->discount = $Detalle[$this->text->getTotalDescuento()];
            $SalesDetails->subtotal = $Detalle[$this->text->getSubTotal()];
            $SalesDetails->total = $Detalle[$this->text->getTotal()];
            $SalesDetails->created_at = $this->date->getFullDate();
            $SalesDetails->updated_at = null;
            $SalesDetails->save();
            return $Product;
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
            $Product = $this->createDetailSales($Partner, $Detalle);
            $this->registerDiscountSale($idCustomer, $Partner->id, $Detalle[$this->text->getDescuentos()]);
            $this->validateStockSku($CiudadId, $Product, $Detalle[$this->text->getAlmacenApi()], $Detalle[$this->text->getQty()], $DetalleCliente[$this->text->getFechaCompromiso()]);
        }
        $newEmail = new MailOrder($Partner->email, self::PEDIDO_MARKETPLACE, $this->lastIdOrder, $DetalleCliente[$this->text->getFechaCompromiso()]);
        $newEmail->createMail();
    }

    /**
     * @param int $idCity
     * @param Product $Product
     * @param string $almacen
     * @param string $FechaCompromiso
     */
    public function validateStockSku(int $idCity, Product $Product, string $almacen, int $Qty, string $FechaCompromiso) {
        $idAlmacen = $this->getWarehouseByAlmacen($almacen)->id;
        $ProductWarehouse = ProductWarehouse::where($this->text->getIdProduct(), $Product->id)->where($this->text->getIdWarehouse(), $idAlmacen)->where($this->text->getIdStore(), $idCity)->first();
        if (!$ProductWarehouse) {
            throw new Exception($this->text->getWarehouseProductNone());
        }
        if ($ProductWarehouse->stock < $Qty) {
            throw new Exception($this->text->getStockNoneProduct());
        }
        $newStock = $ProductWarehouse->stock - $Qty;
        $this->updateProductWarehouse($Product->id, $idAlmacen, $idCity, $newStock);
        $this->committedStock($Product->id, $idAlmacen, $Qty, $FechaCompromiso, $idCity);
        $newStock = $Product->stock - $Qty;
        $this->updateProductStock($Product->id, $newStock);
    }

    /**
     * @param int $id_product
     * @param int $idAlmacen
     * @param int $Qty
     * @param int $idCity
     * @param string $FechaCompromiso
     */
    public function committedStock(int $id_product, int $idAlmacen, int $Qty, string $FechaCompromiso, int $idCity){
        try {
            $CommittedStock = new CommittedStock();
            $CommittedStock->sales = $this->lastIdOrder;
            $CommittedStock->product = $id_product;
            $CommittedStock->warehouse = $idAlmacen;
            $CommittedStock->qty = $Qty;
            $CommittedStock->status = $this->status->getEnable();
            $CommittedStock->date_limit = $FechaCompromiso;
            $CommittedStock->store = $idCity;
            $CommittedStock->created_at = $this->date->getFullDate();
            $CommittedStock->updated_at = null;
            $CommittedStock->save();
        } catch (Exception $th) {
            throw new Exception($th->getMessage());
        }
    }

    /**
     * @param int $id_product
     * @param int $idAlmacen
     * @param int $idCity
     * @param int $newStock
     */
    public function updateProductWarehouse(int $id_product, int $idAlmacen, int $idCity, int $newStock){
        ProductWarehouse::where($this->text->getIdProduct(), $id_product)->where($this->text->getIdWarehouse(), $idAlmacen)->where($this->text->getIdStore(), $idCity)->update([
            $this->text->getStock() => $newStock,
            $this->text->getUpdated() => $this->date->getFullDate()
        ]);
    }

    /**
     * @param int $id_product
     * @param int $newStock
     */
    public function updateProductStock(int $id_product, int $newStock){
        $Product = Product::where($this->text->getId(), $id_product)->first();
        $Product->stock = $newStock;
        $Product->updated_at = $this->date->getFullDate();
        $Product->save();
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
            $this->setHistoryStatusOrder($Sales->id, $Sales->status);
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
        $GEO = $this->addressApi->createGeo($clientAddress[$this->text->getLocalizacion()]);
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
     * @return int
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
     * @return array
     */
    public function campaignsPartner(Partner $partner){
        return $this->getCampaignsPartner($partner);
    }

    /**
     * @param int $id
     * @return Campaign
     */
    public function getCampaignById(int $id){
        $Campaign = Campaign::find($id);
        if (!$Campaign){
            throw new Exception($this->text->getCampaignNone());
        }
        return $Campaign;
    }

    /**
     * @param int $id
     * @return array|null
     */
    public function campaignPartner(int $id){
        $Campaign = $this->getCampaignById($id);
        return $this->getCampaingArray($Campaign);
    }

    /**
     * @return array
     */
    public function getAnalyticsType(){
        return Analytics::select($this->text->getType())->distinct($this->text->getType())->get()->toArray();
    }

    /**
     * @param string $type
     * @param string $code
     * @return array
     */
    public function generateAnalyticsReportMonths(string $type, string $code){
        $firstDayOfMonth = Carbon::today()->firstOfMonth();
        $lastDayOfMonth = Carbon::today()->lastOfMonth();
        $daysOfMonth = [];
        $currentDay = $firstDayOfMonth->copy();
        while ($currentDay->lte($lastDayOfMonth)) {
            $daysOfMonth[] = $currentDay->format('Y-m-d');
            $currentDay->addDay();
        }

        $sumValuesByDay = Analytics::where('type', $type)
            ->where('code', $code)
            ->whereBetween('created_at', [$firstDayOfMonth, $lastDayOfMonth])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->selectRaw('DATE(created_at) as date, SUM(value) as total')
            ->get();

        $sumByDay = [];
        foreach ($daysOfMonth as $day) {
            $sumByDay[$day] = 0;
        }

        foreach ($sumValuesByDay as $result) {
            $sumByDay[$result->date] = $result->total;
        }

        foreach ($sumByDay as $date => $total) {
            $englishDay = Carbon::parse($date)->format('l');
            $spanishDay = __(strtolower($englishDay));

            echo "Fecha: " . $spanishDay . " ($date), Total: " . $total . "\n";
        }
        return [];
    }

    /**
     * @param string $type
     * @param string $code
     * @return array
     */
    public function generateAnalyticsReportDays(string $type, string $code){
        $today = Carbon::today();

        // Obtener el primer día de la semana actual (domingo)
        $firstDayOfWeek = Carbon::today()->startOfWeek();

        // Obtener la suma de 'value' por días de la semana actual
        $sumValuesByDayOfWeek = Analytics::where($this->text->getType(), $type)
            ->where($this->text->getCode(), $code)
            ->whereBetween($this->text->getCreated(), [$firstDayOfWeek, $today])
            ->groupBy(DB::raw($this->text->getRawCreated()))
            ->selectRaw($this->text->getSelectedRawCreated())
            ->get();

        // Crear un arreglo para almacenar la suma de cada día de la semana
        $sumByDayOfWeek = [
            'sunday' => 0,
            'monday' => 0,
            'tuesday' => 0,
            'wednesday' => 0,
            'thursday' => 0,
            'friday' => 0,
            'saturday' => 0,
        ];

        // Actualizar el arreglo con los valores obtenidos de la base de datos
        foreach ($sumValuesByDayOfWeek as $result) {
            // Obtener el nombre del día en inglés
            $englishDay = Carbon::parse($result->date)->format('l');
            
            // Obtener el nombre del día en español directamente de la traducción
            $spanishDay = strtolower($englishDay);
            echo $spanishDay."-";

            // Actualizar el valor en el arreglo con la suma correspondiente
            $sumByDayOfWeek[$spanishDay] = $result->total;
        }

        // Imprimir los resultados con el nombre del día en español
        $response = [];
        foreach ($sumByDayOfWeek as $day => $total) {
            $response[] = [
                "day" => $day,
                "total" => $total,
            ];
        }

        return $response;
    }

    /**
     * @param string $type
     * @return array
     */
    public function getAnalyticsEventsType(string $type){
        return Analytics::select($this->text->getCode())->where($this->text->getType(), $type)->distinct($this->text->getCode())->get()->toArray();
    }

    /**
     * @param Partner $partner
     * @return array
     */
    public function getCampaignsPartner(Partner $partner){
        $data = array();
        foreach ($partner->Campaign as $key => $Campaign) {
            $data[] = $this->getCampaingArray($Campaign);
        }
        return $data;
    }

    public function getCampaingArray($Campaign){
        if (is_null($Campaign)){
            return null;
        }
        return array(
            $this->text->getId() => $Campaign->id,
            $this->text->getUrl() => $Campaign->url,
            $this->text->getName() => $Campaign->name,
            $this->text->getStatus() => $Campaign->status,
            $this->text->getProducts() => $this->countProductCategory($Campaign->id_category),
            $this->text->getFromAt() => $Campaign->from_at,
            $this->text->getToAt() => $Campaign->to_at,
            $this->text->getSocial() => $this->getArraySocial($Campaign->SocialCampaings),
            $this->text->getCategory() => $this->getArrayCategory($Campaign->Category)
        );
    }

    /**
     * @param int|null $id_category
     * @return int|null
     */
    public function countProductCategory(int|null $id_category){
        if (is_null($id_category)){
            return 0;
        }
        return ProductCategory::select($this->text->getIdProduct())->where($this->text->getIdCategory(), $id_category)->distinct($this->text->getIdProduct())->count($this->text->getIdProduct());
    }

    /**
     * @param Category|null $Category
     * @return array|null
     */
    public function getArrayCategory(Category|null $Category){
        if (is_null($Category)){
            return null;
        }
        return array(
            $this->text->getId() => $Category->id,
            $this->text->getName() => $Category->name,
            $this->text->getCode() => $Category->code
        );
    }

    /**
     * @return array
     */
    public function getArraySocial($SocialCampaings){
        $data = array();
        foreach ($SocialCampaings as $key => $SocialCampaing) {
            $data[] = array(
                $this->text->getUrl() => $SocialCampaing->url,
                $this->text->getSocial() => $this->getSocialArray($SocialCampaing->SocialNetwork)
            );
        }
        return $data;
    }

    /**
     * @return null|array
     */
    public function getSocialArray($Social){
        if (is_null($Social)){
            return null;
        }
        return array(
            $this->text->getId() => $Social->id,
            $this->text->getName() => $Social->name,
            $this->text->getUrl() => $Social->url
        );
    }

    /**
     * @param Account|null $Account
     * @return array
     */
    public function getStorePartner(Account|null $Account){
        $data = array();
        $data[$this->text->getStore()] = $this->getAllStore();
        if (is_null($Account)){
            $data[$this->text->getPartner()] = null;
        }else{
            $partner = $Account->accountPartner->Partner;
            $data[$this->text->getPartner()] = $this->getAllStorePartnerArray($partner->Stores);
        }
        return $data;
    }

    public function getAllStorePartnerArray($stores){
        $data = array();
        foreach ($stores as $key => $store) {
            $data[] = $this->storeArray($store->Store);
        }
        return $data;
    }

    /**
     * @param Store|null $store
     * @return array
     */
    public function storeArray(Store|null $store){
        if (!$store){
            return null;
        }
        return array(
            $this->text->getId() => $store->id,
            $this->text->getName() => $store->name,
            $this->text->getCode() => $store->code
        );
    }

    /**
     * @return array
     */
    public function getAllStore(){
        $stores = Store::all();
        return $this->getAllStoreArray($stores);
    }

    public function getAllStoreArray($stores){
        $data = array();
        foreach ($stores as $key => $store) {
            $data[] = $this->storeArray($store);
        }
        return $data;
    }

    /**
     * @param Partner $partner
     * @return array
     */
    public function socialNetworkPartner(Partner $partner){
        return $this->getSocialNetworkPartnet($partner);
    }

    /**
     * @param Partner $partner
     * @return array
     */
    public function getSocialNetworkPartnet(Partner $partner){
        $data = array();
        foreach ($partner->SocialPartner as $key => $social_partner) {
            $Social = $social_partner->Social;
            $data[] = array(
                $this->text->getUrl() => $social_partner->url,
                $this->text->getSocial() => $this->getSocialArray($Social)
            );
        }
        return $data;
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
        return Campaign::select($this->text->getIdCategory())->where($this->text->getIdPartner(), $id_partner)->distinct()->count($this->text->getIdCategory());
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
                $value += $this->calculoPriceProduct($store->id, $product->id) * $this->getStockAbsolutePorduct($product->id, $store->id);
            }
        }
        return $value;
    }

    /**
     * @param int $product_id
     * @param int $id_store
     * @return int
     */
    public function getStockAbsolutePorduct(int $product_id, int $id_store){
        return intval(ProductWarehouse::where($this->text->getIdProduct(), $product_id)->where($this->text->getIdStore(), $id_store)->sum($this->text->getStock()));
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
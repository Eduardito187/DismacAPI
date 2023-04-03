<?php

namespace App\Classes\Product;

use App\Classes\Helper\Date;
use App\Classes\Helper\Status;
use App\Classes\Helper\Text;
use Exception;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Clacom;
use App\Models\MiniCuota;
use App\Models\ProductMinicuotaStore;
use App\Models\ProductStoreStatus;
use App\Models\ProductType;
use App\Models\Store;
use Illuminate\Support\Facades\Log;
use App\Classes\Account\AccountApi;
use App\Models\Category;
use App\Models\CategoryInfo;
use App\Models\Price;
use App\Models\ProductCategory;
use App\Models\ProductPriceStore;
use App\Models\ProductWarehouse;
use App\Models\Warehouse;
use \Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductApi{
    CONST FILTER_ALL = "ALL";
    CONST FILTER_LAST_EDIT = "LAST_EDIT";
    CONST FILTER_LAST_CREATE = "LAST_CREATE";

    CONST OPLN_PRECIO_PROPUESTO = 1;
    CONST OPLN_TIENDAS_SCZ = 3;
    CONST NAME_SCZ         = "SCZ";
    CONST OPLN_TIENDAS_LPZ = 4;
    CONST NAME_LPZ         = "LPZ";
    CONST OPLN_TIENDAS_CBA = 5;
    CONST NAME_CBA         = "CBA";
    CONST OPLN_TIENDAS_TRJ = 22;
    CONST NAME_TRJ         = "TRJ";
    CONST OPLN_TIENDAS_SCE = 23;
    CONST NAME_SCE         = "SCE";
    /**
     * @var Date
     */
    protected $date;
    /**
     * @var AccountApi
     */
    protected $accountApi;
    /**
     * @var Status
     */
    protected $status;
    /**
     * @var Text
     */
    protected $text;

    public function __construct() {
        $this->date         = new Date();
        $this->status       = new Status();
        $this->text         = new Text();
        $this->accountApi   = new AccountApi();
    }



    /**
     * @param string $code
     * @return int|null
     */
    private function getCatalogStore(string $code){
        //->where($this->text->getName(), $name)
        $Product = Product::select($this->text->getId())->where($this->text->getSku(), $code)->get()->toArray();
        if (count($Product) > 0) {
            return $Product[0][$this->text->getId()];
        }else{
            return null;
        }
    }

    /**
     * @param string $code
     * @param string $name
     * @param string|null $id_brand
     * @param string|null $id_clacom
     * @param string|null $id_type
     * @param int|null $id_Account
     */
    private function setProduct(string $code, string $name, string|null $id_brand, string|null $id_clacom, string|null $id_type, int|null $id_Account){
        try {
            $Product = new Product();
            $Product->name = $name;
            $Product->sku = $code;
            $Product->stock = 0;
            $Product->id_brand = $id_brand;
            $Product->id_clacom = $id_clacom;
            $Product->id_type = $id_type;
            $Product->created_at = $this->date->getFullDate();
            $Product->id_partner = $id_Account;
            $Product->save();
        } catch (Exception $th) {
            throw new Exception($th->getMessage());
        }
    }

    /**
     * @param string $filter
     * @return boolean
     */
    public function getFilterAll(string $filter){
        if ($filter == SELF::FILTER_ALL) {
            return true;
        }else{
            return false;
        }
    }

    /**
     * @param string $filter
     * @param int $step
     * @return string
     */
    public function getFilter(string $filter, int $step){
        if ($filter == SELF::FILTER_ALL) {
            if ($step == 0) {
                return SELF::FILTER_LAST_EDIT;
            }else{
                return SELF::FILTER_LAST_CREATE;
            }
        }else if ($filter == SELF::FILTER_LAST_CREATE) {
            return SELF::FILTER_LAST_CREATE;
        }else if ($filter == SELF::FILTER_LAST_EDIT) {
            return SELF::FILTER_LAST_EDIT;
        }else{
            throw new Exception($this->text->getNoneFilter());
        }
    }
    
    /**
     * @param string $filter
     * @return string
     */
    public function getColumnFilter(string $filter){
        if ($filter == SELF::FILTER_LAST_CREATE) {
            return $this->text->getCreated();
        }else if ($filter == SELF::FILTER_LAST_EDIT) {
            return $this->text->getUpdated();
        }else{
            throw new Exception($this->text->getNoneFilter());
        }
    }
    
    /**
     * @param string $filter
     * @return string
     */
    public function getValueFilter(string $filter){
        if ($filter == SELF::FILTER_LAST_CREATE) {
            return $this->text->getOrderDesc();
        }else if ($filter == SELF::FILTER_LAST_EDIT) {
            return $this->text->getOrderDesc();
        }else{
            throw new Exception($this->text->getNoneFilter());
        }
    }

    /**
     * @param int $id
     * @param string $code
     * @param string $name
     * @param string|null $id_brand
     * @param string|null $id_clacom
     * @param string|null $id_type
     * @param int|null $id_Account
     */
    private function updateProductALL(int $id, string $code, string $name, string|null $id_brand, string|null $id_clacom, string|null $id_type, int|null $id_Account){
        Product::where($this->text->getId(), $id)->update([
            $this->text->getName() => $name,
            $this->text->getIdBrand() => $id_brand,
            $this->text->getIdClacom() => $id_clacom,
            $this->text->getIdType() => $id_type,
            $this->text->getUpdated() => $this->date->getFullDate(),
            $this->text->getIdPartner() => $id_Account
        ]);
    }

    /**
     * @param string $column
     * @param string $filter
     * @param int $partnerID
     * @param int $maxItems
     * @param string $minValue
     * @return array
     */
    public function getProductsByDate(string $column, string $filter, int $partnerID, int $maxItems, string $minValue){
        return DB::table($this->text->getProduct())->where(
            $this->text->getIdPartner(), $partnerID
        )->whereBetween(
            $column, [
                $minValue,
                $this->date->getFullDate()
            ]
        )->offset(0)->limit($maxItems)->orderBy(
            $column,
            $filter
        )->get()->toArray();
    }
    
    /**
     * @param int $id
     * @param string $code
     * @param string $name
     * @param string|null $id_brand
     * @param string|null $id_clacom
     * @param string|null $id_type
     * @param int|null $id_Account
     */
    private function updateProductRelations(int $id, string $code, string $name, string|null $id_brand, string|null $id_clacom, string|null $id_type, int|null $id_Account){
        Product::where($this->text->getId(), $id)->update([
            $this->text->getIdBrand() => $id_brand,
            $this->text->getIdClacom() => $id_clacom,
            $this->text->getIdType() => $id_type,
            $this->text->getUpdated() => $this->date->getFullDate(),
            $this->text->getIdPartner() => $id_Account
        ]);
    }

    /**
     * @param array $response
     * @param Request $request
     */
    public function applyRequestAPI(array $response, Request $request){
        $id_Account = $this->accountApi->getPartnerId($this->accountApi->getAccountToken($request->header($this->text->getAuthorization())));
        $allStore = $this->getAllStoreID();
        Log::debug("all store => ".json_encode($allStore));
        foreach ($response as $res) {
            Log::debug("sku => ".$res[$this->text->getCodigo()]);
            $id_product = $this->getCatalogStore($res[$this->text->getCodigo()]);
            $id_brand = null;
            $id_type = null;
            $id_clacom = null;
            if (!empty($res[$this->text->getMarca()]) && is_array($res[$this->text->getMarca()])) {
                $id_brand = $this->getBrand($res[$this->text->getMarca()][$this->text->getNombre()]);
                if (is_null($id_brand)) {
                    if($this->setBrand($res[$this->text->getMarca()][$this->text->getNombre()])){
                        $id_brand = $this->getBrand($res[$this->text->getMarca()][$this->text->getNombre()]);
                    }
                }
            }
            if (!empty($res[$this->text->getDetalle()]) && is_array($res[$this->text->getDetalle()])) {
                $id_type = $this->getType($res[$this->text->getDetalle()][$this->text->getTipoProducto()]);
                $id_clacom = $this->getClacom($res[$this->text->getDetalle()][$this->text->getClacom()]);
                if (is_null($id_type)) {
                    if($this->setType($res[$this->text->getDetalle()][$this->text->getTipoProducto()])){
                        $id_type = $this->getType($res[$this->text->getDetalle()][$this->text->getTipoProducto()]);
                    }
                }
                if (is_null($id_clacom)) {
                    if($this->setClacom($res[$this->text->getDetalle()][$this->text->getClacom()])){
                        $id_clacom = $this->getClacom($res[$this->text->getDetalle()][$this->text->getClacom()]);
                    }
                }
            }
            if (is_null($id_product)) {
                $this->setProduct(
                    $res[$this->text->getCodigo()],
                    !empty($res[$this->text->getNombre()]) ? $res[$this->text->getNombre()] : $this->text->getTextNone(),
                    $id_brand,
                    $id_clacom,
                    $id_type,
                    $id_Account
                );
                $id_product = $this->getCatalogStore(
                    $res[$this->text->getCodigo()]
                );
                $this->updateProductRelations(
                    $id_product,
                    $res[$this->text->getCodigo()],
                    !empty($res[$this->text->getNombre()]) ? $res[$this->text->getNombre()] : $this->text->getTextNone(),
                    $id_brand,
                    $id_clacom,
                    $id_type,
                    $id_Account
                );
            }else{
                $this->updateProductALL(
                    $id_product,
                    $res[$this->text->getCodigo()],
                    !empty($res[$this->text->getNombre()]) ? $res[$this->text->getNombre()] : $this->text->getTextNone(),
                    $id_brand,
                    $id_clacom,
                    $id_type,
                    $id_Account
                );
            }
            if (!empty($res[$this->text->getMinicuotas()]) && is_array($res[$this->text->getMinicuotas()]) && !is_null($id_product)) {
                $this->changeMiniCuotas($id_product, $res[$this->text->getMinicuotas()]);
            }
            if (!empty($res[$this->text->getEstado()]) && is_array($res[$this->text->getEstado()]) && !is_null($id_product)) {
                $this->changeStatusProduct($id_product, $allStore, $res[$this->text->getEstado()][$this->text->getVisible()]);
            }
            if (!empty($res[$this->text->getClasificacion()]) && is_array($res[$this->text->getClasificacion()]) && !is_null($id_product)) {
                $this->setClasificacion($res[$this->text->getClasificacion()], false, $allStore, $id_product);
            }
            if (!empty($res[$this->text->getPreciosPos()]) && is_array($res[$this->text->getPreciosPos()]) && !is_null($id_product)) {
                $this->setProductAllPrice($res[$this->text->getPreciosPos()], $id_product);
            }
            if (!empty($res[$this->text->getDisponibilidadPos()]) && is_array($res[$this->text->getDisponibilidadPos()]) && !is_null($id_product)) {
                $this->updateProducStock($id_product, $this->setDisponibility($res[$this->text->getDisponibilidadPos()], $id_product));
            }
        }
        Log::debug("FIN => IMPORT");
    }

    /**
     * @param array $disponibilidades
     * @param int $id_product
     * @return int
     */
    public function setDisponibility(array $disponibilidades, int $id_product){
        $stockNacional = 0;
        foreach ($disponibilidades as $disponibilidad) {
            $stockNacional += intval($disponibilidad[$this->text->getStockDisponible()]);
            $id_stores = $this->convertListToStoreName($disponibilidad[$this->text->getNombreAlmacen()]);
            $this->loadbyStoresDisponibility($id_product, $id_stores, $disponibilidad);
        }
        return $stockNacional;
    }

    /**
     * @param int $idProduct
     * @param array $id_stores
     * @param array $disponibilidad
     */
    public function loadbyStoresDisponibility(int $idProduct, array $id_stores, array $disponibilidad){
        foreach ($id_stores as $id_store) {
            if($id_store != 0){
                $id_warehouse = $this->getWarehouse($disponibilidad[$this->text->getNombreAlmacen()], $disponibilidad[$this->text->getCode()], $disponibilidad[$this->text->getAlmacenCentral()], $disponibilidad[$this->text->getAlmacen()]);
                if (is_null($id_warehouse)) {
                    $this->setWarehouse($disponibilidad[$this->text->getNombreAlmacen()], $disponibilidad[$this->text->getCode()], $disponibilidad[$this->text->getAlmacenCentral()], $disponibilidad[$this->text->getAlmacen()]);
                    $id_warehouse = $this->getWarehouse($disponibilidad[$this->text->getNombreAlmacen()], $disponibilidad[$this->text->getCode()], $disponibilidad[$this->text->getAlmacenCentral()], $disponibilidad[$this->text->getAlmacen()]);
                }
                if (is_null($this->getProductWarehouse($idProduct, $id_warehouse, intval($disponibilidad[$this->text->getStockDisponible()]), $id_store))) {
                    $this->setProductWarehouse($idProduct, $id_warehouse, intval($disponibilidad[$this->text->getStockDisponible()]), $id_store);
                }else{
                    $this->updateProductWarehouse($idProduct, $id_warehouse, intval($disponibilidad[$this->text->getStockDisponible()]), $id_store);
                }
            }
        }
    }
    
    /**
     * @param int $id_product
     * @param int $id_warehouse
     * @param int $stock
     * @param int $id_store
     */
    public function updateProductWarehouse(int $id_product, int $id_warehouse, int $stock, int $id_store){
        ProductWarehouse::where($this->text->getIdProduct(), $id_product)->where($this->text->getIdWarehouse(), $id_warehouse)->where($this->text->getIdStore(), $id_store)->update([
            $this->text->getStock() => $stock,
            $this->text->getUpdated() => $this->date->getFullDate()
        ]);
    }

    /**
     * @param int $id_product
     * @param int $stock
     */
    public function updateProducStock(int $id_product, int $stock){
        Product::where($this->text->getId(), $id_product)->update([
            $this->text->getStock() => $stock
        ]);
    }

    /**
     * @param int $id_product
     * @param int $id_warehouse
     * @param int $stock
     * @param int $id_store
     */
    public function setProductWarehouse(int $id_product, int $id_warehouse, int $stock, int $id_store){
        try {
            $ProductWarehouse = new ProductWarehouse();
            $ProductWarehouse->id_product = $id_product;
            $ProductWarehouse->id_warehouse = $id_warehouse;
            $ProductWarehouse->stock = $stock;
            $ProductWarehouse->id_store = $id_store;
            $ProductWarehouse->created_at = $this->date->getFullDate();
            $ProductWarehouse->updated_at = null;
            $ProductWarehouse->save();
        } catch (Exception $th) {
            throw new Exception($th->getMessage());
        }
    }

    /**
     * @param int $id_product
     * @param int $id_warehouse
     * @param int $stock
     * @param int $id_store
     */
    public function getProductWarehouse(int $id_product, int $id_warehouse, int $stock, int $id_store){
        $ProductWarehouse = ProductWarehouse::select($this->text->getIdProduct())->where($this->text->getIdProduct(), $id_product)->
        where($this->text->getIdWarehouse(), $id_warehouse)->where($this->text->getStock(), $stock)->where($this->text->getIdStore(), $id_store)->get()->toArray();
        if (count($ProductWarehouse) > 0) {
            return $ProductWarehouse[0][$this->text->getIdProduct()];
        }else{
            return null;
        }
    }

    /**
     * @param int $id_product
     * @param int $id_store
     * @return array
     */
    public function getStoreWarehouse(int $id_product, int $id_store){
        return ProductWarehouse::where($this->text->getIdProduct(), $id_product)->where($this->text->getIdStore(), $id_store)->get()->toArray();
    }

    /**
     * @param int $id_product
     * @param int $id_store
     * @return int
     */
    public function getProductStockStore(int $id_product, int $id_store){
        return ProductWarehouse::where($this->text->getIdProduct(), $id_product)->where($this->text->getIdStore(), $id_store)->sum($this->text->getStock());
    }

    /**
     * @param string $name
     * @param string $code
     * @param bool $base
     * @param string $almacen
     */
    public function setWarehouse(string $name, string $code, bool $base, string $almacen){
        try {
            $Warehouse = new Warehouse();
            $Warehouse->name = $name;
            $Warehouse->code = $code;
            $Warehouse->base = $base;
            $Warehouse->almacen = $almacen;
            $Warehouse->created_at = $this->date->getFullDate();
            $Warehouse->updated_at = null;
            $Warehouse->save();
        } catch (Exception $th) {
            throw new Exception($th->getMessage());
        }
    }
    
    /**
     * @param string $name
     * @param string $code
     * @param bool $base
     * @param string $almacen
     * @return int|null
     */
    public function getWarehouse(string $name, string $code, bool $base, string $almacen){
        $Warehouse = Warehouse::select($this->text->getId())->where($this->text->getName(), $name)->
        where($this->text->getCode(), $code)->where($this->text->getBase(), $base)->where($this->text->getAlmacen(), $almacen)->get()->toArray();
        if (count($Warehouse) > 0) {
            return $Warehouse[0][$this->text->getId()];
        }else{
            return null;
        }
    }
    
    /**
     * @param int $id_warehouse
     * @return array|null
     */
    public function getWarehouseName(int $id_warehouse){
        $Warehouse = Warehouse::select($this->text->getName(), $this->text->getCode())->where($this->text->getId(), $id_warehouse)->get()->toArray();
        if (count($Warehouse) > 0) {
            return $Warehouse[0];
        }else{
            return null;
        }
    }

    /**
     * @param int $id_price
     * @param int $id_store
     * @param int $id_product
     */
    public function setProductPriceStore(int $id_price, int $id_store, int $id_product){
        try {
            $ProductPriceStore = new ProductPriceStore();
            $ProductPriceStore->id_price = $id_price;
            $ProductPriceStore->id_store = $id_store;
            $ProductPriceStore->id_product = $id_product;
            $ProductPriceStore->save();
        } catch (Exception $th) {
            throw new Exception($th->getMessage());
        }
    }

    /**
     * @param int $id_price
     * @param int $id_store
     * @param int $id_product
     */
    public function getProductPriceStore(int $id_store, int $id_product){
        $ProductPriceStore = ProductPriceStore::select($this->text->getIdPrice())->
        where($this->text->getIdStore(), $id_store)->where($this->text->getIdProduct(), $id_product)->get()->toArray();
        if (count($ProductPriceStore) > 0) {
            return $ProductPriceStore[0][$this->text->getIdPrice()];
        }else{
            return null;
        }
    }

    /**
     * @param float $price
     * @param float|null $special_price
     * @param string $from_date
     * @param string $to_date
     */
    public function setPrice(float $price, float|null $special_price, string $from_date, string $to_date){
        try {
            $Price = new Price();
            $Price->price = $price;
            $Price->special_price = $special_price;
            $Price->from_date = $from_date;
            $Price->to_date = $to_date;
            $Price->created_at = $this->date->getFullDate();
            $Price->updated_at = null;
            $Price->save();
        } catch (Exception $th) {
            throw new Exception($th->getMessage());
        }
    }

    /**
     * @param float $price
     * @param float|null $special_price
     * @param string $from_date
     * @param string $to_date
     * @return int|null
     */
    public function getPrice(float $price, float|null $special_price, string $from_date, string $to_date){
        $Price = Price::select($this->text->getId())->where($this->text->getPrice(), $price)->
        where($this->text->getSpecialPrice(), $special_price)->where($this->text->getFromDate(), $from_date)->
        where($this->text->getToDate(), $to_date)->get()->toArray();
        if (count($Price) > 0) {
            return $Price[0][$this->text->getId()];
        }else{
            return null;
        }
    }
    
    /**
     * @param int $id
     * @return array|null
     */
    public function getPriceProduct(int $id){
        $Price = Price::select($this->text->getPrice(), $this->text->getSpecialPrice())->where($this->text->getId(), $id)->get()->toArray();
        if (count($Price) > 0) {
            return $Price[0];
        }else{
            return null;
        }
    }

    /**
     * @param array $prices
     * @param int $id_product
     */
    public function setProductAllPrice(array $prices, int $id_product){
        foreach ($prices as $price) {
            $id_stores = $this->convertListToStore($price[$this->text->getListaPrecio()]);
            $this->loadPricesStores($id_product, $id_stores, $price);
        }
    }
    
    /**
     * @param int $id_product
     * @param array $id_stores
     * @param array $prices
     */
    public function loadPricesStores(int $id_product, array $id_stores, array $price){
        foreach ($id_stores as $id_store) {
            if($id_store != 0){
                $this->validatePriceProductStore($id_store, $id_product, $price[$this->text->getPrecio()], $price[$this->text->getDescuento()]);
            }
        }
    }

    /**
     * @param int $id_store
     * @param int $id_product
     * @param string $precio
     * @param string $descuento
     */
    public function validatePriceProductStore(int $id_store, int $id_product, string $precio, string $descuento){
        $id_price = $this->getProductPriceStore($id_store, $id_product);
        $_price = floatval($precio);
        $_special_price = floatval($precio)-floatval($descuento);
        if ($_price == $_special_price) {
            $_special_price=null;
        }
        $from_date = $this->date->getFullDate();
        $to_date = $this->date->addDateToDate($from_date, $this->text->getAddOneYear());
        if (is_null($id_price)) {
            $this->setPrice($_price, $_special_price, $from_date, $to_date);
            $id_price = $this->getPrice($_price, $_special_price, $from_date, $to_date);
            $this->setProductPriceStore($id_price, $id_store, $id_product);
        }else{
            $this->updatePriceByID($id_price, $_price, $_special_price, $from_date, $to_date);
        }
    }

    /**
     * @param array $product
     * @return int|null
     */
    private function getPriceAPI(array $product){
        return $this->getPrice(
            $product[$this->text->getPrice()],
            $product[$this->text->getSpecialPrice()],
            $product[$this->text->getFromDate()], 
            $product[$this->text->getToDate()]
        );
    }

    /**
     * @param array $product
     * @return void
     */
    private function setPriceAPI(array $product){
        $this->setPrice(
            $product[$this->text->getPrice()],
            $product[$this->text->getSpecialPrice()],
            $product[$this->text->getFromDate()], 
            $product[$this->text->getToDate()]
        );
    }

    /**
     * @param array $allStore
     * @param array $producto
     * @param Product $Producto
     */
    public function changePriceApi(array $allStore, array $product, Product $Producto){
        $id_price = $this->getPriceAPI($product);
        if (is_null($id_price)) {
            $this->setPriceAPI($product);
            $id_price = $this->getPriceAPI($product);
        }
        foreach ($product[$this->text->getStores()] as $key => $store) {
            if ($store == 0) {
                $this->priceAllStore($id_price, $allStore, $Producto->id);
            }else{
                $this->deleteProductPriceStore($store, $Producto->id);
                $this->setProductPriceStore($id_price, $store, $Producto->id);
            }
        }
    }

    /**
     * @param int $id_catalog
     * @param int $id_category
     * @param array $allStore
     * @param array $producto
     * @param Product $Producto
     */
    public function asignarCategory(int $id_catalog, int $id_category, array $allStore, array $product, Product $Producto){
        foreach ($product[$this->text->getStores()] as $key => $store) {
            if ($store == 0) {
                $this->asignarAllStore($id_catalog, $id_category, $allStore, $Producto->id);
            }else{
                $this->deleteProductCategoryCatalog($id_catalog, $store, $id_category, $Producto->id);
                $this->setProductCategoryCatalog($id_catalog, $Producto->id, $store, $id_category);
            }
        }
    }
    
    /**
     * @param int $id_catalog
     * @param int $id_category
     * @param array $allStore
     * @param array $producto
     * @param Product $Producto
     */
    public function desasignarCategory(int $id_catalog, int $id_category, array $allStore, array $product, Product $Producto){
        foreach ($product[$this->text->getStores()] as $key => $store) {
            if ($store == 0) {
                $this->deasignarAllStore($id_catalog, $id_category, $allStore, $Producto->id);
            }else{
                $this->deleteProductCategoryCatalog($id_catalog, $store, $id_category, $Producto->id);
            }
        }
    }
    
    /**
     * @param int $id_catalog
     * @param int $id_category
     * @param array $allStore
     * @param int $id_product
     */
    public function deasignarAllStore(int $id_catalog, int $id_category, array $allStore, int $id_product){
        foreach ($allStore as $key => $store) {
            $this->deleteProductCategoryCatalog($id_catalog, $store[$this->text->getId()], $id_category, $id_product);
        }
    }

    /**
     * @param int $id_catalog
     * @param int $id_category
     * @param array $allStore
     * @param int $id_product
     */
    public function asignarAllStore(int $id_catalog, int $id_category, array $allStore, int $id_product){
        foreach ($allStore as $key => $store) {
            $this->deleteProductCategoryCatalog($id_catalog, $store[$this->text->getId()], $id_category, $id_product);
            $this->setProductCategoryCatalog($id_catalog, $id_product, $store[$this->text->getId()], $id_category);
        }
    }

    /**
     * @param int $id_catalog
     * @param int $id_store
     * @param int $id_category
     * @param int $id_product
     */
    public function deleteProductCategoryCatalog(int $id_catalog, int $id_store, int $id_category, int $id_product){
        ProductCategory::where($this->text->getIdStore(), $id_store)->where($this->text->getIdCategory(), $id_category)->
        where($this->text->getIdCatalog(), $id_catalog)->where($this->text->getIdProduct(), $id_product)->delete();
    }

    /**
     * @param int $id_catalog
     * @param int $id_product
     * @param int $id_store
     * @param int $id_category
     */
    public function setProductCategoryCatalog(int $id_catalog, int $id_product, int $id_store, int $id_category){
        try {
            $ProductCategory = new ProductCategory();
            $ProductCategory->id_product = $id_product;
            $ProductCategory->id_store = $id_store;
            $ProductCategory->id_category = $id_category;
            $ProductCategory->id_catalog = $id_catalog;
            $ProductCategory->save();
        } catch (Exception $th) {
            throw new Exception($th->getMessage());
        }
    }

    /**
     * @param int $id_price
     * @param array $allStore
     * @param int $Producto_id
     */
    private function priceAllStore(int $id_price, array $allStore, int $Producto_id){
        foreach ($allStore as $key => $store) {
            $this->deleteProductPriceStore($store[$this->text->getId()], $Producto_id);
            $this->setProductPriceStore($id_price, $store[$this->text->getId()], $Producto_id);
        }
    }

    /**
     * @param int $id_price
     * @param int $id_store
     * @param int $id_product
     */
    public function updateProductPriceStore(int $id_price, int $id_store, int $id_product){
        ProductPriceStore::where($this->text->getIdPrice(), $id_price)->
        where($this->text->getIdStore(), $id_store)->where($this->text->getIdProduct(), $id_product)->update([
            $this->text->getIdPrice() => $id_price
        ]);
    }

    /**
     * @param int $id_store
     * @param int $id_product
     */
    public function deleteProductPriceStore(int $id_store, int $id_product){
        ProductPriceStore::where($this->text->getIdStore(), $id_store)->where($this->text->getIdProduct(), $id_product)->delete();
    }

    /**
     * @param int $id_price
     * @param float $price
     * @param float|null $special_price
     * @param string $from_date
     * @param string $to_date
     */
    public function updatePriceByID(int $id_price, float $price, float|null $special_price, string $from_date, string $to_date){
        Price::where($this->text->getId(), $id_price)->update([
            $this->text->getPrice() => $price,
            $this->text->getSpecialPrice() => $special_price,
            $this->text->getFromDate() => $from_date,
            $this->text->getToDate() => $to_date,
            $this->text->getUpdated() => $this->date->getFullDate()
        ]);
    }

    /**
     * @param array $clasificacion
     * @param bool $subcat
     * @param array $allStore
     * @param int $id_product
     */
    public function setClasificacion(array $clasificacion, bool $subcat, array $allStore, int $id_product){
        if (!is_null($clasificacion) && $clasificacion[$this->text->getCodigo()] != -1) {
            $id_cat_info = $this->getCategoryInfo($clasificacion[$this->text->getCodigo()], $subcat);
            if (is_null($id_cat_info)) {
                $this->setCategoryInfo($clasificacion[$this->text->getCodigo()], $subcat);
                $id_cat_info = $this->getCategoryInfo($clasificacion[$this->text->getCodigo()], $subcat);
            }
            $id_cat = $this->getCategory($clasificacion[$this->text->getNombre()], $clasificacion[$this->text->getCodigo()], $clasificacion[$this->text->getCodigoPadre()]);
            if (is_null($id_cat)) {
                $this->setCategory($clasificacion[$this->text->getNombre()], $clasificacion[$this->text->getCodigo()], $id_cat_info, $clasificacion[$this->text->getCodigoPadre()]);
                $id_cat = $this->getCategory($clasificacion[$this->text->getNombre()], $clasificacion[$this->text->getCodigo()], $clasificacion[$this->text->getCodigoPadre()]);
            }
            if (!is_null($clasificacion[$this->text->getClasificacion()])) {
                $this->setClasificacion($clasificacion[$this->text->getClasificacion()], true, $allStore, $id_product);
            }else{
                $this->setAllProductCategoryStore($id_product, $allStore, $id_cat);
            }
        }
    }

    /**
     * @param int $id_product
     * @param int $id_store
     * @param int $id_category
     */
    public function setProductCategory(int $id_product, int $id_store, int $id_category){
        try {
            $ProductCategory = new ProductCategory();
            $ProductCategory->id_product = $id_product;
            $ProductCategory->id_store = $id_store;
            $ProductCategory->id_category = $id_category;
            $ProductCategory->save();
        } catch (Exception $th) {
            throw new Exception($th->getMessage());
        }
    }

    /**
     * @param int $id_product
     * @param int $id_store
     * @param int $id_category
     */
    public function getProductCategory(int $id_product, int $id_store, int $id_category){
        $ProductCategory = ProductCategory::select($this->text->getIdProduct())->where($this->text->getIdProduct(), $id_product)->
        where($this->text->getIdStore(), $id_store)->where($this->text->getIdCategory(), $id_category)->get()->toArray();
        if (count($ProductCategory) > 0) {
            return true;
        }else{
            return false;
        }
    }

    /**
     * @param int $id_product
     * @param array $stores
     * @param int $id_category
     */
    public function setAllProductCategoryStore(int $id_product, array $stores, int $id_category){
        foreach ($stores as $store) {
            if (!$this->getProductCategory($id_product, $store[$this->text->getId()], $id_category)) {
                $this->setProductCategory($id_product, $store[$this->text->getId()], $id_category);
            }
        }
    }

    /**
     * @param int $id_pos
     * @param int $sub_category_pos
     */
    public function setCategoryInfo(int $id_pos, int $sub_category_pos){
        try {
            $CategoryInfo = new CategoryInfo();
            $CategoryInfo->show_filter = true;
            $CategoryInfo->id_pos = $id_pos;
            $CategoryInfo->sub_category_pos = $sub_category_pos;
            $CategoryInfo->id_picture = null;
            $CategoryInfo->id_content = null;
            $CategoryInfo->url = $this->text->getTextNone();
            $CategoryInfo->created_at = $this->date->getFullDate();
            $CategoryInfo->updated_at = null;
            $CategoryInfo->save();
        } catch (Exception $th) {
            throw new Exception($th->getMessage());
        }
    }

    /**
     * @param int $id_pos
     * @param int $sub_category_pos
     */
    public function getCategoryInfo(int $id_pos, int $sub_category_pos){
        $CategoryInfo = CategoryInfo::select($this->text->getId())->where($this->text->getIdPos(), $id_pos)->
        where($this->text->getPosSubCategory(), $sub_category_pos)->get()->toArray();
        if (count($CategoryInfo) > 0) {
            return $CategoryInfo[0][$this->text->getId()];
        }else{
            return null;
        }
    }

    /**
     * @param int $id_pos
     * @param int $sub_category_pos
     */
    public function getCategoryIdPos(int $id_pos){
        $Category = Category::select($this->text->getId())->where($this->text->getCode(), $id_pos)->get()->toArray();
        if (count($Category) > 0) {
            return $Category[0][$this->text->getId()];
        }else{
            return null;
        }
    }

    /**
     * @param string $name
     * @param string $code
     * @param int $id_info_category
     * @param int $inheritance
     */
    public function setCategory(string $name, string $code, int $id_info_category, int $inheritance){
        try {
            $Category = new Category();
            $Category->name = $name;
            $Category->name_pos = $name;
            $Category->code = $code;
            $Category->inheritance = $inheritance == 0 ? null : $this->getCategoryIdPos($inheritance);
            $Category->status = true;
            $Category->in_menu = true;
            $Category->id_info_category = $id_info_category;
            $Category->id_metadata = null;
            $Category->created_at = $this->date->getFullDate();
            $Category->updated_at = null;
            $Category->save();
        } catch (Exception $th) {
            throw new Exception($th->getMessage());
        }
    }

    /**
     * @param string $name_pos
     * @param string $code
     * @param int $inheritance
     */
    public function getCategory(string $name_pos, string $code, int $inheritance){
        $Category = Category::select($this->text->getId())->where($this->text->getNamePos(), $name_pos)
        ->where($this->text->getCode(), $code)->where($this->text->getInhitance(), $inheritance == 0 ? null : $this->getCategoryIdPos($inheritance))->get()->toArray();
        if (count($Category) > 0) {
            return $Category[0][$this->text->getId()];
        }else{
            return null;
        }
    }

    /**
     * @param int $idProduct
     * @param array $minicuotas
     * @return bool
     */
    public function changeMiniCuotas(int $idProduct, array $minicuotas){
        foreach ($minicuotas as $minicuota) {
            $id_stores = $this->convertListToStore($minicuota[$this->text->getListaPrecio()]);
            $id_minicuotas = $this->changeCuotas($minicuota[$this->text->getCuotas()]);
            $this->loadbyStores($idProduct, $id_stores, $id_minicuotas);
        }
    }

    /**
     * @param int $idProduct
     * @param array $id_stores
     * @param array $id_minicuotas
     */
    public function loadbyStores(int $idProduct, array $id_stores, array $id_minicuotas){
        foreach ($id_stores as $id_store) {
            if($id_store != 0){
                $this->loadbyStore($idProduct, $id_store, $id_minicuotas);
            }
        }
    }

    /**
     * @param int $idProduct
     * @param int $id_store
     * @param array $id_minicuotas
     */
    public function loadbyStore(int $idProduct, int $id_store, array $id_minicuotas){
        foreach ($id_minicuotas as $id_minicuota) {
            $this->setProductMinicuotaStore($idProduct, $id_store, $id_minicuota);
        }
    }

    /**
     * @param string $id_product
     * @param string $id_store
     * @param string $id_minicuota
     * @return bool
     */
    private function setProductMinicuotaStore(int $id_product, int $id_store, int $id_minicuota){
        try {
            if(!is_null($id_store) > 0 && !is_null($id_product) > 0 && !is_null($id_minicuota) > 0){    
                $MiniCuota = new ProductMinicuotaStore();
                $MiniCuota->id_store = $id_store;
                $MiniCuota->id_product = $id_product;
                $MiniCuota->id_minicuota = $id_minicuota;
                $MiniCuota->save();
                return true;
            }else{
                return false;
            }
        } catch (Exception $th) {
            throw new Exception($th->getMessage());
        }
    }

    /**
     * @param array $minicuotas
     * @return array
     */
    public function changeCuotas(array $minicuotas){
        $id_minicuotas = array();
        foreach ($minicuotas as $minicuota) {
            $id_minicuota = $this->getMiniCuota($minicuota[$this->text->getCuota()], $minicuota[$this->text->getMonto()]);
            if(is_null($id_minicuota)){
                if($this->setMiniCuota($minicuota[$this->text->getCuota()], $minicuota[$this->text->getMonto()])){
                    $id_minicuota = $this->getMiniCuota($minicuota[$this->text->getCuota()], $minicuota[$this->text->getMonto()]);
                }
            }
            if(!is_null($id_minicuota)){
                $id_minicuotas[] = $id_minicuota;
            }
        }
        return $id_minicuotas;
    }

    /**
     * @param int $listaPrecio
     * @return array
     */
    public function convertListToStore(int $listaPrecio){
        switch ($listaPrecio) {
            case SELF::OPLN_PRECIO_PROPUESTO:
                return [0];
            case SELF::OPLN_TIENDAS_SCZ:
                return [2,9];
            case SELF::OPLN_TIENDAS_CBA:
                return [3];
            case SELF::OPLN_TIENDAS_LPZ:
                return [1];
            case SELF::OPLN_TIENDAS_SCE:
                return [8];
            case SELF::OPLN_TIENDAS_TRJ:
                return [5];
        }
    }

    /**
     * @param string $name
     * @return array
     */
    public function convertListToStoreName(string $name){
        if (str_contains($name, SELF::NAME_SCZ)) {
            return [2,9];
        }else if (str_contains($name, SELF::NAME_CBA)) {
            return [3];
        }else if (str_contains($name, SELF::NAME_LPZ)) {
            return [1];
        }else if (str_contains($name, SELF::NAME_SCE)) {
            return [8];
        }else if (str_contains($name, SELF::NAME_TRJ)) {
            return [5];
        }else{
            return [0];
        }
    }

    /**
     * @param string $cuotas
     * @param string $monto
     * @return bool
     */
    private function setMiniCuota(string $cuotas, string $monto){
        try {
            if(strlen($cuotas) > 0 && strlen($monto) > 0){    
                $MiniCuota = new MiniCuota();
                $MiniCuota->meses = $cuotas;
                $MiniCuota->cuotas = $cuotas;
                $MiniCuota->monto = $monto;
                $MiniCuota->created_at = $this->date->getFullDate();
                $MiniCuota->updated_at = null;
                $MiniCuota->save();
                return true;
            }else{
                return false;
            }
        } catch (Exception $th) {
            throw new Exception($th->getMessage());
        }
    }

    /**
     * @param string $cuotas
     * @param string $monto
     */
    public function getMiniCuota(string $cuotas, string $monto){
        $MiniCuota = MiniCuota::select($this->text->getId())->where($this->text->getCuotas(), $cuotas)->where($this->text->getMonto(), $monto)->get()->toArray();
        if (count($MiniCuota) > 0) {
            return $MiniCuota[0][$this->text->getId()];
        }else{
            return null;
        }
    }

    /**
     * @param string $name
     * @return bool
     */
    private function setBrand(string $name){
        try {
            if(strlen($name) > 0){    
                $Brand = new Brand();
                $Brand->name = $name;
                $Brand->save();
                return true;
            }else{
                return false;
            }
        } catch (Exception $th) {
            throw new Exception($th->getMessage());
        }
    }

    /**
     * @param int $id
     */
    public function getBrandName(int $id){
        $Brand = Brand::select($this->text->getName())->where($this->text->getId(), $id)->get()->toArray();
        if (count($Brand) > 0) {
            return $Brand[0][$this->text->getName()];
        }else{
            return $this->text->getTextNone();
        }
    }

    /**
     * @param string $name
     */
    public function getBrand(string $name){
        $Brand = Brand::select($this->text->getId())->where($this->text->getName(), $name)->get()->toArray();
        if (count($Brand) > 0) {
            return $Brand[0][$this->text->getId()];
        }else{
            return null;
        }
    }
    
    /**
     * @param string $type
     * @return bool
     */
    private function setType(string $type){
        try {
            if(strlen($type)){
                $ProductType = new ProductType();
                $ProductType->type = $type;
                $ProductType->created_at = $this->date->getFullDate();
                $ProductType->updated_at = null;
                $ProductType->save();
                return true;
            }else{
                return false;
            }
        } catch (Exception $th) {
            throw new Exception($th->getMessage());
        }
    }

    /**
     * @param string $type
     */
    public function getType(string $type){
        $ProductType = ProductType::select($this->text->getId())->where($this->text->getType(), $type)->get()->toArray();
        if (count($ProductType) > 0) {
            return $ProductType[0][$this->text->getId()];
        }else{
            return null;
        }
    }
    
    /**
     * @param string|null $clacom
     * @return bool
     */
    private function setClacom(string|null $clacom){
        try {
            if(!is_null($clacom) && strlen($clacom) > 0){
                $Clacom = new Clacom();
                $Clacom->label = $clacom;
                $Clacom->code = str_replace($this->text->getSpace(), $this->text->getGuionBajo(), $clacom);
                $Clacom->id_picture = null;
                $Clacom->created_at = $this->date->getFullDate();
                $Clacom->updated_at = null;
                $Clacom->save();
                return true;
            }else{
                return false;
            }
        } catch (Exception $th) {
            throw new Exception($th->getMessage());
        }
    }

    /**
     * @param string|null $clacom
     * @return int|null
     */
    public function getClacom(string|null $clacom){
        if (is_null($clacom)) {
            return null;
        }
        $Clacom = Clacom::select($this->text->getId())->where($this->text->getLabel(), $clacom)->get()->toArray();
        if (count($Clacom) > 0) {
            return $Clacom[0][$this->text->getId()];
        }else{
            return null;
        }
    }

    /**
     * @param int $id
     */
    public function getClacomLabel(int $id){
        $Clacom = Clacom::select($this->text->getLabel())->where($this->text->getId(), $id)->get()->toArray();
        if (count($Clacom) > 0) {
            return $Clacom[0][$this->text->getLabel()];
        }else{
            return $this->text->getTextNone();
        }
    }

    /**
     * @param int $idProduct
     * @param array $stores
     * @param bool $status
     * @return bool
     */
    public function changeStatusProduct(int $idProduct, array $stores, bool $status){
        foreach ($stores as $store) {
            if(is_null($this->getProductStoreStatus($idProduct, $store[$this->text->getId()]))){
                $this->setProductStoreStatus($idProduct, $store[$this->text->getId()], $status);
            }else{
                $this->updateProductStoreStatus($idProduct, $store[$this->text->getId()], $status);
            }
        }
    }

    /**
     * @param array $stores
     * @param int $id_store
     */
    public function readAllStore(array $stores, int $id_store){
        foreach ($stores as $store) {
            if ($store[$this->text->getId()] == $id_store) {
                return $store[$this->text->getName()];
            }
        }
        return $this->text->getTextNone();
    }

    /**
     * @return array
     */
    public function getAllStoreID(){
        return Store::select($this->text->getId())->get()->toArray();
    }
    
    /**
     * @param array
     */
    public function getAllStore(){
        return Store::all()->toArray();
    }

    /**
     * @param Store[]
     */
    public function getAllStoreEntity(){
        return Store::all();
    }

    /**
     * @param string $id_product
     * @param string $id_store
     * @param string $status
     * @return bool
     */
    public function setProductStoreStatus(int $id_product, int $id_store, bool $status){
        try {
            if(!is_null($id_product) && !is_null($id_store)){
                $ProductStoreStatus = new ProductStoreStatus();
                $ProductStoreStatus->id_product = $id_product;
                $ProductStoreStatus->id_store = $id_store;
                $ProductStoreStatus->status = $status;
                $ProductStoreStatus->save();
                return true;
            }else{
                return false;
            }
        } catch (Exception $th) {
            throw new Exception($th->getMessage());
        }
    }

    /**
     * @param int $id_product
     * @param int $id_store
     */
    public function getProductStoreStatus(int $id_product, int $id_store){
        $ProductStoreStatus = ProductStoreStatus::select($this->text->getIdProduct())
        ->where($this->text->getIdProduct(), $id_product)->where($this->text->getIdStore(), $id_store)->get()->toArray();
        if (count($ProductStoreStatus) > 0) {
            return $ProductStoreStatus[0][$this->text->getIdProduct()];
        }else{
            return null;
        }
    }
    
    /**
     * @param int $id_product
     * @return array|null
     */
    public function getProductStatus(int $id_product){
        $ProductStoreStatus = ProductStoreStatus::where($this->text->getIdProduct(), $id_product)->get()->toArray();
        if (count($ProductStoreStatus) > 0) {
            return $ProductStoreStatus;
        }else{
            return null;
        }
    }

    /**
     * @param string $id_product
     * @param string $id_store
     * @param string $status
     */
    public function updateProductStoreStatus(int $id_product, int $id_store, bool $status){
        ProductStoreStatus::where($this->text->getIdProduct(), $id_product)->where($this->text->getIdStore(), $id_store)->update([
            $this->text->getIdProduct() => $id_product,
            $this->text->getIdStore() => $id_store,
            $this->text->getStatus() => $status
        ]);
    }

    /**
     * @param string $sku
     * @return Product
     */
    public function getProductBySku(string $sku){
        $product = Product::where($this->text->getSku(), $sku)->first();
        if (!$product) {
            throw new Exception($this->text->getNoneSku($sku));
        }
        return $product;
    }

    /**
     * @param int $id
     * @return Product
     */
    public function getProductById(int $id){
        $product = Product::where($this->text->getId(), $id)->first();
        if (!$product) {
            throw new Exception($this->text->getNoneIdProduct($id));
        }
        return $product;
    }

    /**
     * @param int $id
     * @return array
     */
    public function getProductArray(int $id){
        $Product = $this->getProductById($id);
        $Stores = $this->getAllStoreEntity();
        //stock
        return [
            $this->text->getId() => $Product->id,
            $this->text->getName() => $Product->name,
            $this->text->getSku() => $Product->sku,
            $this->text->getBrand() => $Product->Brand,
            $this->text->getClacom() => $Product->Clacom,
            $this->text->getType() => $Product->Type,
            $this->text->getAttributes() => $this->getAttributesInProduct($Product->Attributes),
            $this->text->getMedidaComercial() => $Product->MedidasComerciales,
            $this->text->getCuotaInicial() => $this->cuotaInicial($Stores, $Product->CuotaInicial),
            $this->text->getPartner() => $Product->Partner,
            $this->text->getPrices() => $this->pricesProducts($Stores, $Product->PriceStore),
            $this->text->getMinicuotas() => $this->minicuotasProducts($Stores, $Product->MinicuotaStore),
            $this->text->getCategorias() => $this->categoriasProducts($Product->Categorys->unique()),
            $this->text->getSheets() => $this->getProductSheet($Product->Sheet),
            $this->text->getWarehouses() => $this->getProductWarehouses($Stores, $Product->Warehouse)
        ];
    }

    private function getProductSheet($Sheets){
        $Sheet = array();
        foreach ($Sheets as $key => $Sheet) {
            $Sheet[] = $Sheet->DataSheet;
        }
        return $Sheet;
    }
    private function getProductWarehouses($Stores, $Warehouses){
        $Warehouse = array();
        foreach ($Stores as $key => $store) {
            $Warehouse[] = array(
                $this->text->getIdStore() => $store->id,
                $this->text->getStoreName() => $store->name,
                $this->text->getWarehouse() => $this->warehouseStoreProduct($store->id, $Warehouses)
            );
        }
        return $Warehouse;
    }

    private function warehouseStoreProduct($store_id, $Warehouses){
        $Warehouses_Array = array();
        foreach ($Warehouses as $key => $Warehouse) {
            if ($store_id == $Warehouse->id_store) {
                $WarehouseDB = $Warehouse->Warehouse;
                $WarehouseDB[$this->text->getStock()] = $Warehouse->stock;
                $Warehouses_Array[] = $WarehouseDB;
            }
        }
        return $Warehouses_Array;
    }

    private function getCustomValueAttribute($Attribute){
        return array(
            $this->text->getId() => $Attribute->id,
            $this->text->getName() => $Attribute->name,
            $this->text->getCode() => $Attribute->code,
            $this->text->getLabel() => $Attribute->label,
            $this->text->getType() => $Attribute->Type
        );
    }

    private function getAttributesInProduct($Attributes){
        $attributes_Array = array();
        foreach ($Attributes as $key => $Attribute) {
            $attributes_Array[] = array(
                $this->text->getValue() => $Attribute->value,
                $this->text->getCustom() => $this->getCustomValueAttribute($Attribute->Attribute)
            );
        }
        return $attributes_Array;
    }

    private function categoriasProducts($Categorys){
        $Categorias = array();
        foreach ($Categorys as $key => $Category) {
            $Categorias[] = $Category->Category;
        }
        return $Categorias;
    }

    private function minicuotasProducts($stores, $MinicuotaStores){
        $MinicuotaStore = array();
        foreach ($stores as $key => $store) {
            $MinicuotaStore[] = array(
                $this->text->getIdStore() => $store->id,
                $this->text->getStoreName() => $store->name,
                $this->text->getPrice() => $this->minicuotaStorePrice($store->id, $MinicuotaStores)
            );
        }
        return $MinicuotaStore;
    }
    
    private function minicuotaStorePrice($store_id, $MinicuotaStores){
        foreach ($MinicuotaStores as $key => $PriceStore) {
            if ($store_id == $PriceStore->id_store) {
                return $PriceStore->MiniCuota;
            }
        }
        return 0;
    }

    private function pricesProducts($stores, $PriceStores){
        $priceProduct = array();
        foreach ($stores as $key => $store) {
            $priceProduct[] = array(
                $this->text->getIdStore() => $store->id,
                $this->text->getStoreName() => $store->name,
                $this->text->getPrice() => $this->existStorePrice($store->id, $PriceStores)
            );
        }
        return $priceProduct;
    }

    private function existStorePrice($store_id, $PriceStores){
        foreach ($PriceStores as $key => $PriceStore) {
            if ($store_id == $PriceStore->id_store) {
                return $PriceStore->Price;
            }
        }
        return 0;
    }

    private function cuotaInicial($stores, $CuotasIniciales){
        $cuotasInicial = array();
        foreach ($stores as $key => $store) {
            $cuotasInicial[] = array(
                $this->text->getIdStore() => $store->id,
                $this->text->getStoreName() => $store->name,
                $this->text->getMonto() => $this->existStoreInicial($store->id, $CuotasIniciales)
            );
        }
        return $cuotasInicial;
    }

    private function existStoreInicial($store_id, $CuotasIniciales){
        foreach ($CuotasIniciales as $key => $cuotaInicial) {
            if ($store_id == $cuotaInicial->id_store) {
                return $cuotaInicial->inicial;
            }
        }
        return 0;
    }
}
?>
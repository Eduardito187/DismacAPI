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
use \Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductApi{
    CONST FILTER_ALL = "ALL";
    CONST FILTER_LAST_EDIT = "LAST_EDIT";
    CONST FILTER_LAST_CREATE = "LAST_CREATE";

    CONST OPLN_PRECIO_PROPUESTO = 1;
    CONST OPLN_TIENDAS_SCZ = 3;
    CONST OPLN_TIENDAS_LPZ = 4;
    CONST OPLN_TIENDAS_CBA = 5;
    CONST OPLN_TIENDAS_TRJ = 22;
    CONST OPLN_TIENDAS_SCE = 23;
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
     * @param string $name
     * @return int|null
     */
    private function getCatalogStore(string $code, string $name){
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
     * @param string $id_brand
     * @param string $id_clacom
     * @param string $id_type
     * @param int $id_Account
     */
    private function setProduct(string $code, string $name, string $id_brand, string $id_clacom, string $id_type, int $id_Account){
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
            Log::debug("ERROR #12");
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
            Log::debug("ERROR #13");
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
            Log::debug("ERROR #14");
            throw new Exception($this->text->getNoneFilter());
        }
    }
    
    /**
     * @param string $filter
     * @return string
     */
    public function getValueFilter(string $filter){
        if ($filter == SELF::FILTER_LAST_CREATE) {
            return "DESC";
        }else if ($filter == SELF::FILTER_LAST_EDIT) {
            return "DESC";
        }else{
            Log::debug("ERROR #15");
            throw new Exception($this->text->getNoneFilter());
        }
    }

    /**
     * @param int $id
     * @param string $code
     * @param string $name
     * @param string $id_brand
     * @param string $id_clacom
     * @param string $id_type
     * @param int $id_Account
     */
    private function updateProductALL(int $id, string $code, string $name, string $id_brand, string $id_clacom, string $id_type, int $id_Account){
        Product::where('id', $id)->update([
            "name" => $name,
            "id_brand" => $id_brand,
            "id_clacom" => $id_clacom,
            "id_type" => $id_type,
            "updated_at" => $this->date->getFullDate(),
            "id_partner" => $id_Account
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
        return DB::table('product')->where(
            "id_partner", $partnerID
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
     * @param string $id_brand
     * @param string $id_clacom
     * @param string $id_type
     * @param int $id_Account
     */
    private function updateProductRelations(int $id, string $code, string $name, string $id_brand, string $id_clacom, string $id_type, int $id_Account){
        Product::where('id', $id)->update([
            "id_brand" => $id_brand,
            "id_clacom" => $id_clacom,
            "id_type" => $id_type,
            "updated_at" => $this->date->getFullDate(),
            "id_partner" => $id_Account
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
            Log::debug("sku => ".$res["codigo"]);
            Log::debug("----0----");
            $id_product = $this->getCatalogStore($res["codigo"], $res["nombre"]);
            $id_brand = null;
            $id_type = null;
            $id_clacom = null;
            Log::debug("----1----");
            if (!empty($res["marca"]) && is_array($res["marca"])) {
                $id_brand = $this->getBrand($res["marca"]["nombre"]);
                if (is_null($id_brand)) {
                    if($this->setBrand($res["marca"]["nombre"])){
                        $id_brand = $this->getBrand($res["marca"]["nombre"]);
                    }
                }
            }
            Log::debug("----2----");
            if (!empty($res["detalle"]) && is_array($res["detalle"])) {
                $id_type = $this->getType($res["detalle"]["tipoProducto"]);
                $id_clacom = $this->getClacom($res["detalle"]["clacom"]);
                if (is_null($id_type)) {
                    if($this->setType($res["detalle"]["tipoProducto"])){
                        $id_type = $this->getType($res["detalle"]["tipoProducto"]);
                    }
                }
                if (is_null($id_clacom)) {
                    if($this->setClacom($res["detalle"]["clacom"])){
                        $id_clacom = $this->getClacom($res["detalle"]["clacom"]);
                    }
                }
            }
            Log::debug("----3----");
            if (is_null($id_product)) {
                $this->setProduct(
                    $res["codigo"],
                    $res["nombre"],
                    $id_brand,
                    $id_clacom,
                    $id_type,
                    $id_Account
                );
                $id_product = $this->getCatalogStore(
                    $res["codigo"],
                    $res["nombre"]
                );
                $this->updateProductRelations(
                    $id_product,
                    $res["codigo"],
                    $res["nombre"],
                    $id_brand,
                    $id_clacom,
                    $id_type,
                    $id_Account
                );
            }else{
                $this->updateProductALL(
                    $id_product,
                    $res["codigo"],
                    $res["nombre"],
                    $id_brand,
                    $id_clacom,
                    $id_type,
                    $id_Account
                );
            }
            Log::debug("----4----");
            if (!empty($res["minicuotas"]) && is_array($res["minicuotas"])) {
                $this->changeMiniCuotas($id_product, $res["minicuotas"]);
            }
            Log::debug("----5----");
            if (!empty($res["estado"]) && is_array($res["estado"])) {
                $this->changeStatusProduct($id_product, $allStore, $res["estado"]["visible"]);
            }
            Log::debug("----6----");
            if (!empty($res["clasificacion"]) && is_array($res["clasificacion"])) {
                $this->setClasificacion($res["clasificacion"], false, $allStore, $id_product);
            }
            Log::debug("----7----");
            if (!empty($res["precios"]) && is_array($res["precios"])) {
                $this->setProductAllPrice($res["precios"], $id_product);
            }
            Log::debug("----8----");
        }
        Log::debug("FIN => IMPORT");
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
            Log::debug("ERROR #1");
            throw new Exception($th->getMessage());
        }
    }

    /**
     * @param int $id_price
     * @param int $id_store
     * @param int $id_product
     */
    public function getProductPriceStore(int $id_store, int $id_product){
        $ProductPriceStore = ProductPriceStore::select("id_price")->
        where("id_store", $id_store)->where("id_product", $id_product)->get()->toArray();
        if (count($ProductPriceStore) > 0) {
            return $ProductPriceStore[0]["id_price"];
        }else{
            return null;
        }
    }

    /**
     * @param float $price
     * @param float $special_price
     * @param string $from_date
     * @param string $to_date
     */
    public function setPrice(float $price, float $special_price, string $from_date, string $to_date){
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
            Log::debug("ERROR #2");
            throw new Exception($th->getMessage());
        }
    }

    /**
     * @param float $price
     * @param float $special_price
     * @param string $from_date
     * @param string $to_date
     */
    public function getPrice(float $price, float $special_price, string $from_date, string $to_date){
        $Price = Price::select($this->text->getId())->where("price", $price)->
        where("special_price", $special_price)->where("from_date", $from_date)->where("to_date", $to_date)->get()->toArray();
        if (count($Price) > 0) {
            return $Price[0][$this->text->getId()];
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
            $id_stores = $this->convertListToStore($price["listaPrecio"]);
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
                $this->validatePriceProductStore($id_store, $id_product, $price["precio"], $price["descuento"]);
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
        $from_date = $this->date->getFullDate();
        $to_date = $this->date->addDateToDate($from_date, ' + 1 years');
        if (is_null($id_price)) {
            $this->setPrice($_price, $_special_price, $from_date, $to_date);
            $id_price = $this->getPrice($_price, $_special_price, $from_date, $to_date);
            $this->setProductPriceStore($id_price, $id_store, $id_product);
        }else{
            $this->updatePriceByID($id_price, $_price, $_special_price, $from_date, $to_date);
        }
    }

    /**
     * @param int $id_price
     * @param float $price
     * @param float $special_price
     * @param string $from_date
     * @param string $to_date
     */
    public function updatePriceByID(int $id_price, float $price, float $special_price, string $from_date, string $to_date){
        Price::where($this->text->getId(), $id_price)->update([
            "price" => $price,
            "special_price" => $special_price,
            "from_date" => $from_date,
            "to_date" => $to_date
        ]);
    }

    /**
     * @param array $clasificacion
     * @param bool $subcat
     * @param array $allStore
     * @param int $id_product
     */
    public function setClasificacion(array $clasificacion, bool $subcat, array $allStore, int $id_product){
        Log::debug("setClasificacion ERROR #1");
        if (!is_null($clasificacion) && $clasificacion["codigo"] != -1) {
            Log::debug("setClasificacion ERROR #2");
            $id_cat_info = $this->getCategoryInfo($clasificacion["codigo"], $subcat);
            Log::debug("setClasificacion ERROR #3");
            if (is_null($id_cat_info)) {
                Log::debug("setClasificacion ERROR #4");
                $this->setCategoryInfo($clasificacion["codigo"], $subcat);
                Log::debug("setClasificacion ERROR #5");
                $id_cat_info = $this->getCategoryInfo($clasificacion["codigo"], $subcat);
                Log::debug("setClasificacion ERROR #6");
            }
            $id_cat = $this->getCategory($clasificacion["nombre"], $clasificacion["codigo"], $clasificacion["codigoPadre"]);
            Log::debug("setClasificacion ERROR #7");
            if (is_null($id_cat)) {
                Log::debug("setClasificacion ERROR #8");
                $this->setCategory($clasificacion["nombre"], $clasificacion["codigo"], $id_cat_info, $clasificacion["codigoPadre"]);
                Log::debug("setClasificacion ERROR #8");
                $id_cat = $this->getCategory($clasificacion["nombre"], $clasificacion["codigo"], $clasificacion["codigoPadre"]);
                Log::debug("setClasificacion ERROR #10");
            }
            if (!is_null($clasificacion["clasificacion"])) {
                Log::debug("setClasificacion ERROR #11");
                $this->setClasificacion($clasificacion["clasificacion"], true, $allStore, $id_product);
                Log::debug("setClasificacion ERROR #12");
            }else{
                $this->setAllProductCategoryStore($id_product, $allStore, $id_cat);
                Log::debug("setClasificacion ERROR #13");
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
            Log::debug("ERROR #3");
            throw new Exception($th->getMessage());
        }
    }

    /**
     * @param int $id_product
     * @param int $id_store
     * @param int $id_category
     */
    public function getProductCategory(int $id_product, int $id_store, int $id_category){
        $ProductCategory = ProductCategory::select("id_product")->where("id_product", $id_product)->
        where("id_store", $id_store)->where("id_category", $id_category)->get()->toArray();
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
            $CategoryInfo->url = "";
            $CategoryInfo->created_at = $this->date->getFullDate();
            $CategoryInfo->updated_at = null;
            $CategoryInfo->save();
        } catch (Exception $th) {
            Log::debug("ERROR #4");
            throw new Exception($th->getMessage());
        }
    }

    /**
     * @param int $id_pos
     * @param int $sub_category_pos
     */
    public function getCategoryInfo(int $id_pos, int $sub_category_pos){
        $CategoryInfo = CategoryInfo::select($this->text->getId())->where("id_pos", $id_pos)->
        where("sub_category_pos", $sub_category_pos)->get()->toArray();
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
        $Category = Category::select($this->text->getId())->where("code", $id_pos)->get()->toArray();
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
            Log::debug("ERROR #5");
            throw new Exception($th->getMessage());
        }
    }

    /**
     * @param string $name_pos
     * @param string $code
     * @param int $inheritance
     */
    public function getCategory(string $name_pos, string $code, int $inheritance){
        $Category = Category::select($this->text->getId())->where("name_pos", $name_pos)
        ->where("code", $code)->where("inheritance", $inheritance == 0 ? null : $this->getCategoryIdPos($inheritance))->get()->toArray();
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
            $id_stores = $this->convertListToStore($minicuota["listaPrecio"]);
            $id_minicuotas = $this->changeCuotas($minicuota["cuotas"]);
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
            Log::debug("ERROR #6");
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
            $id_minicuota = $this->getMiniCuota($minicuota["cuota"], $minicuota["monto"]);
            if(is_null($id_minicuota)){
                if($this->setMiniCuota($minicuota["cuota"], $minicuota["monto"])){
                    $id_minicuota = $this->getMiniCuota($minicuota["cuota"], $minicuota["monto"]);
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
            Log::debug("ERROR #7");
            throw new Exception($th->getMessage());
        }
    }

    /**
     * @param string $cuotas
     * @param string $monto
     */
    public function getMiniCuota(string $cuotas, string $monto){
        $MiniCuota = MiniCuota::select($this->text->getId())->where("cuotas", $cuotas)->where("monto", $monto)->get()->toArray();
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
            Log::debug("ERROR #8");
            throw new Exception($th->getMessage());
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
            Log::debug("ERROR #9");
            throw new Exception($th->getMessage());
        }
    }

    /**
     * @param string $type
     */
    public function getType(string $type){
        $ProductType = ProductType::select($this->text->getId())->where("type", $type)->get()->toArray();
        if (count($ProductType) > 0) {
            return $ProductType[0][$this->text->getId()];
        }else{
            return null;
        }
    }
    
    /**
     * @param string $clacom
     * @return bool
     */
    private function setClacom(string $clacom){
        try {
            if(strlen($clacom) > 0){
                $Clacom = new Clacom();
                $Clacom->label = $clacom;
                $Clacom->code = str_replace(" ", "_", $clacom);
                $Clacom->id_picture = null;
                $Clacom->created_at = $this->date->getFullDate();
                $Clacom->updated_at = null;
                $Clacom->save();
                return true;
            }else{
                return false;
            }
        } catch (Exception $th) {
            Log::debug("ERROR #10");
            throw new Exception($th->getMessage());
        }
    }

    /**
     * @param string $clacom
     */
    public function getClacom(string $clacom){
        $Clacom = Clacom::select($this->text->getId())->where("label", $clacom)->get()->toArray();
        if (count($Clacom) > 0) {
            return $Clacom[0][$this->text->getId()];
        }else{
            return null;
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
            if(is_null($this->getProductStoreStatus($idProduct, $store["id"]))){
                $this->setProductStoreStatus($idProduct, $store["id"], $status);
            }else{
                $this->updateProductStoreStatus($idProduct, $store["id"], $status);
            }
        }
    }

    /**
     * @param array
     */
    public function getAllStoreID(){
        return Store::select($this->text->getId())->get()->toArray();
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
            Log::debug("ERROR #11");
            throw new Exception($th->getMessage());
        }
    }

    /**
     * @param int $id_product
     * @param int $id_store
     */
    public function getProductStoreStatus(int $id_product, int $id_store){
        $ProductStoreStatus = ProductStoreStatus::select("id_product")
        ->where("id_product", $id_product)->where("id_store", $id_store)->get()->toArray();
        if (count($ProductStoreStatus) > 0) {
            return $ProductStoreStatus[0]["id_product"];
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
        ProductStoreStatus::where('id_product', $id_product)->where('id_store', $id_store)->update([
            "id_product" => $id_product,
            "id_store" => $id_store,
            "status" => $status
        ]);
    }
}

?>
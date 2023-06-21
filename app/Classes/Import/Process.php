<?php

namespace App\Classes\Import;

use App\Models\Attribute;
use App\Classes\Helper\Text;
use App\Models\Product;
use \Exception;
use App\Classes\Helper\Types;
use App\Classes\Helper\Date;
use App\Models\Brand;
use App\Models\Clacom;
use App\Models\MedidasComerciales;
use App\Models\ProductAttribute;
use App\Models\ProductDescription;
use App\Models\ProductType;
use App\Models\Warehouse;
use App\Classes\Product\ProductApi;

class Process{
    const PRODUCT = "Product";
    const STOCK = "Stock";
    const ESTADOS = "Estados";
    const CATEGORIZAR = "Categorizar";
    const PRECIOS = "Precios";
    const DEFAULT_ENABLE = 1;
    /**
     * @var array
     */
    protected $Headers = [];
    /**
     * @var array
     */
    protected $Data = [];
    /**
     * @var array
     */
    protected $Attributes = [];
    /**
     * @var string
     */
    protected $Type;
    /**
     * @var Text
     */
    protected $Text;
    /**
     * @var array|null
     */
    protected $Current_Row = null;
    /**
     * @var Types
     */
    protected $Types;
    /**
     * @var Date
     */
    protected $Date;
    /**
     * @var array
     */
    protected $WarehouseCentral = [];
    /**
     * @var ProductApi
     */
    protected $ProductApi;
    /**
     * @var array
     */
    protected $Stores = [];

    public function __construct() {
        $this->Date       = new Date();
        $this->Text       = new Text();
        $this->Types      = new Types();
        $this->ProductApi = new ProductApi();
    }

    /**
     * @return void
     */
    public function getAllStore(){
        $this->Stores = $this->ProductApi->getAllStoreEntity();
    }

    /**
     * @param string $type
     * @return void
     */
    public function setType(string $type){
        $this->Type = $type;
    }

    /**
     * @return Warehouse[]
     */
    public function getWarehouseCentral(){
        return Warehouse::where($this->Text->getBase(), self::DEFAULT_ENABLE)->get();
    }

    /**
     * @return void
     */
    public function setProductBaseWarehouse(){
        $WH = $this->getWarehouseCentral();
        $this->processWarehouseCentral($WH);
    }

    /**
     * @param mixed $WH
     * @return void
     */
    public function processWarehouseCentral(mixed $WH){
        foreach ($WH as $key => $warehouse) {
            if ($warehouse->base == self::DEFAULT_ENABLE){
                $this->WarehouseCentral[] = $warehouse;
            }
        }
    }

    /**
     * @param string $sku
     * @param int $id_Partner
     * @return Product
     */
    public function getProductBySkuPartner(string $sku, int $id_Partner){
        $product = Product::where($this->Text->getSku(), $sku)->where($this->Text->getIdPartner(), $id_Partner)->first();
        if (!$product) {
            throw new Exception($this->Text->getNoneSku($sku));
        }
        return $product;
    }

    /**
     * @return void
     */
    public function loadAttributes(){
        if ($this->Type == self::PRODUCT) {
            $this->Attributes = $this->loadAttributesProduct();
            $this->setProductAttribute();
        } else if ($this->Type == self::STOCK) {
            $this->Attributes = $this->loadStockProduct();
        } else if ($this->Type == self::ESTADOS) {
            $this->Attributes = $this->loadStatusProduct();
        } else if ($this->Type == self::CATEGORIZAR) {
            $this->Attributes = $this->loadCategoryProduct();
        } else if ($this->Type == self::PRECIOS) {
            $this->Attributes = $this->loadPricesProduct();
        }
        $this->getAllStore();
        $this->setProductBaseWarehouse();
    }

    public function setAllStore(){
        $this->ProductApi->getAllStoreEntity();
    }

    /**
     * @return array
     */
    public function loadPricesProduct(){
        return array(
            $this->Text->getSku() => $this->Text->getString(),
            $this->Text->getPrice() => $this->Text->getFloat(),
            $this->Text->getSpecialPrice() => $this->Text->getFloat(),
            $this->Text->getStore() => $this->Text->getString()
        );
    }

    /**
     * @return array
     */
    public function loadCategoryProduct(){
        return array(
            $this->Text->getSku() => $this->Text->getString(),
            $this->Text->getCategory() => $this->Text->getInt(),
            $this->Text->getStore() => $this->Text->getString()
        );
    }

    /**
     * @return array
     */
    public function loadStatusProduct(){
        return array(
            $this->Text->getSku() => $this->Text->getString(),
            $this->Text->getStatus() => $this->Text->getBool(),
            $this->Text->getStore() => $this->Text->getString()
        );
    }

    /**
     * @return array
     */
    public function loadAttributesProduct(){
        return array(
            $this->Text->getSku() => $this->Text->getString(),
            $this->Text->getName() => $this->Text->getString(),
            $this->Text->getBrand() => $this->Text->getString(),
            $this->Text->getClacom() => $this->Text->getString(),
            $this->Text->getType() => $this->Text->getString(),
            $this->Text->getDescription() => $this->Text->getString(),
            $this->Text->getLongitude() => $this->Text->getString(),
            $this->Text->getWidth() => $this->Text->getString(),
            $this->Text->getHeight() => $this->Text->getString(),
            $this->Text->getWeight() => $this->Text->getString(),
            $this->Text->getVolume() => $this->Text->getString()
        );
    }

    /**
     * @return array
     */
    public function loadStockProduct(){
        return array(
            $this->Text->getSku() => $this->Text->getString(),
            $this->Text->getWarehouse() => $this->Text->getInt(),
            $this->Text->getStock() => $this->Text->getInt(),
            $this->Text->getStore() => $this->Text->getString()
        );
    }

    /**
     * @return void
     */
    public function setProductAttribute(){
        $Attributes = $this->getAllAtributeProduct();
        foreach ($Attributes as $key => $Attribute) {
            $this->Attributes[$Attribute->code] = $Attribute->Type->type;
        }
    }

    /**
     * @return Attribute[]
     */
    public function getAllAtributeProduct(){
        return Attribute::all();
    }

    /**
     * @param string $key
     * @return bool
     */
    public function ifExistKey(string $key){
        if (array_key_exists($key, $this->Attributes)){
            return true;
        }
        return false;
    }

    /**
     * @param string $key
     * @param int $index
     * @return bool
     */
    public function setStructure(string $key, int $index){
        if (array_key_exists($key, $this->Attributes)){
            $this->Headers[$key] = array(
                $this->Text->getIndex() => $index,
                $this->Text->getType() => $this->Attributes[$key],
                $this->Text->getCode() => $key
            );
            return true;
        }
        return false;
    }

    /**
     * @param int $index
     * @return array|null
     */
    public function existIndex(int $index){
        foreach ($this->Headers as $key => $value) {
            if ($value[$this->Text->getIndex()] == $index){
                return $value;
            }
        }
        return null;
    }

    /**
     * @param int $id
     * @param string $sku
     * @param int $index
     * @return int
     */
    public function createRow(int $id, string $sku, int $index){
        $Index = $this->existIndex($index);
        if (!is_null($Index)) {
            if ($this->Types->validateType($Index[$this->Text->getType()], $sku)){
                $this->Current_Row = array(
                    $this->Text->getId() => $id,
                    $this->Text->getSku() => $sku,
                    $this->Text->getData() => []
                );
                return 2;
            }else{
                $this->Current_Row = null;
                return 1;
            }
        }else{
            $this->Current_Row = null;
            return 0;
        }
    }

    /**
     * @param string $value
     * @param int $index
     */
    public function setDataBody(string $value, int $index){
        if (!is_null($this->Current_Row)){
            $Index = $this->existIndex($index);
            if (!is_null($Index)) {
                if ($this->Types->validateType($Index[$this->Text->getType()], $value)){
                    $this->Current_Row[$this->Text->getData()][] = array(
                        $this->Text->getType() => $Index[$this->Text->getType()],
                        $this->Text->getValue() => $this->Types->convertType($Index[$this->Text->getType()], $value),
                        $this->Text->getCode() => $Index[$this->Text->getCode()]
                    );
                }
            }
        }
    }

    /**
     * @return void
     */
    public function setDataQuery(){
        if (!is_null($this->Current_Row)){
            $this->Data[] = $this->Current_Row;
        }
    }

    /**
     * @return int
     */
    public function validateSku(string $sku, int $id_Partner){
        try {
            return $this->getProductBySkuPartner($sku, $id_Partner)->id;
        } catch (\Throwable $th) {
            return 0;
        }
    }

    /**
     * @return void
     */
    public function saveProcess(){
        foreach ($this->Data as $key => $row) {
            $this->changeRow($row);
        }
    }

    /**
     * @param array $row
     * @return void
     */
    public function changeRow(array $row){
        if ($this->Type == self::PRODUCT) {
            $defaultValues = $this->loadAttributesProduct();
            $this->updateProduct($defaultValues, $row[$this->Text->getData()], $row[$this->Text->getId()]);
        } else if ($this->Type == self::STOCK) {
            $defaultValues = $this->loadStockProduct();
            $this->updateStock($defaultValues, $row[$this->Text->getData()], $row[$this->Text->getId()]);
        } else if ($this->Type == self::ESTADOS) {
            $defaultValues = $this->loadStatusProduct();
            $this->updateStates($defaultValues, $row[$this->Text->getData()], $row[$this->Text->getId()]);
        } else if ($this->Type == self::CATEGORIZAR) {
            $defaultValues = $this->loadCategoryProduct();
            $this->updateCategory($defaultValues, $row[$this->Text->getData()], $row[$this->Text->getId()]);
        } else if ($this->Type == self::PRECIOS) {
            $defaultValues = $this->loadPricesProduct();
            $this->updatePrices($defaultValues, $row[$this->Text->getData()], $row[$this->Text->getId()]);
        }
    }

    /**
     * @param int $id
     * @return Product
     */
    public function getProductId(int $id){
        $product = Product::find($id);
        if (!$product) {
            throw new Exception($this->Text->getProductNone());
        }
        return $product;
    }

    /**
     * @param array $defaultValues
     * @param array $row
     * @param int $id_product
     * @return void
     */
    public function updateProduct(array $defaultValues, array $row, int $id_product){
        foreach ($row as $key => $data) {
            $code = $data[$this->Text->getCode()];
            $value = $data[$this->Text->getValue()];
            if ($code == $this->Text->getName()){
                $this->updateProductAttribute($id_product, $value, $this->Text->getName());
            }else if ($code == $this->Text->getBrand()){
                $this->updateProductBrand($id_product, $value);
            }else if ($code == $this->Text->getClacom()){
                $this->updateProductClacom($id_product, $value);
            }else if ($code == $this->Text->getType()){
                $this->updateProductType($id_product, $value);
            }else if ($code == $this->Text->getDescription()){
                $this->setDescription($id_product, $value);
            }else if ($code == $this->Text->getLongitude() || $code == $this->Text->getWidth() || $code == $this->Text->getHeight() || $code == $this->Text->getWeight() || $code == $this->Text->getVolume()){
                $this->setMedidasComerciales($id_product, $value, $code);
            }else{
                $this->updateCustomAttribute($id_product, $value, $code);
            }
        }
    }

    /**
     * @param int $id_product
     * @param string $value
     * @param string $code
     * @return void
     */
    public function updateCustomAttribute(int $id_product, string $value, string $code){
        $attribute_id = $this->getAttibuteByCode($code);
        if (!is_null($attribute_id)){
            $this->getProductAttribute($id_product, $attribute_id, $value);
        }
    }

    /**
     * @param int $id_product
     * @param int $attribute_id
     * @param string $value
     * @return void
     */
    public function getProductAttribute(int $id_product, int $attribute_id, string $value){
        $ProductAttribute = ProductAttribute::where($this->Text->getIdProduct(), $id_product)->where($this->Text->getIdAttribute(), $attribute_id)->first();
        if (!$ProductAttribute){
            $this->createProductAttribute($id_product, $attribute_id, $value);
        }else{
            $this->changeCustomAttribute($id_product, $attribute_id, $value);
        }
    }
    
    /**
     * @param int $id_product
     * @param int $attribute_id
     * @param string $value
     * @return void
     */
    public function createProductAttribute(int $id_product, int $attribute_id, string $value){
        try {
            $ProductAttribute = new ProductAttribute();
            $ProductAttribute->value = $value;
            $ProductAttribute->id_product = $id_product;
            $ProductAttribute->id_attribute = $attribute_id;
            $ProductAttribute->save();
        } catch (Exception $th) {
            //
        }
    }

    /**
     * @param int $id_product
     * @param int $attribute_id
     * @param string $value
     */
    public function changeCustomAttribute(int $id_product, int $attribute_id, string $value){
        ProductAttribute::where($this->Text->getIdProduct(), $id_product)->where($this->Text->getIdAttribute(), $attribute_id)->update([
            $this->Text->getValue() => $value,
            $this->Text->getUpdated() => $this->Date->getFullDate()
        ]);
    }

    /**
     * @param string $code
     * @return int|null
     */
    public function getAttibuteByCode(string $code){
        $Attribute = Attribute::where($this->Text->getCode(), $code)->first();
        if (!$Attribute) {
            return null;
        }
        return $Attribute->id;
    }

    /**
     * @param int $id_product
     * @param string $value
     * @param string $code
     * @return void
     */
    public function setMedidasComerciales(int $id_product, string $value, string $code){
        $product = $this->getProductId($id_product);
        if (is_null($product->id_medidas_comerciales)){
            $id_medidas_comerciales = $this->createMedidaComercial($value, $code);
            $this->updateProductAttribute($product->id, $id_medidas_comerciales, $this->Text->getIdMedidasComerciales());
        }else{
            $this->updateMedidaComercial($product->id_medidas_comerciales, $code, $value);
        }
    }
    
    /**
     * @param int $id
     * @param string $code
     * @param string $value
     * @return void
     */
    public function updateMedidaComercial(int $id, string $code, string $value){
        $code = $this->convertMedidasComercial($code);
        if (!is_null($code)){
            MedidasComerciales::where($this->Text->getId(), $id)->update([
                $code => $value,
                $this->Text->getUpdated() => $this->Date->getFullDate()
            ]);
        }
    }

    /**
     * @param string $code
     * @return string|null
     */
    private function convertMedidasComercial(string $code){
        if ($code == $this->Text->getLongitude()){
            return $this->Text->getLongitud();
        }else if ($code == $this->Text->getWidth()){
            return $this->Text->getAncho();
        }else if ($code == $this->Text->getHeight()){
            return $this->Text->getAltura();
        }else if ($code == $this->Text->getWeight()){
            return $this->Text->getVolumen();
        }else if ($code == $this->Text->getVolume()){
            return $this->Text->getPeso();
        }else{
            return null;
        }
    }

    /**
     * @param string $value
     * @param string $code
     * @return int|null
     */
    public function createMedidaComercial(string $value, string $code){
        try {
            $MedidasComerciales = new MedidasComerciales();
            $MedidasComerciales->longitud = $code == $this->Text->getLongitude() ? $value : $this->Text->getTextNone();
            $MedidasComerciales->ancho = $code == $this->Text->getWidth() ? $value : $this->Text->getTextNone();
            $MedidasComerciales->altura = $code == $this->Text->getHeight() ? $value : $this->Text->getTextNone();
            $MedidasComerciales->volumen = $code == $this->Text->getWeight() ? $value : $this->Text->getTextNone();
            $MedidasComerciales->peso = $code == $this->Text->getVolume() ? $value : $this->Text->getTextNone();
            $MedidasComerciales->created_at = $this->Date->getFullDate();
            $MedidasComerciales->updated_at = null;
            $MedidasComerciales->save();
            return $MedidasComerciales->id;
        } catch (Exception $th) {
            return null;
        }
    }

    /**
     * @param int $id_product
     * @param string $value
     * @return void
     */
    public function updateProductBrand(int $id_product, string $value){
        $id_val = $this->getBrand($value);
        $this->updateProductAttribute($id_product, $id_val, $this->Text->getIdBrand());
    }
    
    /**
     * @param int $id_product
     * @param string $value
     * @return void
     */
    public function updateProductClacom(int $id_product, string $value){
        $id_val = $this->getClacom($value);
        $this->updateProductAttribute($id_product, $id_val, $this->Text->getIdClacom());
    }
    
    /**
     * @param int $id_product
     * @param string $value
     * @return void
     */
    public function updateProductType(int $id_product, string $value){
        $id_val = $this->getType($value);
        $this->updateProductAttribute($id_product, $id_val, $this->Text->getIdType());
    }

    /**
     * @param string $value
     * @return int|null
     */
    public function getBrand(string $value){
        $Brand = Brand::where($this->Text->getName(), $value)->first();
        if (!$Brand) {
            return null;
        }
        return $Brand->id;
    }

    /**
     * @param string $value
     * @return int|null
     */
    public function setBrand(string $value){
        try {
            $Brand = new Brand();
            $Brand->name = $value;
            $Brand->save();
            return $Brand->id;
        } catch (Exception $th) {
            return null;
        }
    }
    
    /**
     * @param string $value
     * @return int|null
     */
    public function getClacom(string $value){
        $Clacom = Clacom::where($this->Text->getLabel(), $value)->first();
        if (!$Clacom) {
            return null;
        }
        return $Clacom->id;
    }
    
    /**
     * @param string $value
     * @return int|null
     */
    public function getType(string $value){
        $ProductType = ProductType::where($this->Text->getType(), $value)->first();
        if (!$ProductType) {
            return null;
        }
        return $ProductType->id;
    }

    /**
     * @param int $id_product
     * @param string|int $value
     * @param string $attribute
     * @return void
     */
    public function updateProductAttribute(int $id_product, string|int $value, string $attribute){
        Product::where($this->Text->getId(), $id_product)->update([
            $attribute => $value,
            $this->Text->getUpdated() => $this->Date->getFullDate()
        ]);
    }

    /**
     * @param int $id_product
     * @param string $description
     * @return void
     */
    public function setDescription(int $id_product, string $description){
        $product = $this->getProductId($id_product);
        if (is_null($product->id_description)){
            $id_description = $this->createDescription($description);
            $this->updateProductAttribute($product->id, $id_description, $this->Text->getIdDescription());
        }else{
            $this->updateDescription($product->id_description, $description);
        }
    }

    /**
     * @param int $id
     * @param string $description
     * @return void
     */
    public function updateDescription(int $id, string $description){
        ProductDescription::where($this->Text->getId(), $id)->update([
            $this->Text->getDescription() => $description,
            $this->Text->getUpdated() => $this->Date->getFullDate()
        ]);
    }

    /**
     * @param string $description
     * @return int|null
     */
    public function createDescription(string $description){
        try {
            $ProductDescription = new ProductDescription();
            $ProductDescription->description = $description;
            $ProductDescription->created_at = $this->Date->getFullDate();
            $ProductDescription->updated_at = null;
            $ProductDescription->save();
            return $ProductDescription->id;
        } catch (Exception $th) {
            return null;
        }
    }

    /**
     * @param array $defaultValues
     * @param array $row
     * @param int $id_product
     * @return void
     */
    public function updateStock(array $defaultValues, array $row, int $id_product){
        if (array_key_exists($this->Text->getWarehouse(), $defaultValues)) {
            $store = $this->getCodeParam($row, $this->Text->getStore());
            $id_store = $this->storeData($store == null ? $this->Text->getTextNone() : $store);
            print_r($id_store);
        }else{
            //Error tipo erroneo
        }
    }

    public function updateStockAllCentralWarehouse(int $id_product, int $stock){
        foreach ($this->WarehouseCentral as $key => $wh) {
            //$this->ProductApi->updateProductWarehouse($id_product, $wh->id, $stock);
        }
    }

    /**
     * @param array $row
     * @param string $code
     * @return string|null
     */
    public function getCodeParam(array $row, string $code){
        foreach ($row as $key => $item) {
            if ($item[$this->Text->getCode()] == $code) {
                return $item[$this->Text->getValue()];
            }
        }
        return null;
    }

    /**
     * @param string $store
     * @return array
     */
    public function storeData(string $store){
        if ($store == $this->Text->getTextNone()) {
            return $this->getAllIdStore();
        }else{
            $data = explode($this->Text->getComa(), $store);
            return $this->getStoreData($data);
        }
    }

    /**
     * @return array
     */
    public function getAllIdStore(){
        $data = [];
        foreach ($this->Stores as $key => $value) {
            $data[] = $value->id;
        }
        return $data;
    }

    /**
     * @param array $data
     * @return array
     */
    public function getStoreData(array $data){
        $store = array();
        foreach ($data as $key => $value) {
            $STORE = $this->getStoreByCode($value);
            if (!is_null($STORE)) {
                $store[] = $STORE;
            }
        }
        return $store;
    }

    /**
     * @param string $code
     * @return int|null
     */
    public function getStoreByCode(string $code){
        foreach ($this->Stores as $key => $value) {
            if ($value->code == $code){
                return $value->id;
            }
        }
        return null;
    }

    /**
     * @param array $defaultValues
     * @param array $row
     * @param int $id_product
     * @return void
     */
    public function updateStates(array $defaultValues, array $row, int $id_product){}

    /**
     * @param array $defaultValues
     * @param array $row
     * @param int $id_product
     * @return void
     */
    public function updateCategory(array $defaultValues, array $row, int $id_product){}

    /**
     * @param array $defaultValues
     * @param array $row
     * @param int $id_product
     * @return void
     */
    public function updatePrices(array $defaultValues, array $row, int $id_product){}
}
?>
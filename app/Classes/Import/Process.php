<?php

namespace App\Classes\Import;

use App\Models\Attribute;
use App\Classes\Helper\Text;
use App\Models\Product;
use \Exception;
use App\Classes\Helper\Types;

class Process{
    const PRODUCT = "Product";
    const STOCK = "Stock";
    const ESTADOS = "Estados";
    const CATEGORIZAR = "Categorizar";
    const PRECIOS = "Precios";
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

    public function __construct() {
        $this->Text  = new Text();
        $this->Types = new Types();
    }

    /**
     * @param string $type
     * @return void
     */
    public function setType(string $type){
        $this->Type = $type;
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
            $this->loadAttributesProduct();
            $this->setProductAttribute();
        } else if ($this->Type == self::STOCK) {
            $this->loadStockProduct();
        } else if ($this->Type == self::ESTADOS) {
            $this->loadStatusProduct();
        } else if ($this->Type == self::CATEGORIZAR) {
            $this->loadCategoryProduct();
        } else if ($this->Type == self::PRECIOS) {
            $this->loadPricesProduct();
        }
    }

    /**
     * @var void
     */
    public function loadPricesProduct(){
        $this->Attributes = array(
            $this->Text->getSku() => $this->Text->getString(),
            $this->Text->getPrice() => $this->Text->getFloat(),
            $this->Text->getSpecialPrice() => $this->Text->getFloat(),
            $this->Text->getStore() => $this->Text->getString()
        );
    }

    /**
     * @var void
     */
    public function loadCategoryProduct(){
        $this->Attributes = array(
            $this->Text->getSku() => $this->Text->getString(),
            $this->Text->getCategory() => $this->Text->getInt(),
            $this->Text->getStore() => $this->Text->getString()
        );
    }

    /**
     * @var void
     */
    public function loadStatusProduct(){
        $this->Attributes = array(
            $this->Text->getSku() => $this->Text->getString(),
            $this->Text->getStatus() => $this->Text->getBool(),
            $this->Text->getStore() => $this->Text->getString()
        );
    }

    /**
     * @var void
     */
    public function loadAttributesProduct(){
        $this->Attributes = array(
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
     * @var void
     */
    public function loadStockProduct(){
        $this->Attributes = array(
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
            print_r($this->Headers);
            $Index = $this->existIndex($index);
            print_r($Index);
            if (!is_null($Index)) {
                print_r("Entro1");
                if ($this->Types->validateType($Index[$this->Text->getType()], $value)){
                    print_r("Entro2");
                    $this->Current_Row[$this->Text->getData()][] = array(
                        $this->Text->getType() => $Index[$this->Text->getType()],
                        $this->Text->getValue() => $value,
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
        print_r($this->Data);
    }
}
?>
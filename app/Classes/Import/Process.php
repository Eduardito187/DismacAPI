<?php

namespace App\Classes\Import;

use App\Models\Attribute;

class Process{
    /**
     * @var array
     */
    protected $Headers = [];
    /**
     * @var array
     */
    protected $Attributes = [];
    /**
     * @var string
     */
    protected $Type;

    public function __construct() {
        //
    }

    public function setHeaders(string $type){
        $this->Type = $type;
    }

    public function loadAttributes(){
        if ($this->Type == "Product") {
            $this->loadAttributesProduct();
            $this->setProductAttribute();
        } else if ($this->Type == "Stock") {
            $this->loadStockProduct();
        } else if ($this->Type == "Estados") {
            $this->loadStatusProduct();
        } else if ($this->Type == "Categorizar") {
            $this->loadCategoryProduct();
        } else if ($this->Type == "Precios") {
            $this->loadPricesProduct();
        }
    }

    /**
     * @var void
     */
    public function loadPricesProduct(){
        $this->Attributes = array(
            "sku" => "string",
            "price" => "float",
            "special_price" => "float",
            "store" => "string"
        );
    }

    /**
     * @var void
     */
    public function loadCategoryProduct(){
        $this->Attributes = array(
            "sku" => "string",
            "category" => "int",
            "store" => "string"
        );
    }

    /**
     * @var void
     */
    public function loadStatusProduct(){
        $this->Attributes = array(
            "sku" => "string",
            "status" => "bool",
            "store" => "string"
        );
    }

    /**
     * @var void
     */
    public function loadAttributesProduct(){
        $this->Attributes = array(
            "sku" => "string",
            "name" => "string",
            "brand" => "string",
            "clacom" => "string",
            "type" => "string",
            "description" => "string",
            "longitude" => "string",
            "width" => "string",
            "height" => "string",
            "weight" => "string",
            "volume" => "string"
        );
    }

    /**
     * @var void
     */
    public function loadStockProduct(){
        $this->Attributes = array(
            "sku" => "string",
            "wharehouse" => "int",
            "stock" => "int",
            "store" => "string"
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
}
?>
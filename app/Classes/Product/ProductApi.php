<?php

namespace App\Classes\Product;

use App\Classes\Helper\Date;
use App\Classes\Helper\Status;
use App\Classes\Helper\Text;
use Exception;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Clacom;
use App\Models\ProductType;

class ProductApi{
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

    public function __construct() {
        $this->date     = new Date();
        $this->status   = new Status();
        $this->text     = new Text();
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
     */
    private function setProduct(string $code, string $name, string $id_brand, string $id_clacom, string $id_type){
        try {
            $Product = new Product();
            $Product->name = $name;
            $Product->sku = $code;
            $Product->stock = 0;
            $Product->id_brand = $id_brand;
            $Product->id_clacom = $id_clacom;
            $Product->id_type = $id_type;
            $Product->save();
        } catch (Exception $th) {
            throw new Exception($th->getMessage());
        }
    }
    
    /**
     * @param int $id
     * @param string $code
     * @param string $name
     * @param string $id_brand
     * @param string $id_clacom
     * @param string $id_type
     */
    private function updateProductALL(int $id, string $code, string $name, string $id_brand, string $id_clacom, string $id_type){
        Product::where('id', $id)->update([
            "name" => $name,
            "id_brand" => $id_brand,
            "id_clacom" => $id_clacom,
            "id_type" => $id_type
        ]);
    }
    
    /**
     * @param int $id
     * @param string $code
     * @param string $name
     * @param string $id_brand
     * @param string $id_clacom
     * @param string $id_type
     */
    private function updateProduct(int $id, string $code, string $name, string $id_brand, string $id_clacom, string $id_type){
        Product::where('id', $id)->update([
            "id_brand" => $id_brand,
            "id_clacom" => $id_clacom,
            "id_type" => $id_type
        ]);
    }

    /**
     * @param array $response
     */
    public function applyRequestAPI(array $response){
        foreach ($response as $res) {
            $id_product = $this->getCatalogStore($res["codigo"], $res["nombre"]);
            $id_brand = null;
            $id_type = null;
            $id_clacom = null;
            if (!empty($res["marca"]) && is_array($res["marca"])) {
                $id_brand = $this->getBrand($res["marca"]["nombre"]);
                if (is_null($id_brand)) {
                    $this->setBrand($res["marca"]["nombre"]);
                    $id_brand = $this->getBrand($res["marca"]["nombre"]);
                }
            }
            if (!empty($res["detalle"]) && is_array($res["detalle"])) {
                $id_type = $this->getType($res["detalle"]["tipoProducto"]);
                $id_clacom = $this->getClacom($res["detalle"]["clacom"]);
                if (is_null($id_type)) {
                    $this->setType($res["detalle"]["tipoProducto"]);
                    $id_type = $this->getType($res["detalle"]["tipoProducto"]);
                }
                if (is_null($id_clacom)) {
                    $this->setClacom($res["detalle"]["clacom"]);
                    $id_clacom = $this->getClacom($res["detalle"]["clacom"]);
                }
            }
            if (is_null($id_product)) {
                $this->setProduct($res["codigo"], $res["nombre"], $id_brand, $id_clacom, $id_type);
                $id_product = $this->getCatalogStore($res["codigo"], $res["nombre"]);
                $this->updateProduct(
                    $id_product,
                    $res["codigo"],
                    $res["nombre"],
                    $id_brand,
                    $id_clacom,
                    $id_type
                );
            }else{
                $this->updateProductALL(
                    $id_product,
                    $res["codigo"],
                    $res["nombre"],
                    $id_brand,
                    $id_clacom,
                    $id_type
                );
            }
        }
    }

    /**
     * @param string $name
     */
    private function setBrand(string $name){
        try {
            $Brand = new Brand();
            $Brand->name = $name;
            $Brand->save();
        } catch (Exception $th) {
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
     */
    private function setType(string $type){
        try {
            $ProductType = new ProductType();
            $ProductType->type = $type;
            $ProductType->created_at = $this->date->getFullDate();
            $ProductType->updated_at = null;
            $ProductType->save();
        } catch (Exception $th) {
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
     */
    private function setClacom(string $clacom){
        try {
            $Clacom = new Clacom();
            $Clacom->label = $clacom;
            $Clacom->code = str_replace(" ", "_", $clacom);
            $Clacom->id_picture = null;
            $Clacom->created_at = $this->date->getFullDate();
            $Clacom->updated_at = null;
            $Clacom->save();
        } catch (Exception $th) {
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
}

?>
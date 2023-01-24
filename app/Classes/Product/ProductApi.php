<?php

namespace App\Classes\Product;

use App\Classes\Helper\Date;
use App\Classes\Helper\Status;
use App\Classes\Helper\Text;
use Exception;
use App\Models\Product;

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
        $Product = Product::select($this->text->getId())->where($this->text->getSku(), $code)
        ->where($this->text->getName(), $name)->get()->toArray();
        if (count($Product) > 0) {
            return $Product[0][$this->text->getId()];
        }else{
            return null;
        }
    }

    /**
     * @param string $code
     * @param string $name
     */
    private function setProduct(string $code, string $name){
        try {
            $Product = new Product();
            $Product->name = $name;
            $Product->sku = $code;
            $Product->stock = 0;
            $Product->save();
        } catch (Exception $th) {
            throw new Exception($th->getMessage());
        }
    }

    /**
     * @param array $response
     */
    public function applyRequestAPI(array $response){
        foreach ($response as $res) {
            if (is_null($this->getCatalogStore($res["codigo"], $res["nombre"]))) {
                $this->setProduct($res["codigo"], $res["nombre"]);
            }
        }
    }
}

?>
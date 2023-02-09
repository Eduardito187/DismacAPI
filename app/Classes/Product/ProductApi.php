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

class ProductApi{
    const OPLN_PRECIO_PROPUESTO = 1;
    const OPLN_TIENDAS_SCZ = 3;
    const OPLN_TIENDAS_LPZ = 4;
    const OPLN_TIENDAS_CBA = 5;
    const OPLN_TIENDAS_TRJ = 22;
    const OPLN_TIENDAS_SCE = 23;
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
    private function updateProductRelations(int $id, string $code, string $name, string $id_brand, string $id_clacom, string $id_type){
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
        $allStore = $this->getAllStoreID();
        Log::debug("all store => ".json_encode($allStore));
        foreach ($response as $res) {
            Log::debug("sku => ".$res["codigo"]);
            $id_product = $this->getCatalogStore($res["codigo"], $res["nombre"]);
            $id_brand = null;
            $id_type = null;
            $id_clacom = null;
            if (!empty($res["marca"]) && is_array($res["marca"])) {
                $id_brand = $this->getBrand($res["marca"]["nombre"]);
                if (is_null($id_brand)) {
                    if($this->setBrand($res["marca"]["nombre"])){
                        $id_brand = $this->getBrand($res["marca"]["nombre"]);
                    }
                }
            }
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
            if (is_null($id_product)) {
                $this->setProduct($res["codigo"], $res["nombre"], $id_brand, $id_clacom, $id_type);
                $id_product = $this->getCatalogStore($res["codigo"], $res["nombre"]);
                $this->updateProductRelations(
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
            if (!empty($res["minicuotas"]) && is_array($res["minicuotas"])) {
                $this->changeMiniCuotas($id_product, $res["minicuotas"]);
            }
            if (!empty($res["estado"]) && is_array($res["estado"])) {
                $this->changeStatusProduct($id_product, $allStore, $res["estado"]["visible"]);
            }
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
            throw new Exception($th->getMessage());
        }
    }

    /**
     * @param int $id_product
     * @param int $id_store
     */
    public function getProductStoreStatus(int $id_product, int $id_store){
        $ProductStoreStatus = ProductStoreStatus::select($this->text->getId())
        ->where("id_product", $id_product)->where("id_store", $id_store)->get()->toArray();
        if (count($ProductStoreStatus) > 0) {
            return $ProductStoreStatus[0][$this->text->getId()];
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
        Product::where('id_product', $id_product)->where('id_store', $id_store)->update([
            "id_product" => $id_product,
            "id_store" => $id_store,
            "status" => $status
        ]);
    }
}

?>
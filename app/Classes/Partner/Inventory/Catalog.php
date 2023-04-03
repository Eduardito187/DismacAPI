<?php

namespace App\Classes\Partner\Inventory;

use App\Models\Catalog as ModelCatalog;
use App\Models\CatalogPartner;
use App\Models\CatalogStore;
use App\Classes\Account\AccountApi;
use App\Classes\Helper\Text;
use App\Models\Category;
use Exception;
use App\Models\Store;
use App\Classes\Helper\Status;
use App\Classes\Helper\Date;
use App\Models\CatalogCategory;
use App\Classes\Product\ProductApi;
use App\Models\ProductCategory;
use \Illuminate\Support\Facades\Log;

class Catalog{
    /**
     * @var array
     */
    protected $listResponse = [];
    protected $accountApi;
    protected $productApi;
    protected $text;
    protected $status;
    protected $date;
    public function __construct() {
        $this->accountApi = new AccountApi();
        $this->productApi = new ProductApi();
        $this->text       = new Text();
        $this->status     = new Status();
        $this->date        = new Date();
    }

    /**
     * @param string $name
     * @param string $code
     */
    private function setCatalog(string $name, string $code){
        try {
            $catalog = new ModelCatalog();
            $catalog->name = $name;
            $catalog->code = $code;
            $catalog->created_at = $this->date->getFullDate();
            $catalog->updated_at = null;
            $catalog->save();
        } catch (Exception $th) {
            throw new Exception($th->getMessage());
        }
    }

    /**
     * @param string $code
     * @return int|null
     */
    private function getCatalogIdByCode(string $code){
        $ModelCatalog = ModelCatalog::select($this->text->getId())->where($this->text->getCode(), $code)->get()->toArray();
        if (count($ModelCatalog) > 0) {
            return $ModelCatalog[0][$this->text->getId()];
        }else{
            return null;
        }
    }

    /**
     * @param string $idAccount
     * @param string $id_catalog
     * @return int|null
     */
    private function getCatalogPartner(int $idAccount, int $id_catalog){
        $CatalogPartner = CatalogPartner::select($this->text->getId())->where($this->text->getIdCatalog(), $id_catalog)->
        where($this->text->getIdPartner(), $this->accountApi->getPartnerId($idAccount))->get()->toArray();
        if (count($CatalogPartner) > 0) {
            return $CatalogPartner[0][$this->text->getId()];
        }else{
            return null;
        }
    }

    /**
     * @param int $idAccount
     * @param int $id_catalog
     */
    private function setCatalogPartner(int $idAccount, int $id_catalog){
        try {
            $catalogPartner = new CatalogPartner();
            $catalogPartner->id_catalog = $id_catalog;
            $catalogPartner->id_partner = $this->accountApi->getPartnerId($idAccount);
            $catalogPartner->id_account = $idAccount;
            $catalogPartner->created_at = $this->date->getFullDate();
            $catalogPartner->updated_at = null;
            $catalogPartner->save();
        } catch (Exception $th) {
            throw new Exception($th->getMessage());
        }
    }

    /**
     * @param int $id_catalog
     * @param int $id_store
     * @param int $id_account
     * @return int|null
     */
    private function getCatalogStore(int $id_catalog, int $id_store, int $id_account){
        $CatalogStore = CatalogStore::select($this->text->getId())->where($this->text->getIdCatalog(), $id_catalog)
        ->where($this->text->getIdStore(), $id_store)
        ->where($this->text->getIdAccount(), $id_account)->get()->toArray();
        if (count($CatalogStore) > 0) {
            return $CatalogStore[0][$this->text->getId()];
        }else{
            return null;
        }
    }

    /**
     * @param int $idCatalog
     * @param int $idAccount
     */
    private function setCatalogStore(int $idCatalog, int $idAccount){
        foreach (Store::all() as $store) {
            try {
                if (is_null($this->getCatalogStore($idCatalog, $store->id, $idAccount))) {
                    $catalogStore = new CatalogStore();
                    $catalogStore->id_catalog = $idCatalog;
                    $catalogStore->id_store = $store->id;
                    $catalogStore->id_account = $idAccount;
                    $catalogStore->created_at = $this->date->getFullDate();
                    $catalogStore->updated_at = null;
                    $catalogStore->save();
                }
            } catch (Exception $th) {
                throw new Exception($th->getMessage());
            }
        }
    }

    /**
     * @param int|null $id_catalog
     * @return array
     */
    public function getCatalog(int|null $id_catalog){
        $Catalog = ModelCatalog::find($id_catalog);
        if (is_null($Catalog)) {
            throw new Exception($this->text->getCatalogNoExist());
        }
        $NO_UNIQUE = $Catalog->Categorias;
        $UNIQUE = $Catalog->Categorias->unique();
        return array(
            $this->text->getId() => $Catalog->id,
            $this->text->getName() => $Catalog->name,
            $this->text->getCode() => $Catalog->code,
            $this->text->getCantidad() => count($UNIQUE),
            $this->text->getProducts() => $this->countProductsInCatalog($Catalog->id),
            $this->text->getCategorias() => $this->getCategoryByCatalog($Catalog->id, $UNIQUE, $NO_UNIQUE)
        );
        //->distinct($this->text->getIdCategory())
    }

    /**
     * @param int $id_catalog
     * @return int
     */
    private function countProductsInCatalog(int $id_catalog){
        return ProductCategory::select($this->text->getIdProduct())->
        where($this->text->getIdCatalog(), $id_catalog)->distinct()->count($this->text->getIdProduct());
    }
    
    /**
     * @param int $id_catalog
     * @return int
     */
    private function getProductsInCatalog(int $id_catalog){
        return ProductCategory::select($this->text->getIdProduct())->
        where($this->text->getIdCatalog(), $id_catalog)->distinct()->get();
    }

    /**
     * @param int $id_catalog
     * @param int $id_category
     * @return int
     */
    private function countProductsInCategory(int $id_catalog, int $id_category){
        return ProductCategory::select($this->text->getIdProduct())->
        where($this->text->getIdCatalog(), $id_catalog)->
        where($this->text->getIdCategory(), $id_category)->distinct()->count($this->text->getIdProduct());
    }

    /**
     * @param int $id_catalog
     * @param int $id_category
     * @return int
     */
    private function getProductsInCategory(int $id_catalog, int $id_category){
        return ProductCategory::select($this->text->getIdProduct())->
        where($this->text->getIdCatalog(), $id_catalog)->
        where($this->text->getIdCategory(), $id_category)->distinct()->get();
    }

    /**
     * @param int $id_catalog
     * @param int $id_category
     * @param int $id_store
     * @return int
     */
    private function countProductsInCategoryStore(int $id_catalog, int $id_category, int $id_store){
        return ProductCategory::select($this->text->getIdProduct())->
        where($this->text->getIdCatalog(), $id_catalog)->
        where($this->text->getIdCategory(), $id_category)->
        where($this->text->getIdStore(), $id_store)->distinct()->count($this->text->getIdProduct());
    }
    
    /**
     * @param int $id_catalog
     * @param int $id_category
     * @param int $id_store
     * @return int
     */
    private function getProductsInCategoryStore(int $id_catalog, int $id_category, int $id_store){
        return ProductCategory::select($this->text->getIdProduct())->
        where($this->text->getIdCatalog(), $id_catalog)->
        where($this->text->getIdCategory(), $id_category)->
        where($this->text->getIdStore(), $id_store)->distinct()->get();
    }

    private function getUniqueCategoryCatalog(int $id_catalog, $NO_UNIQUE, $Category){
        return array(
            $this->text->getId() => $Category->id,
            $this->text->getName() => $Category->name,
            $this->text->getCode() => $Category->code,
            $this->text->getStatus() => $Category->status,
            $this->text->getProducts() => $this->countProductsInCategory($id_catalog, $Category->id),
            $this->text->getStores() => $this->searchStoreNoUnique($id_catalog, $Category->id, $NO_UNIQUE)
        );
    }

    private function searchStoreNoUnique($id_catalog, $id_Category, $NO_UNIQUE){
        $stores = array();
        foreach ($NO_UNIQUE as $key => $ItemCategory) {
            if ($id_Category == $ItemCategory->id_category) {
                $Store = $ItemCategory->Store;
                $Store[$this->text->getProducts()] = $this->countProductsInCategoryStore($id_catalog, $id_Category, $Store->id);
                $stores[] = $ItemCategory->Store;
            }
        }
        return $stores;
    }

    /**
     * @return array
     */
    public function getCategoryByCatalog(int $id_catalog, $CatalogCategory, $NO_UNIQUE){
        $Items = array();
        foreach ($CatalogCategory as $key => $ItemCategory) {
            $Items[] = $this->getUniqueCategoryCatalog($id_catalog, $NO_UNIQUE, $ItemCategory->Category);
        }
        return $Items;
    }

    /**
     * @param int $id_catalog
     * @param string $name
     * @param int $id_account
     * @param array $id_store
     * @return 
     */
    public function newCategory(int $id_catalog, string $name, int $id_account, array $id_store){
        try {
            $code = $this->generateCode();
            $Category = new Category();
            $Category->name = $name;
            $Category->name_pos = $this->text->getNegativeId();
            $Category->code = $code;
            $Category->inheritance = null;
            $Category->status = $this->status->getEnable();
            $Category->in_menu = $this->status->getEnable();
            $Category->id_info_category = null;
            $Category->created_at = $this->date->getFullDate();
            $Category->updated_at = null;
            $Category->id_metadata = null;
            $Category->save();
            $this->setListStore($id_catalog, $this->getIdCategory($name, $code), $id_account, $id_store);
        } catch (Exception $th) {
            throw new Exception($th->getMessage());
        }
    }

    /**
     * @param int $id_category
     * @param int $id_catalog
     * @return array
     */
    public function getCategory(int $id_category, int $id_catalog){
        $Category = $this->getCategoryById($id_category);
        $NO_UNIQUE = $Category->CatalogCategory;
        $CategoryArray = $this->getUniqueCategoryCatalog($id_catalog, $NO_UNIQUE, $Category);
        $CategoryArray[$this->text->getProducts()] = $this->getProductsInCategory($id_catalog, $id_category);
        return $CategoryArray;
    }

    /**
     * @param array $products
     * @return void
     */
    public function changePrices(array $products){
        $allStore = $this->productApi->getAllStoreID();
        foreach ($products as $key => $product) {
            $status = null;
            try {
                $Producto = $this->productApi->getProductBySku($product[$this->text->getSku()]);
                $this->productApi->changePriceApi($allStore, $product, $Producto);
                $status = $this->status->getEnable();
            } catch (Exception $th) {
                Log::debug($th->getMessage());
                $status = $this->status->getDisable();
            }
            $this->listResponse[] = array(
                $this->text->getSku() => $product[$this->text->getSku()],
                $this->text->getStatus() => $status,
                $this->text->getStores() => implode($this->text->getComa(), $product[$this->text->getStores()])
            );
        }
    }

    public function removeProductCategory(){
        
    }

    /**
     * @param int $id_catalog
     * @param int $id_category
     * @param array $products
     * @return void
     */
    public function asignarProductos(int $id_catalog, int $id_category, array $products){
        $allStore = $this->productApi->getAllStoreID();
        foreach ($products as $key => $product) {
            $status = null;
            try {
                $Producto = $this->productApi->getProductBySku($product[$this->text->getSku()]);
                $this->productApi->asignarCategory($id_catalog, $id_category, $allStore, $product, $Producto);
                $status = $this->status->getEnable();
            } catch (Exception $th) {
                Log::debug($th->getMessage());
                $status = $this->status->getDisable();
            }
            $this->listResponse[] = array(
                $this->text->getSku() => $product[$this->text->getSku()],
                $this->text->getStatus() => $status,
                $this->text->getStores() => implode($this->text->getComa(), $product[$this->text->getStores()])
            );
        }
    }

    /**
     * @param int $id_catalog
     * @param int $id_category
     * @param array $products
     * @return void
     */
    public function desasignarProductos(int $id_catalog, int $id_category, array $products){
        $allStore = $this->productApi->getAllStoreID();
        foreach ($products as $key => $product) {
            $status = null;
            try {
                $Producto = $this->productApi->getProductBySku($product[$this->text->getSku()]);
                $this->productApi->desasignarCategory($id_catalog, $id_category, $allStore, $product, $Producto);
                $status = $this->status->getEnable();
            } catch (Exception $th) {
                Log::debug($th->getMessage());
                $status = $this->status->getDisable();
            }
            $this->listResponse[] = array(
                $this->text->getSku() => $product[$this->text->getSku()],
                $this->text->getStatus() => $status,
                $this->text->getStores() => implode($this->text->getComa(), $product[$this->text->getStores()])
            );
        }
    }

    /**
     * @return array
     */
    public function getResponseAPI(){
        return $this->listResponse;
    }

    /**
     * @param int $id_catalog
     * @param int $id_category
     * @param int $id_account
     * @param array $listStore
     */
    private function setListStore(int $id_catalog, int $id_category, int $id_account, array $listStore){
        try {
            $Catalog = ModelCatalog::find($id_catalog);
            foreach ($listStore as $key => $store) {
                $this->setCatalogCategory($Catalog->id, $id_category, $id_account, $store);
            }
        } catch (Exception $th) {
            throw new Exception($th->getMessage());
        }
    }

    /**
     * @return int
     */
    private function generateCode(){
        return rand(1000, 1000000);
    }

    /**
     * @param string $name
     * @param int $code
     */
    private function getIdCategory(string $name, int $code){
        $Category = Category::select($this->text->getId())->where($this->text->getName(), $name)->where($this->text->getCode(), $code)->get()->toArray();
        if (count($Category) > 0) {
            return $Category[0][$this->text->getId()];
        }
        throw new Exception($this->text->getCategoryNone());
    }

    /**
     * @param int $id_catalog
     * @param int $id_category
     * @param int $id_account
     * @param int $id_store
     */
    public function setCatalogCategory(int $id_catalog, int $id_category, int $id_account, int $id_store){
        try {
            $CatalogCategory = new CatalogCategory();
            $CatalogCategory->id_category = $id_category;
            $CatalogCategory->id_catalog = $id_catalog;
            $CatalogCategory->id_account = $id_account;
            $CatalogCategory->id_store = $id_store;
            $CatalogCategory->created_at = $this->date->getFullDate();
            $CatalogCategory->updated_at = null;
            $CatalogCategory->save();
        } catch (Exception $th) {
            throw new Exception($th->getMessage());
        }
    }

    /**
     * @param string $name
     * @param string $code
     * @param int $idAccoun
     * @return void
     */
    public function newCatalog(string $name, string $code, int $idAccount){
        $id_catalog = $this->getCatalogIdByCode($code);
        if (is_null($id_catalog)) {
            $this->setCatalog($name, $code);
            $id_catalog = $this->getCatalogIdByCode($code);
        }else{
            throw new Exception($this->text->getCatalogExist());
        }
        $id_catalog_partner = $this->getCatalogPartner($idAccount, $id_catalog);
        if (is_null($id_catalog_partner)) {
            $this->setCatalogPartner($idAccount, $id_catalog);
            $id_catalog_partner = $this->getCatalogPartner($idAccount, $id_catalog);
        }
        $this->setCatalogStore($id_catalog, $idAccount);
    }

    /**
     * @param int $id
     * @return Category
     */
    public function getCategoryById(int $id){
        $product = Category::where($this->text->getId(), $id)->first();
        if (!$product) {
            throw new Exception($this->text->getNoneIdProduct($id));
        }
        return $product;
    }
}

?>
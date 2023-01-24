<?php

namespace App\Classes\Partner\Inventory;

use App\Models\Catalog as ModelCatalog;
use App\Models\CatalogPartner;
use App\Models\CatalogStore;
use App\Classes\Account\AccountApi;
use App\Classes\Helper\Text;
use Exception;
use App\Models\Store;

class Catalog{
    protected $accountApi;
    protected $text;
    public function __construct() {
        $this->accountApi = new AccountApi();
        $this->text       = new Text();
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
            $catalog->created_at = date("Y-m-d H:i:s");
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
            $catalogPartner->created_at = date("Y-m-d H:i:s");
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
                    $catalogStore->created_at = date("Y-m-d H:i:s");
                    $catalogStore->updated_at = null;
                    $catalogStore->save();
                }
            } catch (Exception $th) {
                throw new Exception($th->getMessage());
            }
        }
    }

    public function newCatalog(string $name, string $code, int $idAccount){
        $id_catalog = $this->getCatalogIdByCode($code);
        if (is_null($id_catalog)) {
            $this->setCatalog($name, $code);
            $id_catalog = $this->getCatalogIdByCode($code);
        }
        $id_catalog_partner = $this->getCatalogPartner($idAccount, $id_catalog);
        if (is_null($id_catalog_partner)) {
            $this->setCatalogPartner($idAccount, $id_catalog);
            $id_catalog_partner = $this->getCatalogPartner($idAccount, $id_catalog);
        }
        $this->setCatalogStore($id_catalog, $idAccount);
    }
}

?>
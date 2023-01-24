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
        $CatalogPartner = CatalogPartner::select($this->text->getId())->where($this->text->getCode(), $code)->get()->toArray();
        if (count($CatalogPartner) > 0) {
            return $CatalogPartner[0][$this->text->getId()];
        }else{
            throw new Exception($this->text->getNonePartner());
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
     * @param int $idCatalog
     * @param int $idAccount
     */
    private function setCatalogStore(int $idCatalog, int $idAccount){
        foreach (Store::all() as $store) {
            try {
                $catalogStore = new CatalogStore();
                $catalogStore->id_catalog = $idCatalog;
                $catalogStore->id_store = $store->id;
                $catalogStore->id_account = $idAccount;
                $catalogStore->created_at = date("Y-m-d H:i:s");
                $catalogStore->updated_at = null;
                $catalogStore->save();
            } catch (Exception $th) {
                throw new Exception($th->getMessage());
            }
        }
    }

    public function newCatalog(string $name, string $code, int $idAccount){
        $this->setCatalog($name, $code);
        $id_catalog = $this->getCatalogIdByCode($code);
        $this->setCatalogPartner($idAccount, $id_catalog);
        $this->setCatalogStore($id_catalog, $idAccount);
    }
}

?>
<?php

namespace App\Classes\Pdf;

use Illuminate\Support\Facades\Log;
use App\Classes\Helper\Text;
use App\Models\Account;
use App\Models\Category;
use App\Models\Price;
use App\Models\ProductCategory;
use App\Models\ProductPriceStore;
use App\Models\Store;
use Exception;
use App\Classes\Picture\PictureApi;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\View;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

class GeneratePdf{
    CONST DEFAULT_PRICE = 24948;
    /**
     * @var Text
     */
    protected $text;
    /**
     * @var PictureApi
     */
    protected $pictureApi;

    public function __construct() {
        $this->text = new Text();
        $this->pictureApi = new PictureApi();
    }

    /**
     * @param int $id
     * @return Category
     */
    public function categoryById(int $id){
        $Category = Category::find($id);
        if (!$Category){
            throw new Exception($this->text->getCategoryNone());
        }
        return $Category;
    }

    /**
     * @param Account $Account
     * @param int $id_category
     * @return array
     */
    public function generatePdfCategory(Account $Account, int $id_category){
        $Category = $this->categoryById($id_category);
        $partner = $Account->accountPartner->Partner;
        return $this->generateCategoryPdf($Category->id, $this->getAllStorePartnerArray($partner->Stores), $partner->id);
    }

    public function getAllStorePartnerArray($stores){
        $data = array();
        foreach ($stores as $key => $store) {
            $data[] = $this->storeArray($store->Store);
        }
        return $data;
    }

    /**
     * @param Store|null $store
     * @return array
     */
    public function storeArray(Store|null $store){
        if (!$store){
            return null;
        }
        return array(
            $this->text->getId() => $store->id,
            $this->text->getName() => $store->name,
            $this->text->getCode() => $store->code
        );
    }

    /**
     * @return ProductCategory[]
     */
    public function getProductCategoryStore(int $id_category, int $id_store){
        return ProductCategory::where($this->text->getIdCategory(), $id_category)->where($this->text->getIdStore(), $id_store)->get();
    }

    public function getBrand($Brand){
        if (!$Brand){
            return null;
        }
        return $Brand->name;
    }

    /**
     * @param int $id_store
     * @param int $id_product
     * @return ProductPriceStore
     */
    public function getPriceByStore(int $id_store, int $id_product){
        return ProductPriceStore::where($this->text->getIdStore(), $id_store)->where($this->text->getIdProduct(), $id_product)->first();
    }

    /**
     * @param int $id
     * @return Price
     */
    public function getPriceById(int $id){
        return Price::find($id);
    }

    /**
     * @param int $id_store
     * @param int $id_product
     * @return array
     */
    public function getProductPriceByStore(int $id_store, int $id_product){
        $ProductPriceStore = $this->getPriceByStore($id_store, $id_product);
        $Price = null;
        if (!$ProductPriceStore) {
            $Price = $this->getPriceById(self::DEFAULT_PRICE);
        }else{
            $Price = $ProductPriceStore->Price;
        }
        return $this->priceByPrice($Price);
    }

    /**
     * @param Price $Price
     * @return array
     */
    public function priceByPrice(Price $Price){
        return array(
            $this->text->getPrice() => $Price->price ?? 0,
            $this->text->getSpecialPrice() => $Price->special_price ?? 0
        );
    }

    /**
     * @param string $dir
     * @return void
     */
    public function createFolder(string $dir){
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
    }

    public function generateCategoryPdf(int $id_category, array $stores, int $id_partner){
        $list = array();
        $locationStorage = $this->text->getPublicStoragePdf().$id_partner.$this->text->getSlashOnly();
        $this->createFolder($locationStorage);
        foreach ($stores as $key => $store) {
            $products = array();
            $listProduct = $this->getProductCategoryStore($id_category, $store[$this->text->getId()]);
            foreach ($listProduct as $key => $list) {
                $Product = $list->Product;
                $ProductPriceStore = $this->getProductPriceByStore($store[$this->text->getId()], $Product->id);
                if (!$ProductPriceStore) {
                    //
                }else{
                    $products[] = array(
                        $this->text->getName() => $Product->name,
                        $this->text->getBrand() => $this->getBrand($Product->Brand),
                        $this->text->getPrice() => $ProductPriceStore[$this->text->getPrice()],
                        $this->text->getSpecialPrice() => $ProductPriceStore[$this->text->getSpecialPrice()],
                        $this->text->getImage() => $this->pictureApi->productFirstPicture($Product->id)
                    );
                }
            }
            $products = array_chunk($products, 12);

            $pdf = PDF::loadView('catalogo', compact('products'));

            $filename = date("Y-m-d H:i:s")."-PDF-".$store[$this->text->getName()].'.pdf';
            $list[] = $filename;
            $filePath = public_path($locationStorage.$filename);

            $pdf->save($filePath);
        }
        return $list;
    }
}
?>
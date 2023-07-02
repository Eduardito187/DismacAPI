<?php

namespace App\Classes\Picture;

use Illuminate\Support\Facades\Log;
use App\Models\Picture;
use App\Models\ProductPicture;
use App\Classes\Helper\Text;
use Illuminate\Support\Facades\Hash;
use App\Classes\Helper\Date;
use App\Models\PictureProperty;
use App\Models\Product;
use \Illuminate\Http\UploadedFile;
use \Illuminate\Http\Request;
use Exception;
use Throwable;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class PictureApi{
    /**
     * @var Text
     */
    protected $text;
    /**
     * @var Date
     */
    protected $date;
    CONST DEFAULT_IMAGE = 3;
    /**
     * @var string
     */
    protected $path = "";
    /**
     * @var string
     */
    protected $nameFile = "";

    public function __construct() {
        $this->date = new Date();
        $this->text = new Text();
    }

    /**
     * @param Request $request
     * @param int $id_Partner
     * @param string $folder
     * @param int $idProduct
     * @return void
     */
    public function uploadPictures(Request $request, int $id_Partner, string $folder, int $idProduct){
        $index = 0;
        $params = $request->all();
        $this->createFolder($this->text->getPublicStorage().$id_Partner.$this->text->getGuionBajo().$idProduct.$this->text->getSlashOnly());
        while ($index < $params[$this->text->getLong()] ?? 0) {
            $file = $request->file($this->text->getFile().$this->text->getGuionBajo().$index);
            $public = $this->uploadFile($file, $id_Partner, $folder);
            $this->saveProductPicture($idProduct, $this->getPicture($public)->id);
            $index++;
        }
    }  

    /**
     * @param Request $request
     * @param int $id_Partner
     * @param string $folder
     * @param bool $type
     * @return int|Picture
     */
    public function uploadPicture(Request $request, int $id_Partner, string $folder, bool $type = false){
        $file = $request->file($this->text->getFile());
        $public = $this->uploadFile($file, $id_Partner, $folder);
        if ($type == false){
            return $this->getPicture($public)->id;
        }
        return $this->getPicture($public);
    }    

    /**
     * @param UploadedFile $File
     * @param int $id_Partner
     * @param string $folder
     * @return string
     */
    public function uploadFile(UploadedFile $File, int $id_Partner, string $folder){
        $this->nameFile = time().$this->text->getPictureParam().time();
        $imageName = $this->nameFile.$this->text->getPunto().$File->getClientOriginalExtension();
        $Path = $this->text->getFolderStorage().$folder.$id_Partner;
        $File->move($Path, $imageName);
        $this->path = $Path.$this->text->getSlashOnly();
        $local = $this->path.$imageName;
        $public = env($this->text->getAppUrl()).$this->text->getSlashOnly().$local;
        $this->saveData($public, $local);
        return $public;
    }

    /**
     * @return string
     */
    public function getPath(){
        return $this->path;
    }

    /**
     * @return string
     */
    public function getNameFile(){
        return $this->nameFile;
    }

    /**
     * @param string $zipPath
     * @return void
     */
    public function unZip(string $zipPath){
        $zip = new ZipArchive();
        if ($zip->open($zipPath) !== true) {
            throw new Exception($this->text->getFileNoReading());
        }
        $zip->extractTo($this->getPath().$this->getNameFile());
        $zip->close();
    }

    /**
     * @param int $id_partner
     * @param string $pathFile
     * @return void
     */
    public function processZipFile(int $id_partner, string $pathFile){
        $pathFile = str_replace($this->text->getZipExtensions(), $this->text->getSlashOnly(), $pathFile);
        $filePath = str_replace($this->text->getStorage(), $this->text->getPublic(), $pathFile).$this->text->getImagenes();
        $folderPath = Storage::directories($filePath);
        foreach ($folderPath as $dir) {
            $array_tmp = explode($this->text->getSlashOnly(), $dir);
            $sku = end($array_tmp);
            $SKU = str_replace($this->text->getGuionBajo(), $this->text->getSlashOnly(), $sku);
            $id_Product = $this->getProductBySkuPartner($SKU, $id_partner)->id;
            $files = Storage::files($dir);
            foreach ($files as $file) {
                $file_after = str_replace($this->text->getProcess(), $this->text->getProductsApi(), $file);
                $this->createFolder($this->text->getPublicStorage().$id_partner.$this->text->getGuionBajo().$id_Product.$this->text->getSlashOnly());
                $file_after = str_replace($id_partner.$this->text->getSlashOnly().$this->getNameFile().$this->text->getStorageImagenes().$sku.$this->text->getSlashOnly(), $id_partner.$this->text->getGuionBajo().$id_Product.$this->text->getSlashOnly(), $file_after);
                if (Storage::move($file, $file_after)){
                    $public = env($this->text->getAppUrl()).Storage::url($file_after);
                    $id_Picture = $this->saveData($public, $file_after);
                    $this->saveProductPicture($id_Product, $id_Picture);
                    $this->deleteFile($file);
                }
            }
        }
        $this->deleteFolder($pathFile);
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

    /**
     * @param string $sku
     * @param int $id_Partner
     * @return Product
     */
    public function getProductBySkuPartner(string $sku, int $id_Partner){
        $product = Product::where($this->text->getSku(), $sku)->where($this->text->getIdPartner(), $id_Partner)->first();
        if (!$product) {
            throw new Exception($this->text->getNoneSku($sku));
        }
        return $product;
    }

    /**
     * @param int $id_product
     * @param int $id_picture
     * @return bool
     */
    public function saveProductPicture(int $id_product, int $id_picture){
        try {
            $ProductPicture = new ProductPicture();
            $ProductPicture->id_product = $id_product;
            $ProductPicture->id_picture = $id_picture;
            $ProductPicture->save();
            $this->updateProductInformation($id_product);
            return true;
        } catch (Exception $th) {
            return false;
        }
    }

    /**
     * @param int $id_product
     * @return void
     */
    public function updateProductInformation(int $id_product){
        Product::where($this->text->getId(), $id_product)->update([
            $this->text->getUpdated() => $this->date->getFullDate()
        ]);
    }

    /**
     * @param string $path
     * @return void
     */
    public function deleteFile(string $path){
        $path = str_replace($this->text->getStoragePath(), $this->text->getTextNone(), $path);
        unlink(storage_path().$path);
    }

    /**
     * @param int $id_picture
     * @return bool
     */
    public function deletePictureProduct(int $id_picture){
        try {
            ProductPicture::where($this->text->getIdPicture(), $id_picture)->delete();
            PictureProperty::where($this->text->getIdPicture(), $id_picture)->delete();
            Picture::where($this->text->getId(), $id_picture)->delete();
            return true;
        } catch (Throwable $th) {
            return false;
        }
    }

    /**
     * @param string $path
     * @return void
     */
    public function deleteFolder(string $path){
        File::deleteDirectory($path);
    }

    /**
     * @param string $url
     * @param string $path
     * @return int|bool
     */
    public function saveData(string $url, string $path){
        try {
            $Picture = new Picture();
            $Picture->url = $url;
            $Picture->path = $path;
            $Picture->created_at = $this->date->getFullDate();
            $Picture->updated_at = null;
            $Picture->save();
            return $Picture->id;
        } catch (Exception $th) {
            return false;
        }
    }

    /**
     * @param string $url
     * @return Picture
     */
    public function getPictureByUrl(string $url){
        return Picture::where($this->text->getUrl(), $url)->first();
    }

    /**
     * @param string $url
     * @return Picture
     */
    public function getPicture(string $url){
        $Picture = $this->getPictureByUrl($url);
        if (!$Picture) {
            throw new Exception($this->text->getFileUndefined());
        }
        return $Picture;
    }

    /**
     * @param int $id
     * @return Picture
     */
    public function getImageById(int $id){
        return Picture::find($id);
    }

    /**
     * @param Picture $Picture
     * @return string
     */
    public function getPublicUrlImage(Picture $Picture){
        return $Picture->url;
    }
    
    /**
     * @param int $id_product
     * @return string
     */
    public function productFirstPicture(int $id_product){
        $productPicture = $this->getFirstImage($id_product);
        $Picture = null;
        if (!$productPicture) {
            $Picture = $this->getImageById($this::DEFAULT_IMAGE);
        }else{
            $Picture = $productPicture->Picture;
        }
        return $this->getPublicUrlImage($Picture);
    }

    /**
     * @param int $id_product
     * @return ProductPicture
     */
    public function getFirstImage(int $id_product){
        return ProductPicture::where($this->text->getIdProduct(), $id_product)->first();
    }
}

?>
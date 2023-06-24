<?php

namespace App\Classes\Picture;

use Illuminate\Support\Facades\Log;
use App\Models\Picture;
use App\Models\ProductPicture;
use App\Classes\Helper\Text;
use Illuminate\Support\Facades\Hash;
use App\Classes\Helper\Date;
use App\Models\Product;
use \Illuminate\Http\UploadedFile;
use \Illuminate\Http\Request;
use Exception;
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
     * @param bool $type
     * @return int|Picture
     */
    public function uploadPicture(Request $request, int $id_Partner, string $folder, bool $type = false){
        $file = $request->file('File');
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
        $this->nameFile = time().'-picture-'.time();
        $imageName = $this->nameFile.".".$File->getClientOriginalExtension();
        $Path = "storage/".$folder.$id_Partner;
        $File->move($Path, $imageName);
        $this->path = $Path."/";
        $local = $this->path.$imageName;
        $public = env('APP_URL')."/".$local;
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
        $pathFile = str_replace(".zip", "/", $pathFile);
        $filePath = str_replace("storage", "public", $pathFile)."Imagenes/";
        $folderPath = Storage::directories($filePath);
        foreach ($folderPath as $dir) {
            $array_tmp = explode('/', $dir);
            $sku = end($array_tmp);
            $SKU = str_replace("_", "/", $sku);
            $id_Product = $this->getProductBySkuPartner($SKU, $id_partner)->id;
            $files = Storage::files($dir);
            foreach ($files as $file) {
                $file_after = str_replace("Process", "Products", $file);
                Storage::makeDirectory('public/Products/'.$id_partner."_".$id_Product, 0755);
                $file_after = str_replace($id_partner."/".$this->getNameFile()."/Imagenes/".$sku."/", $id_partner."_".$id_Product."/", $file_after);
                if (Storage::move($file, $file_after)){
                    $public = env('APP_URL').Storage::url($file_after);
                    $id_Picture = $this->saveData($public, $file_after);
                    $this->saveProductPicture($id_Product, $id_Picture);
                    $this->deleteFile($file);
                }
            }
        }
        $this->deleteFolder($pathFile);
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
            return true;
        } catch (Exception $th) {
            return false;
        }
    }

    /**
     * @param string $path
     * @return void
     */
    public function deleteFile(string $path){
        Storage::delete($path);
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
        return Picture::where("url", $url)->first();
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
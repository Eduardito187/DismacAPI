<?php

namespace App\Classes\Picture;

use Illuminate\Support\Facades\Log;
use App\Models\Picture;
use App\Models\ProductPicture;
use App\Classes\Helper\Text;
use Illuminate\Support\Facades\Hash;
use App\Classes\Helper\Date;
use \Illuminate\Http\UploadedFile;
use \Illuminate\Http\Request;
use Exception;

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

    public function __construct() {
        $this->date = new Date();
        $this->text = new Text();
    }

    /**
     * @param Request $request
     * @param int $id_Partner
     * @param string $folder
     * @return int
     */
    public function uploadPicture(Request $request, int $id_Partner, string $folder){
        $file = $request->file('File');
        $public = $this->uploadFile($file, $id_Partner, $folder);
        return $this->getPicture($public)->id;
    }    

    /**
     * @param UploadedFile $File
     * @param int $id_Partner
     * @param string $folder
     * @return string
     */
    public function uploadFile(UploadedFile $File, int $id_Partner, string $folder){
        $imageName = time().'-picture-'.time().".".$File->getClientOriginalExtension();
        $Path = "storage/".$folder.$id_Partner;
        $File->move($Path, $imageName);
        $local = "/".$Path."/".$imageName;
        $public = env('APP_URL').$local;
        print_r($public);
        print_r($local);
        print_r($Path);
        print_r($imageName);
        $this->saveData($public, $local);
        return $public;
    }

    /**
     * @param string $url
     * @param string $path
     * @return bool
     */
    public function saveData(string $url, string $path){
        try {
            $Picture = new Picture();
            $Picture->url = $url;
            $Picture->path = $path;
            $Picture->created_at = $this->date->getFullDate();
            $Picture->updated_at = null;
            $Picture->save();
            return true;
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
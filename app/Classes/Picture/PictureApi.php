<?php

namespace App\Classes\Picture;

use Illuminate\Support\Facades\Log;
use App\Models\Picture;
use App\Models\ProductPicture;
use App\Classes\Helper\Text;
use Illuminate\Support\Facades\Hash;
use Exception;

class PictureApi{
    /**
     * @var Text
     */
    protected $text;
    CONST DEFAULT_IMAGE = 3;
    public function __construct() {
        $this->text = new Text();
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
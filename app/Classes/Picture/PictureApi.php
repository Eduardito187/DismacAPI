<?php

namespace App\Classes\Picture;

use Illuminate\Support\Facades\Log;
use App\Models\Picture;
use Illuminate\Support\Facades\Hash;
use Exception;

class PictureApi{
    CONST DEFAULT_IMAGE = 3;
    public function __construct() {
        //
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
}

?>
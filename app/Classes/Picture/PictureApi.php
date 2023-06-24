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
        $this->path = "/".$Path."/";
        $local = "/".$this->path.$imageName;
        $public = env('APP_URL').$local;
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

    public function processZipFile(){
        // directorio del cliente en `/storage/app/public`
        $id_figura = 11;
        // Obtienes los subdirectorios que están dentro del directorio del cliente
        $directorios_del_cliente = Storage::directories("/public/Process/1/1687598029-picture-1687598029/");
        print_r($directorios_del_cliente);
        /*
        // creas un array vacío para ir llenándolo con los elementos año/mes/archivos
        $tree_array = [];

        // iteras sobre los directorios obtenidos
        foreach ($directorios_del_cliente as $directorio_del_ano) {
            // haciendo un explode, separas el string en un array con cada directorio.
            $temp_array = explode('/', $directorio_del_ano);
            // obtienes el último elemento (el año).
            $year = end( $temp_array );
            // Obtienes los subdirectorios que están dentro del directorio del año
            $subdirectorios_del_ano = Storage::directories("/public/Process/1/$id_figura/$year");
            foreach ($subdirectorios_del_ano as $directorio_del_mes) {
                $temp_array = explode('/', $directorio_del_mes);
                // obtienes el último elemento (el mes).
                $month = end( $temp_array );
                // Obtienes los archivos que están dentro del directorio del mes
                $archivos_del_mes = Storage::files($directorio_del_mes);
                foreach ($archivos_del_mes as $archivo_del_mes) {
                    $temp_array = explode('/', $archivo_del_mes);
                    // obtienes el último elemento (el nombre del archivo).
                    $filename = end( $temp_array );
                    // obtienes la url que corresponde a ese archivo.
                    $url = Storage::url($archivo_del_mes);
                    // guardas en tu array el nombre y la url del archivo, 
                    // haciendo que el array sea multidimensional por año y mes.
                    $tree_array[$year][$month][] = [
                        'filename' => $filename,
                        'url' => $url
                    ];
                }
            }
        }
        */
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
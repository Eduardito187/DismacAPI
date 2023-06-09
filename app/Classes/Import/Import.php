<?php

namespace App\Classes\Import;

use App\Classes\Helper\Date;
use App\Classes\Helper\Status;
use App\Classes\Helper\Text;
use App\Classes\TokenAccess;
use App\Models\Account;
use App\Models\AccountPartner;
use App\Models\Picture;
use App\Models\Process;
use \Illuminate\Http\Request;
use \Exception;
use \Illuminate\Http\UploadedFile;

class Import{
    CONST FOLDER = "Process/";
    CONST AUTH = "Wagento:wagento2021";
    /**
     * @var Date
     */
    protected $date;
    /**
     * @var Status
     */
    protected $status;
    /**
     * @var Text
     */
    protected $text;
    /**
     * @var TokenAccess
     */
    protected $tokenAccess;

    public function __construct() {
        $this->date     = new Date();
        $this->status   = new Status();
        $this->text     = new Text();
    }

    /**
     * @param array $request
     * @return array $response
     */
    public function importCategory(array $request){
        
        $url = 'https://posapi.dismac.com.bo/v2/Product/GetItems';
        $data = [
            $this->text->getGrupoArticulo()  => $request[$this->text->getGrupoArticulo()],
            $this->text->getDisponibilidad() => $request[$this->text->getDisponibilidad()],
            $this->text->getPrecios()        => $request[$this->text->getPrecios()],
            $this->text->getSubCategoria()   => $request[$this->text->getSubCategoria()]
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPGET, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $this->text->getMethodGet());
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array($this->text->getPosParamOne(),$this->text->getPosAuth(). base64_encode(SELF::AUTH)));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response, true);
    }

    /**
     * @param string $value
     * @return int
     */
    public function getAccountToken(string $value){
        $this->tokenAccess = new TokenAccess($value);
        $Account = Account::select($this->text->getId())->where($this->text->getToken(), $this->tokenAccess->getToken())->get()->toArray();
        if (count($Account) > 0) {
            return $Account[0][$this->text->getId()];
        }else{
            throw new Exception($this->text->AccountNotExist());
        }
    }

    /**
     * @param int $idAccount
     * @return int|null
     */
    public function getPartnerId(int $idAccount){
        $AccountPartner = AccountPartner::select($this->text->getIdPartner())->where($this->text->getIdAccount(), $idAccount)->get()->toArray();
        if (count($AccountPartner) > 0) {
            return $AccountPartner[0][$this->text->getIdPartner()];
        }else{
            throw new Exception($this->text->getNonePartner());
        }
    }

    public function setActionProgram(Request $request){
        $params = $request->all();
        $id_Partner = $this->getPartnerId($this->getAccountToken($request->header($this->text->getAuthorization())));
        $file = $request->file('File');
        $public = $this->uploadFile($file, $id_Partner);
        $Picture = $this->getPicture($public);
        return  $this->newProcess($Picture->id, $id_Partner, $params);
    }

    /**
     * @param int $File
     * @param int $Partner
     * @param array $param
     * @return bool
     */
    public function newProcess(int $File, int $Partner, array $param){
        try {
            $Process = new Process();
            $Process->File = $File;
            $Process->Partner = $Partner;
            $Process->Type = $param["Type"];
            $Process->Ejecucion = $param["Ejecucion"];
            $Process->Duracion = $param["Duracion"];
            $Process->FechaEjecucion = $param["FechaEjecucion"];
            $Process->FechaDuracion = $param["FechaDuracion"];
            $Process->Status = $param["Status"];
            $Process->created_at = $this->date->getFullDate();
            $Process->updated_at = null;
            $Process->save();
            return true;
        } catch (Exception $th) {
            return false;
        }
    }

    /**
     * @param UploadedFile $File
     * @param int $id_Partner
     * @return string
     */
    public function uploadFile(UploadedFile $File, int $id_Partner){
        $imageName = time().'-process-'.time().$File->extension();
        $Path = self::FOLDER.$id_Partner;
        $File->move($Path, $imageName);
        $local = "/storage/".$Path."/".$imageName;
        $public = env('APP_URL').$local;
        $this->saveData($local, $public);
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
}
?>
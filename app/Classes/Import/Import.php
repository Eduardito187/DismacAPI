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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class Import{
    CONST FOLDER = "Process/";
    CONST AUTH = "Wagento:wagento2021";
    CONST LOG_TEXT = "El proceso ID : %, del partner & fue ejecutado exitosamente.";
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
     * @param array $params
     * @return bool
     */
    public function runProcess(array $params){
        return $this->processApply($this->getProcess($params[$this->text->getId()]));
    }

    /**
     * @param Process $Process
     * @return bool
     */
    public function processApply(Process $Process){
        $this->validateFile($Process);
        $text = $this->getReplaceId($Process->id, self::LOG_TEXT);
        $text = $this->getReplacePartner($Process->PartnerProcess->name, $text);
        Log::channel('process_run')->info($text);
        return true;
    }

    public function validateFile(Process $Process){
        echo public_path('Process/1/1686298936-process-1686298936.csv');
        if (File::exists(public_path('Process/1/1686298936-process-1686298936.csv'))){
            throw new Exception("Existe el archivo.");
        }else{
            throw new Exception("No existe el archivo.");
        }
    }

    /**
     * @param int $id
     * @param string $text
     * @return string
     */
    public function getReplaceId(int $id, string $text){
        return str_replace("%", $id, $text);
    }

    /**
     * @param string $partner
     * @param string $text
     * @return string
     */
    public function getReplacePartner(string $partner, string $text){
        return str_replace("&", $partner, $text);
    }

    /**
     * @param int $id
     * @return Process
     */
    public function getProcess(int $id){
        $Process = $this->getProcessById($id);
        if (!$Process) {
            throw new Exception($this->text->getProcessNone());
        }
        return $Process;
    }
    

    /**
     * @param int $id
     * @return Process
     */
    public function getProcessById(int $id){
        return Process::find($id);
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

    /**
     * @param Request $request
     * @return bool
     */
    public function setActionProgram(Request $request){
        $params = $request->all();
        $id_Partner = $this->getPartnerId($this->getAccountToken($request->header($this->text->getAuthorization())));
        $file = $request->file('File');
        $public = $this->uploadFile($file, $id_Partner);
        $Picture = $this->getPicture($public);
        return $this->newProcess($Picture->id, $id_Partner, $params);
    }

    /**
     * @return array
     */
    public function getAllProcessPending(){
        $data = $this->getAllProcess();
        return $this->processAllPending($data);
    }

    public function processAllPending($Process){
        $data = array();
        foreach ($Process as $key => $process) {
            $data[] = array(
                $this->text->getIdApi() => $process->id,
                $this->text->getEjecucionApi() => $process->Ejecucion,
                $this->text->getDuracionApi() => $process->Duracion,
                $this->text->getFechaEjecucionApi() => $process->FechaEjecucion,
                $this->text->getFechaDuracionApi() => $process->FechaDuracion,
                $this->text->getStatusColumn() => $process->Status
            );
        }
        return $data;
    }

    /**
     * @return Process[]
     */
    public function getAllProcess(){
        return Process::where($this->text->getStatusColumn(), "!=" , $this->status->getDisable())->get();
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
            $Process->FechaEjecucion = $this->replaceDateApi($param["FechaEjecucion"] ?? "");
            $Process->FechaDuracion = $this->replaceDateApi($param["FechaDuracion"] ?? "");
            $Process->Status = $this->status->getEnable();
            $Process->created_at = $this->date->getFullDate();
            $Process->updated_at = null;
            $Process->save();
            return true;
        } catch (Exception $th) {
            throw new Exception($th->getMessage());
            return false;
        }
    }

    public function replaceDateApi(string $date){
        $date = str_replace("T", " ", $date);
        $date = str_replace(".000Z", "", $date);
        return strlen($date) == 0 ? null : $date;
    }

    /**
     * @param UploadedFile $File
     * @param int $id_Partner
     * @return string
     */
    public function uploadFile(UploadedFile $File, int $id_Partner){
        $imageName = time().'-process-'.time().".csv";
        $Path = "storage/".self::FOLDER.$id_Partner;
        $File->move($Path, $imageName);
        $local = $Path."/".$imageName;
        $public = env('APP_URL')."/".$local;
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
}
?>
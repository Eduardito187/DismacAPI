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
use App\Models\ProcessTask;
use App\Models\ProcessTaskLog;
use \Illuminate\Http\Request;
use \Exception;
use \Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class Import{
    CONST FOLDER = "Process/";
    CONST AUTH = "Wagento:wagento2021";
    CONST LOG_TEXT = "El proceso ID : %, del partner & fue ejecutado.";
    CONST FILE_NONE = "El archivo adjunto no existe.";
    CONST FILE_EMPTY = "El archivo adjunto se encuentra en vacío.";
    CONST FILE_EXIST = "El archivo adjunto existe.";
    CONST SKU_CONTENT = "La columna sku no se encontro al inicio del archivo adjunto.";
    CONST ERROR_1 = "Archivo vacío.";
    CONST ERROR_2 = "Archivo no encontrado.";
    CONST ERROR_3 = "Formato del archivo erroneo.";
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
    protected $iniDate;
    protected $endDate;
    protected $logProcess = [];
    protected $DataExcel = [];
    protected $MessageProcess = "Proceso ejecutado exitosamente.";
    protected $StatusProcess = true;

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
        $this->iniDate = strtotime($this->date->getFullDate());
        return $this->processApply($this->getProcess($params[$this->text->getId()]));
    }

    /**
     * @param Process $Process
     * @return bool
     */
    public function processApply(Process $Process){
        $this->validateFile($Process);
        $this->endDate = strtotime($this->date->getFullDate());
        $this->endProcessLog($Process);
        $this->saveHostoryLog($Process);
        return true;
    }

    /**
     * @param Process $Process
     * @return void
     */
    public function endProcessLog(Process $Process){
        $text = $this->getReplaceId($Process->id, self::LOG_TEXT);
        $text = $this->getReplacePartner($Process->PartnerProcess->name, $text);
        Log::channel('process_run')->info($text);
    }

    /**
     * @param Process $Process
     * @return bool
     */
    public function saveHostoryLog(Process $Process){
        try {
            $ProcessTask = new ProcessTask();
            $ProcessTask->id_process = $Process->id;
            $ProcessTask->id_partner = $Process->Partner;
            $ProcessTask->mensaje = $this->MessageProcess;
            $ProcessTask->duracion = $this->getDuracionProcess();
            $ProcessTask->status = $this->StatusProcess;
            $ProcessTask->created_at = $this->date->getFullDate();
            $ProcessTask->updated_at = null;
            $ProcessTask->save();
            $this->setAllHistory($ProcessTask->id);
            return true;
        } catch (Exception $th) {
            return false;
        }
    }

    /**
     * @param int $id
     * @return void
     */
    public function setAllHistory(int $id){
        foreach ($this->logProcess as $key => $Task) {
            $this->saveHostoryDetailLog($id, $Task[$this->text->getMensaje()], $Task[$this->text->getStatus()], $Task[$this->text->getCreated()]);
        }
    }

    /**
     * @param int $id
     * @return bool
     */
    public function saveHostoryDetailLog(int $id, string $mensaje, bool $status, string $created_at){
        try {
            $ProcessTaskLog = new ProcessTaskLog();
            $ProcessTaskLog->id_process_task = $id;
            $ProcessTaskLog->mensaje = $mensaje;
            $ProcessTaskLog->status = $status;
            $ProcessTaskLog->created_at = $created_at;
            $ProcessTaskLog->updated_at = null;
            $ProcessTaskLog->save();
            return true;
        } catch (Exception $th) {
            return false;
        }
    }

    /**
     * @return void
     */
    public function getDuracionProcess(){
        return ($this->endDate - $this->iniDate).$this->text->getDuracionProcess();
    }

    /**
     * @param Process $Process
     * @return bool
     */
    public function validateFile(Process $Process){
        $ProcessPath = $Process->Data->path;
        $path = public_path().$ProcessPath;
        if (file_exists($path)){
            $this->addLogHistory(self::FILE_EXIST, $this->status->getEnable(), $this->date->getFullDate());
            $this->DataExcel = $this->readFileCsv($path);
            if (count($this->DataExcel) == 0) {
                $this->errorProcess(self::ERROR_1);
                $this->addLogHistory(self::FILE_EMPTY, $this->status->getDisable(), $this->date->getFullDate());
                $this->updateStatusProcess($Process->id, $this->status->getDisable());
            }else{
                $this->validateHeadersCsv($Process, $this->DataExcel[0]);
            }
        }else{
            $this->errorProcess(self::ERROR_2);
            $this->addLogHistory(self::FILE_NONE, $this->status->getDisable(), $this->date->getFullDate());
            $this->updateStatusProcess($Process->id, $this->status->getDisable());
        }
    }

    /**
     * @param string $mensaje
     * @param bool $status
     * @param string $created_at
     */
    public function addLogHistory(string $mensaje, bool $status, string $created_at){
        $this->logProcess[] = array(
            $this->text->getMensaje() => $mensaje,
            $this->text->getStatus() => $status,
            $this->text->getCreated() => $created_at
        );
    }

    /**
     * @param Process $Process
     * @param array $HeaderCsv
     * @return void
     */
    public function validateHeadersCsv(Process $Process, array $HeaderCsv){
        for ($i=0; $i < count($HeaderCsv); $i++) { 
            $code = strtolower($HeaderCsv[$i]);
            if ($i == 0 && $code != $this->text->getSku()){
                $this->errorProcess(self::ERROR_3);
                $this->addLogHistory(self::SKU_CONTENT, $this->status->getDisable(), $this->date->getFullDate());
                $this->updateStatusProcess($Process->id, $this->status->getDisable());
                $i = count($HeaderCsv);
            }
        }
    }

    /**
     * @param string $msg
     * @return void
     */
    public function errorProcess(string $msg){
        $this->MessageProcess = $msg;
        $this->StatusProcess = $this->status->getDisable();
    }

    /**
     * @param string $path
     * @return array
     */
    public function readFileCsv(string $path){
        $DataCsv = array();
        if (($open = fopen($path, "r")) !== FALSE) {
            while (($data = fgetcsv($open, 1000, ",")) !== FALSE) {
                $DataCsv[] = $data;
            }
            fclose($open);
        }
        return $DataCsv;
    }

    /**
     * @param int $id
     * @param int $status
     * @return void
     */
    public function updateStatusProcess(int $id, int $status){
        Process::where($this->text->getId(), $id)->update([
            $this->text->getStatusColumn() => $status,
            $this->text->getUpdated() => $this->date->getFullDate()
        ]);
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
        $local = "/".$Path."/".$imageName;
        $public = env('APP_URL').$local;
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
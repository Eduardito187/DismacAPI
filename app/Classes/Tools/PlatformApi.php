<?php

namespace App\Classes\Tools;

use App\Classes\Helper\Text;
use Exception;
use \Illuminate\Http\Request;
use App\Classes\Helper\Date;
use App\Classes\Helper\Status;
use App\Models\Delimitations;
use App\Models\Localization;
use App\Models\Permissions;
use App\Models\Rol;
use App\Models\RolPermissions;
use App\Models\Store;

class PlatformApi{
    /**
     * @var Text
     */
    protected $text;
    /**
     * @var Date
     */
    protected $date;
    /**
     * @var Status
     */
    protected $status;

    public function __construct() {
        $this->text = new Text();
        $this->date = new Date();
        $this->status = new Status();
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function modifyPermissions(Request $request){
        $params = $request->all();
        if (isset($params[$this->text->getRol()]) && isset($params[$this->text->getPermissions()])){
            $rol = $this->getRolByCode($params[$this->text->getRol()]);
            $this->clearAllRolPermission($rol->id);
            foreach ($params[$this->text->getPermissions()] as $key => $permissionApi) {
                $permission = $this->getPermissionByCode($permissionApi);
                $this->createRolPermission($permission->id, $rol->id);
            }
            return $this->status->getEnable();
        }else{
            throw new Exception($this->text->getParametersNone());
        }
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function addPermission(Request $request){
        $params = $request->all();
        if (isset($params[$this->text->getRol()]) && isset($params[$this->text->getPermission()])){
            $rol = $this->getRolByCode($params[$this->text->getRol()]);
            $permission = $this->getPermissionByCode($params[$this->text->getPermission()]);
            return $this->createRolPermission($permission->id, $rol->id);
        }else{
            throw new Exception($this->text->getParametersNone());
        }
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function removePermission(Request $request){
        $params = $request->all();
        if (isset($params[$this->text->getRol()]) && isset($params[$this->text->getPermission()])){
            $rol = $this->getRolByCode($params[$this->text->getRol()]);
            $permission = $this->getPermissionByCode($params[$this->text->getPermission()]);
            return $this->clearRolPermission($permission->id, $rol->id);
        }else{
            throw new Exception($this->text->getParametersNone());
        }
    }
    
    /**
     * @param int $IdRol
     * @return bool
     */
    public function clearAllRolPermission(int $IdRol){
        return RolPermissions::where($this->text->getIdRol(), $IdRol)->delete();
    }
    
    /**
     * @param int $IdRolPermissions
     * @param int $IdRol
     * @return bool
     */
    public function clearRolPermission(int $IdRolPermissions, int $IdRol){
        return RolPermissions::where($this->text->getIdRolPermissions(), $IdRolPermissions)->where($this->text->getIdRol(), $IdRol)->delete();
    }

    /**
     * @param int $IdRolPermissions
     * @param int $IdRol
     * @return bool
     */
    public function createRolPermission(int $IdRolPermissions, int $IdRol){
        try {
            $RolPermissions = new RolPermissions();
            $RolPermissions->id_permissions = $IdRolPermissions;
            $RolPermissions->id_rol = $IdRol;
            $RolPermissions->save();
            return $this->status->getEnable();
        } catch (Exception $th) {
            return $this->status->getDisable();
        }
    }

    /**
     * @param string $code
     * @return Rol
     */
    public function getRolByCode(string $code){
        $rol = Rol::where($this->text->getCode(), $code)->first();
        if (!$rol){
            throw new Exception($this->text->getRolNone());
        }
        return $rol;
    }

    /**
     * @param string $code
     * @return Permissions
     */
    public function getPermissionByCode(string $code){
        $permission = Permissions::where($this->text->getCode(), $code)->first();
        if (!$permission){
            throw new Exception($this->text->getPermissionNone());
        }
        return $permission;
    }
    
    /**
     * @param Request $request
     * @return bool
     */
    public function storeProcess(Request $request){
        $param = $request->all();
        if (isset($param[$this->text->getStore()])){
            $this->disableAllStore();
            foreach ($param[$this->text->getStore()] as $key => $store) {
                try {
                    $id_store = $this->getStoreById($store[$this->text->getId()]);
                    if (is_null($id_store)){
                        $this->createStore($store[$this->text->getId()], $store[$this->text->getName()], $store[$this->text->getCode()]);
                    }else{
                        $this->updateStore($store[$this->text->getId()], $store[$this->text->getName()], $store[$this->text->getCode()]);
                    }
                } catch (\Throwable $th) {
                    //throw $th;
                }
            }
        }else{
            throw new Exception($this->text->getParametersNone());
        }
        return $this->status->getEnable();
    }

    /**
     * @param int $id
     * @return int|null
     */
    public function getStoreById(int $id){
        $store = Store::find($id);
        if (!$store){
            return null;
        }
        return $store->id;
    }

    /**
     * @param string $code
     * @return int|null
     */
    public function getStoreByCode(string $code){
        $store = Store::where($this->text->getCode(), $code)->first();
        if (!$store){
            return null;
        }
        return $store->id;
    }

    /**
     * @param int $id
     * @param string $name
     * @param string $code
     * @return void
     */
    public function updateStore(int $id, string $name, string $code){
        Store::where($this->text->getId(), $id)->update([
            $this->text->getName() => $name,
            $this->text->getCode() => $code,
            $this->text->getStatus() => $this->status->getEnable(),
            $this->text->getUpdated() => $this->date->getFullDate()
        ]);
    }

    /**
     * @param int $id
     * @return bool
     */
    public function enableStore(int $id){
        $store = $this->getStoreById($id);
        if (is_null($store)){
            throw new Exception($this->text->getStoreNoneId());
        }
        $this->updateStatusStore($store, $this->status->getEnable());
        return $this->status->getEnable();
    }

    /**
     * @param int $id
     * @return bool
     */
    public function disableStore(int $id){
        $store = $this->getStoreById($id);
        if (is_null($store)){
            throw new Exception($this->text->getStoreNoneId());
        }
        $this->updateStatusStore($store, $this->status->getDisable());
        return $this->status->getEnable();
    }

    /**
     * @param int $id
     * @param bool $status
     * @return void
     */
    public function updateStatusStore(int $id, bool $status){
        Store::where($this->text->getId(), $id)->update([
            $this->text->getStatus() => $status,
            $this->text->getUpdated() => $this->date->getFullDate()
        ]);
    }

    /**
     * @param int $id
     * @param string $name
     * @param string $code
     * @return int
     */
    public function createStore(int $id, string $name, string $code){
        try {
            $Store = new Store();
            $Store->id = $id;
            $Store->name = $name;
            $Store->code = $code;
            $Store->created_at = $this->date->getFullDate();
            $Store->updated_at = null;
            $Store->status = $this->status->getEnable();
            $Store->save();
            return $Store->id;
        } catch (Exception $th) {
            throw new Exception($th->getMessage());
        }
    }

    /**
     * @return void
     */
    public function disableAllStore(){
        Store::where($this->text->getId(), $this->text->getSymbolMayor(), $this->text->getCero())->update([
            $this->text->getStatus() => $this->status->getDisable(),
            $this->text->getUpdated() => $this->date->getFullDate()
        ]);
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function delimitationProcess(Request $request){
        $param = $request->all();
        if (isset($param[$this->text->getDelimitation()])){
            $this->disableAllDelimitations();
            foreach ($param[$this->text->getDelimitation()] as $key => $delimitation) {
                try {
                    $id_store = $this->getStoreByCode($delimitation[$this->text->getStore()]);
                    if (!is_null($id_store)){
                        $id_geo = $this->getGeo($delimitation[$this->text->getLatitud()], $delimitation[$this->text->getLongitud()]);
                        if (is_null($id_geo)){
                            $id_geo = $this->createGeo($delimitation[$this->text->getLatitud()], $delimitation[$this->text->getLongitud()]);
                        }
                    }
                    $id_delimitation = $this->getDelimitation($id_store, $id_geo);
                    if (is_null($id_delimitation)){
                        $this->createDelimitation($id_store, $id_geo);
                    }
                } catch (\Throwable $th) {
                    //throw $th;
                }
            }
        }else{
            throw new Exception($this->text->getParametersNone());
        }
        return $this->status->getEnable();
    }

    /**
     * @param int $id
     * @param int $id_store
     * @param int $id_geo
     * @return void
     */
    public function updateDelimitation(int $id, int $id_store, int $id_geo){
        Delimitations::where($this->text->getId(), $id)->update([
            $this->text->getIdStore() => $id_store,
            $this->text->getIdLocalization() => $id_geo,
            $this->text->getStatus() => $this->status->getEnable(),
            $this->text->getUpdated() => $this->date->getFullDate()
        ]);
    }

    /**
     * @param int $id_store
     * @param int $id_geo
     * @return int|null
     */
    public function getDelimitation(int $id_store, int $id_geo){
        $Delimitations = Delimitations::where($this->text->getIdStore(), $id_store)->where($this->text->getIdLocalization(), $id_geo)->first();
        if (!$Delimitations){
            return null;
        }
        return $Delimitations->id;
    }

    /**
     * @param int $id_store
     * @param int $id_localization
     * @return int|null
     */
    public function createDelimitation(int $id_store, int $id_localization){
        try {
            $Delimitations = new Delimitations();
            $Delimitations->id_store = $id_store;
            $Delimitations->id_localization = $id_localization;
            $Delimitations->status = $this->status->getEnable();
            $Delimitations->created_at = $this->date->getFullDate();
            $Delimitations->updated_at = null;
            $Delimitations->save();
            return $Delimitations->id;
        } catch (Exception $th) {
            return null;
        }
    }
    
    /**
     * @param string $latitud
     * @param string $longitud
     * @return int|null
     */
    public function getGeo(string $latitud, string $longitud){
        $Localization = Localization::where($this->text->getLatitud(), $latitud)->where($this->text->getLongitud(), $longitud)->first();
        if (!$Localization){
            return null;
        }
        return $Localization->id;
    }

    /**
     * @param string $latitud
     * @param string $longitud
     * @return int|null
     */
    public function createGeo(string $latitud, string $longitud){
        try {
            $Localization = new Localization();
            $Localization->latitud = $latitud;
            $Localization->longitud = $longitud;
            $Localization->save();
            return $Localization->id;
        } catch (Exception $th) {
            return null;
        }
    }

    /**
     * @return void
     */
    public function disableAllDelimitations(){
        Delimitations::where($this->text->getId(), $this->text->getSymbolMayor(), $this->text->getCero())->update([
            $this->text->getStatus() => $this->status->getDisable(),
            $this->text->getUpdated() => $this->date->getFullDate()
        ]);
    }
}
?>
<?php

namespace App\Classes\Tools;

use App\Classes\Helper\Text;
use Exception;
use \Illuminate\Http\Request;
use App\Classes\Helper\Date;
use App\Classes\Helper\Status;
use App\Models\Config;
use App\Models\Delimitations;
use App\Models\Localization;
use App\Models\MunicipalityPos;
use App\Models\Permissions;
use App\Models\ProductWarehouse;
use App\Models\Rol;
use App\Models\RolPermissions;
use App\Models\Store;
use App\Models\Warehouse;
use App\Classes\Account\AccountApi;
use App\Classes\Tools\Sockets;

class PlatformApi{
    const CODE_VERSION = "version";
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
    /**
     * @var AccountApi
     */
    protected $AccountApi;
    /**
     * @var Sockets
     */
    protected $Sockets;

    public function __construct() {
        $this->text = new Text();
        $this->date = new Date();
        $this->status = new Status();
        $this->AccountApi = new AccountApi();
        $this->Sockets = new Sockets();
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
     * @return void
     */
    public function disableAllMunicipality(){
        MunicipalityPos::where($this->text->getId(), $this->text->getSymbolMayor(), $this->text->getCero())->update([
            $this->text->getStatus() => $this->status->getDisable(),
            $this->text->getUpdated() => $this->date->getFullDate()
        ]);
    }

    /**
     * @return void
     */
    public function disableAllWarehouses(){
        Warehouse::where($this->text->getId(), $this->text->getSymbolMayor(), $this->text->getCero())->update([
            $this->text->getStatus() => $this->status->getDisable(),
            $this->text->getUpdated() => $this->date->getFullDate()
        ]);
    }

    /**
     * @param string $almacen
     * @return int|null
     */
    public function getWarehouseByCode(string $almacen){
        $warehouse = Warehouse::where($this->text->getAlmacen(), $almacen)->first();
        if (!$warehouse){
            return null;
        }
        return $warehouse->id;
    }


    /**
     * @param Request $request
     * @param array $data
     * @return bool
     */
    public function verifyVersion(Request $request, array $data){
        $Config = $this->getConfigValue(self::CODE_VERSION);
        if (is_null($Config)){
            return false;
        }
        if (array_key_exists($this->text->getVersion(), $data)){
            $result = version_compare($data[$this->text->getVersion()], $Config);
            $Account = $this->AccountApi->getAccountByToken($request->header($this->text->getAuthorization()));
            $status = false;
            if ($result < 0) {
                $status = false;
            } elseif ($result > 0) {
                $status = false;
            } else {
                $status = true;
            }
            $data = array($this->text->getIdAccount() => $Account->id, $this->text->getStatus() => $status);
            $this->Sockets->sendQueryPost($this->text->getVersionVerify(), $data);
            return $status;
        }else{
            return false;
        }
    }

    public function getConfigValue(string $code){
        $Config = Config::where($this->text->getCode(), $code)->first();
        if (!$Config){
            return null;
        }
        return $Config->value;
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function warehouseProcess(Request $request){
        $param = $request->all();
        $listProcessWarehouse = array();
        if (isset($param[$this->text->getWarehouses()])){
            $this->disableAllWarehouses();
            foreach ($param[$this->text->getWarehouses()] as $key => $warehouses) {
                try {
                    $id_store = $this->getStoreByName($warehouses[$this->text->getStore()]);
                    $warehouse = $this->getWarehouseByCode($warehouses[$this->text->getAlmacen()]);
                    $idWarehouse = 0;
                    if (!is_null($id_store)){
                        if (is_null($warehouse)){
                            $idWarehouse = $this->setWarehouse(
                                $warehouses[$this->text->getName()],
                                $warehouses[$this->text->getCode()],
                                $warehouses[$this->text->getBase()],
                                $warehouses[$this->text->getAlmacen()],
                                $warehouses[$this->text->getMunicipioApi()] ?? null
                            );
                        }else{
                            $idWarehouse = $warehouse;
                            $this->updateWarehouse(
                                $warehouse,
                                $warehouses[$this->text->getName()],
                                $warehouses[$this->text->getCode()],
                                $warehouses[$this->text->getBase()],
                                $warehouses[$this->text->getMunicipioApi()] ?? null
                            );
                        }
                        $listProcessWarehouse[] = $idWarehouse;
                    }
                } catch (\Throwable $th) {
                    //throw $th;
                }
            }
            $this->clearStockWarehouseDisable($listProcessWarehouse);
        }else{
            throw new Exception($this->text->getParametersNone());
        }
        return $this->status->getEnable();
    }

    /**
     * @param array $list
     * @return void
     */
    public function clearStockWarehouseDisable(array $list){
        $listWarehouse = Warehouse::whereNotIn($this->text->getId(), $list)->pluck($this->text->getId());
        foreach ($listWarehouse as $key => $wh) {
            $this->clearStockWarehouse($wh);
        }
    }

    /**
     * @param int $id
     * @return void
     */
    public function clearStockWarehouse(int $id){
        ProductWarehouse::where($this->text->getIdWarehouse(), $id)->update([
            $this->text->getStock() => $this->text->getCero(),
            $this->text->getUpdated() => $this->date->getFullDate()
        ]);
    }
    
    /**
     * @param string $name
     * @param string $code
     * @param bool $base
     * @param string $almacen
     * @param int|null $id_municipio
     * @return int
     */
    public function setWarehouse(string $name, string $code, bool $base, string $almacen, int|null $id_municipio){
        try {
            $Warehouse = new Warehouse();
            $Warehouse->name = $name;
            $Warehouse->code = $code;
            $Warehouse->base = $base;
            $Warehouse->almacen = $almacen;
            $Warehouse->created_at = $this->date->getFullDate();
            $Warehouse->updated_at = null;
            $Warehouse->id_municipality_pos = $id_municipio;
            $Warehouse->status = $this->status->getEnable();
            $Warehouse->save();
            return $Warehouse->id;
        } catch (Exception $th) {
            throw new Exception($th->getMessage());
        }
    }

    /**
     * @param int $id
     * @param string $name
     * @param string $code
     * @param bool $base
     * @param int|null $id_municipio
     * @return void
     */
    public function updateWarehouse(int $id, string $name, string $code, bool $base, int|null $id_municipio){
        Warehouse::where($this->text->getId(), $id)->update([
            $this->text->getName() => $name,
            $this->text->getCode() => $code,
            $this->text->getBase() => $base,
            $this->text->getIdMunicipalitypos() => $id_municipio,
            $this->text->getStatus() => $this->status->getEnable(),
            $this->text->getUpdated() => $this->date->getFullDate()
        ]);
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function municipalityProcess(Request $request){
        $param = $request->all();
        if (isset($param[$this->text->getMunicipios()])){
            $this->disableAllMunicipality();
            foreach ($param[$this->text->getMunicipios()] as $key => $municipality) {
                try {
                    $id_store = $this->getStoreByName($municipality[$this->text->getNombre()]);
                    if (!is_null($id_store)){
                        $this->processAllStore($id_store, $municipality[$this->text->getMunicipios()]);
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
     * @param int $id_store
     * @param array $municipios
     * @return bool
     */
    public function processAllStore(int $id_store, array $municipios){
        foreach ($municipios as $key => $municipio) {
            $minipioPos = $this->getMunicipalityPos($municipio[$this->text->getId()]);
            if (!$minipioPos){
                $this->createMunicipalityPos($id_store, $municipio[$this->text->getNombre()], $municipio[$this->text->getId()]);
            }else{
                $this->updateMunicipalityPos($minipioPos->id);
            }
        }
    }

    /**
     * @param int $id
     * @return void
     */
    public function updateMunicipalityPos(int $id){
        MunicipalityPos::where($this->text->getId(), $id)->update([
            $this->text->getStatus() => $this->status->getEnable(),
            $this->text->getUpdated() => $this->date->getFullDate()
        ]);
    }
    
    /**
     * @param int $id_store
     * @param string $name
     * @param int $id
     * @return int
     */
    public function createMunicipalityPos(int $id_store, string $name, int $id){
        try {
            $MunicipalityPos = new MunicipalityPos();
            $MunicipalityPos->id = $id;
            $MunicipalityPos->name = $name;
            $MunicipalityPos->id_store = $id_store;
            $MunicipalityPos->created_at = $this->date->getFullDate();
            $MunicipalityPos->updated_at = null;
            $MunicipalityPos->status = $this->status->getEnable();
            $MunicipalityPos->save();
            return $MunicipalityPos->id;
        } catch (Exception $th) {
            throw new Exception($th->getMessage());
        }
    }

    /**
     * @param int $id
     * @return MunicipalityPos|null
     */
    public function getMunicipalityPos(int $id){
        return MunicipalityPos::find($id);
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
     * @param string|null $name
     * @return int|null
     */
    public function getStoreByName(string|null $name){
        if (is_null($name)){
            return null;
        }
        $store = Store::where($this->text->getName(), $this->text->getLike(), $this->text->getPercent().$name.$this->text->getPercent())->first();
        if (!$store){
            return null;
        }
        return $store->id;
    }

    /**
     * @param string|null $code
     * @return int|null
     */
    public function getStoreByCode(string|null $code){
        if (is_null($code)){
            return null;
        }
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
                    $id_store = $this->getStoreByCode($delimitation[$this->text->getStore()] ?? null);

                    $id_geo = $this->getGeo($delimitation[$this->text->getLatitud()], $delimitation[$this->text->getLongitud()]);
                    if (is_null($id_geo)){
                        $id_geo = $this->createGeo($delimitation[$this->text->getLatitud()], $delimitation[$this->text->getLongitud()]);
                    }

                    $idMunicipio = $delimitation[$this->text->getMunicipioApi()] ?? null;
                    $minipioPos = $this->getMunicipalityPos($idMunicipio);

                    if (!$minipioPos){
                        $idMunicipio = null;
                    }else{
                        $id_store = $minipioPos->id_store;
                    }

                    $id_delimitation = $this->getDelimitation($id_store, $id_geo, $idMunicipio);
                    if (is_null($id_delimitation)){
                        $this->createDelimitation($id_store, $id_geo, $idMunicipio);
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
     * @param int|null $id_store
     * @param int|null $id_geo
     * @param int|null $id_municipio
     * @return int|null
     */
    public function getDelimitation(int|null $id_store, int|null $id_geo, int|null $id_municipio){
        $Delimitations = Delimitations::where($this->text->getIdStore(), $id_store)->where($this->text->getIdLocalization(), $id_geo)->where($this->text->getIdMunicipalitypos(), $id_municipio)->first();
        if (!$Delimitations){
            return null;
        }
        return $Delimitations->id;
    }

    /**
     * @param int|null $id_store
     * @param int|null $id_geo
     * @param int|null $id_municipio
     * @return int|null
     */
    public function createDelimitation(int|null $id_store, int|null $id_geo, int|null $id_municipio){
        try {
            $Delimitations = new Delimitations();
            $Delimitations->id_store = $id_store;
            $Delimitations->id_localization = $id_geo;
            $Delimitations->status = $this->status->getEnable();
            $Delimitations->created_at = $this->date->getFullDate();
            $Delimitations->updated_at = null;
            $Delimitations->id_municipality_pos = $id_municipio;
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
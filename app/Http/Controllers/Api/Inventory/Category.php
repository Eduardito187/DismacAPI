<?php

namespace App\Http\Controllers\Api\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Classes\Account\AccountApi;
use App\Classes\Helper\Text;
use App\Classes\Helper\Status;
use \Exception;
use Illuminate\Support\Facades\Log;
use App\Classes\Partner\Inventory\Catalog as CatalogApi;

class Category extends Controller
{
    protected $addressApi;
    protected $accountApi;
    protected $partnerApi;
    protected $text;
    protected $status;
    protected $catalogApi;

    public function __construct() {
        $this->accountApi = new AccountApi();
        $this->text       = new Text();
        $this->status     = new Status();
        $this->catalogApi = new CatalogApi();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $response = array();
        try {
            if (!is_null($request->all()[$this->text->getIdCatalog()]) && !is_null($request->all()[$this->text->getName()])) {
                Log::debug(json_encode($request->all()));
                $this->catalogApi->newCategory(
                    $request->all()[$this->text->getIdCatalog()],
                    $request->all()[$this->text->getName()],
                    $this->accountApi->getAccountToken($request->header($this->text->getAuthorization())),
                    $request->all()[$this->text->getStores()],
                    $request->all()[$this->text->getEstado()],
                    $request->all()[$this->text->getVisible()],
                    $request->all()[$this->text->getFiltros()],
                    $request->all()[$this->text->getIdPos()],
                    $request->all()[$this->text->getUrl()],
                    $request->all()[$this->text->getSubCategoryPos()],
                    $request->all()[$this->text->getInhitance()],
                    $request->all()[$this->text->getProductos()],
                    $request->all()[$this->text->getLanding()],
                    $request->all()[$this->text->getMetadata()],
                    $request->all()[$this->text->getCustom()]
                );
                $response = $this->text->getResponseApi($this->status->getEnable(), $this->text->getAddSuccess());
            }else{
                throw new Exception($this->text->getErrorParametros());
            }
        } catch (Exception $th) {
            $response = $this->text->getResponseApi($this->status->getDisable(), $th->getMessage());
        }
        return response()->json($response);
    }

    /**
     * Display the specified resource.
     *
     * @param int|null  $id_category
     * @param int|null $id_catalog
     * @return \Illuminate\Http\Response
     */
    public function show(int|null $id_category, int|null $id_catalog)
    {
        $response = array();
        try {
            if (!is_null($id_category) && !is_null($id_catalog)) {
                $response = $this->text->getResponseApi(
                    $this->catalogApi->getCategory(
                        $id_category,
                        $id_catalog
                    ),
                    $this->text->getQuerySuccess()
                );
            }else{
                throw new Exception($this->text->getErrorParametros());
            }
        } catch (Exception $th) {
            $response = $this->text->getResponseApi($this->status->getDisable(), $th->getMessage());
        }
        return response()->json($response);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}

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
                $this->catalogApi->newCategory(
                    $request->all()[$this->text->getIdCatalog()],
                    $request->all()[$this->text->getName()],
                    $this->accountApi->getAccountToken($request->header($this->text->getAuthorization())),
                    $request->all()[$this->text->getStores()]
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
                    $this->text->getAddSuccess()
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

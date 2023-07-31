<?php

namespace App\Http\Controllers\Api\Inventory\Category;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Classes\Account\AccountApi;
use App\Classes\Helper\Text;
use App\Classes\Helper\Status;
use Exception;
use Illuminate\Support\Facades\Log;
use App\Classes\Partner\Inventory\Catalog as CatalogApi;

class RemoveProducts extends Controller
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $response = array();
        try {
            if (!is_null($request->all()[$this->text->getIdCatalog()]) && 
            !is_null($request->all()[$this->text->getIdCategory()]) && 
            !is_null($request->all()[$this->text->getProducts()])) {
                $this->catalogApi->desasignarProductos(
                    $request->all()[$this->text->getIdCatalog()],
                    $request->all()[$this->text->getIdCategory()],
                    $request->all()[$this->text->getProducts()]
                );
                $response = $this->text->getResponseApi($this->catalogApi->getResponseAPI(), $this->text->getQuerySuccess());
            }else{
                throw new Exception($this->text->getErrorParametros());
            }
        } catch (Exception $th) {
            $response = $this->text->getResponseApi($this->status->getDisable(), $th->getMessage());
        }
        return response()->json($response);
    }
}
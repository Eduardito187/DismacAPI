<?php

namespace App\Http\Controllers\Api\Inventory\Stock;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Classes\Account\AccountApi;
use App\Classes\Helper\Text;
use App\Classes\Helper\Status;
use Exception;
use App\Classes\Partner\Inventory\Catalog as CatalogApi;

class Stock extends Controller
{
    protected $addressApi;
    protected $accountApi;
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
            if (!is_null($request->all()[$this->text->getProductos()])) {
                $this->catalogApi->updateStock(
                    $request->all()[$this->text->getProductos()]
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

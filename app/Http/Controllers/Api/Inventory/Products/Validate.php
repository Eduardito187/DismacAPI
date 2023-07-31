<?php

namespace App\Http\Controllers\Api\Inventory\Products;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Classes\Helper\Text;
use App\Classes\Helper\Status;
use \Illuminate\Support\Facades\Log;
use \Illuminate\Http\Response;
use App\Classes\Product\ProductApi;
use App\Classes\Account\AccountApi;
use \Exception;

class Validate extends Controller
{
    protected $productApi;
    protected $accountApi;
    protected $text;
    protected $status;

    public function __construct() {
        $this->productApi = new ProductApi();
        $this->accountApi = new AccountApi();
        $this->text       = new Text();
        $this->status     = new Status();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            if (empty($request->all()[$this->text->getSku()]) || !is_array($request->all()[$this->text->getSku()])) {
                throw new Exception($this->text->getErrorParametros());
            }
            $id_Partner = $this->accountApi->getPartnerId($this->accountApi->getAccountToken($request->header($this->text->getAuthorization())));
            $response = $this->text->getResponseApi(
                $this->productApi->getProductsBySku($id_Partner,$request->all()[$this->text->getSku()]),
                $this->text->getQuerySuccess()
            );
        } catch (Exception $th) {
            $response = $this->text->getResponseApi([], $th->getMessage());
        }
        return response()->json($response);
    }
}
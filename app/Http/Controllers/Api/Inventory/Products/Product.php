<?php

namespace App\Http\Controllers\Api\Inventory\Products;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Classes\Helper\Text;
use App\Classes\Helper\Status;
use \Illuminate\Support\Facades\Log;
use \Illuminate\Http\Response;
use App\Classes\Product\ProductApi;
use \Exception;

class Product extends Controller
{
    protected $productApi;
    protected $text;
    protected $status;

    public function __construct() {
        $this->productApi = new ProductApi();
        $this->text       = new Text();
        $this->status     = new Status();
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function clacom(Request $request)
    {
        try {
            $response = $this->text->getResponseApi(
                $this->productApi->processClacom($request),
                $this->text->getUpdateSuccess()
            );
        } catch (Exception $th) {
            $response = $this->text->getResponseApi(null, $th->getMessage());
        }
        return response()->json($response);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function seturl(Request $request)
    {
        try {
            $response = $this->text->getResponseApi(
                $this->productApi->setUrl($request),
                $this->text->getUpdateSuccess()
            );
        } catch (Exception $th) {
            $response = $this->text->getResponseApi(null, $th->getMessage());
        }
        return response()->json($response);
    }

    /**
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getAttributes($id)
    {
        try {
            $response = $this->text->getResponseApi(
                $this->productApi->getProductAttributesArray($id),
                $this->text->getQuerySuccess()
            );
        } catch (Exception $th) {
            $response = $this->text->getResponseApi(null, $th->getMessage());
        }
        return response()->json($response);
    }

    /**
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getPrices($id)
    {
        try {
            $response = $this->text->getResponseApi(
                $this->productApi->getProductPricesArray($id),
                $this->text->getQuerySuccess()
            );
        } catch (Exception $th) {
            $response = $this->text->getResponseApi(null, $th->getMessage());
        }
        return response()->json($response);
    }

    /**
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getPosData($id)
    {
        try {
            $response = $this->text->getResponseApi(
                $this->productApi->getPosData($id),
                $this->text->getQuerySuccess()
            );
        } catch (Exception $th) {
            $response = $this->text->getResponseApi(null, $th->getMessage());
        }
        return response()->json($response);
    }

    /**
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getStatus($id)
    {
        try {
            $response = $this->text->getResponseApi(
                $this->productApi->getProductStatusArray($id),
                $this->text->getQuerySuccess()
            );
        } catch (Exception $th) {
            $response = $this->text->getResponseApi(null, $th->getMessage());
        }
        return response()->json($response);
    }

    /**
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $response = $this->text->getResponseApi(
                $this->productApi->getProductArray($id),
                $this->text->getQuerySuccess()
            );
        } catch (Exception $th) {
            $response = $this->text->getResponseApi(null, $th->getMessage());
        }
        return response()->json($response);
    }

    /**
     * @param \Illuminate\Http\Request  $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function updateAttributes(Request $request, $id)
    {
        try {
            $response = $this->text->getResponseApi(
                $this->productApi->updateAttributes($request, $id),
                $this->text->getQuerySuccess()
            );
        } catch (Exception $th) {
            $response = $this->text->getResponseApi(null, $th->getMessage());
        }
        return response()->json($response);
    }

    /**
     * @param \Illuminate\Http\Request  $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $response = $this->text->getResponseApi(
                $this->productApi->updateStatus($request, $id),
                $this->text->getQuerySuccess()
            );
        } catch (Exception $th) {
            $response = $this->text->getResponseApi(null, $th->getMessage());
        }
        return response()->json($response);
    }
    
    /**
     * @param \Illuminate\Http\Request  $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function updatePrices(Request $request, $id)
    {
        try {
            $response = $this->text->getResponseApi(
                $this->productApi->updatePrices($request, $id),
                $this->text->getQuerySuccess()
            );
        } catch (Exception $th) {
            $response = $this->text->getResponseApi(null, $th->getMessage());
        }
        return response()->json($response);
    }
}
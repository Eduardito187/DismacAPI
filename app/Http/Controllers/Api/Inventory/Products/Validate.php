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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json([]);
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
            $id_Partner = $this->accountApi->getPartnerId($this->accountApi->getAccountToken($request->header($this->text->getAuthorization())));
            $products = $this->productApi->getProductsBySku($id_Partner,$request->all()[$this->text->getSku()]);
            $response = $this->text->getResponseApi($products, $this->text->getImportSuccess());
        } catch (Exception $th) {
            $response = $this->text->getResponseApi([], $th->getMessage());
        }
        return response()->json($response);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json([]);
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
        return response()->json([]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return response()->json([]);
    }
}

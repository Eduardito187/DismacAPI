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
        return response()->json([]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $response = $this->text->getResponseApi(
                $this->productApi->getProductArray($id),
                $this->text->getAccountExist()
            );
        } catch (Exception $th) {
            $response = $this->text->getResponseApi(null, $th->getMessage());
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

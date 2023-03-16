<?php

namespace App\Http\Controllers\Api\Report;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Classes\Product\ProductApi;

class Products extends Controller
{
    protected $_ProductApi;

    public function __construct() {
        $this->_ProductApi = new ProductApi();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $Products = Product::where("stock" > 0)->skip(200)->take(100)->get();
        $repsonse = array();
        $Stores = $this->_ProductApi->getAllStore();
        foreach ($Products as $p) {
            $Product = array(
                "id" => $p->id,
                "name" => $p->name,
                "sku" => $p->sku,
                "brand" => $p->id_brand == null ? "" : $this->_ProductApi->getBrandName($p->id_brand),
                "clacom" => $p->id_clacom == null ? "" : $this->_ProductApi->getClacomLabel($p->id_clacom),
                "store" => "",
                "status" => "Disable",
                "stock" => "",
                "price" => 0,
                "special_price" => 0
            );

            $ProductStore = $this->_ProductApi->getProductStatus($p->id);
            if (!is_null($ProductStore)) {
                for ($i=0; $i < count($ProductStore); $i++) { 
                    $Product["store"] = $this->_ProductApi->readAllStore($Stores, $ProductStore[$i]["id_store"]);
                    $Product["status"] = $ProductStore[$i]["status"] == 0 ? "Disable" : "Enable";
                    $Product["stock"] = $this->_ProductApi->getProductStockStore($p->id, $ProductStore[$i]["id_store"]);
                    $id_price = $this->_ProductApi->getProductPriceStore($ProductStore[$i]["id_store"], $p->id);
                    if (!is_null($id_price)) {
                        $price_array = $this->_ProductApi->getPriceProduct($id_price);
                        if (!is_null($price_array)) {
                            $Product["price"] = $price_array["price"];
                            $Product["special_price"] = $price_array["special_price"] == null ? "" : $price_array["special_price"];
                        }
                    }
                    $repsonse[] = $Product;
                }
            }
        }
        return response()->json($repsonse);
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

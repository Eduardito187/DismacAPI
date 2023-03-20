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
        $Products = Product::where("stock", ">" ,0)->skip(200)->take(100)->get();
        $repsonse = array();
        $Stores = $this->_ProductApi->getAllStore();
        foreach ($Products as $p) {
            $Product = array(
                "id" => $p->id,
                "name" => $p->name,
                "sku" => $p->sku,
                "brand" => $p->id_brand == null ? "" : $this->_ProductApi->getBrandName($p->id_brand),
                "clacom" => $p->id_clacom == null ? "" : $this->_ProductApi->getClacomLabel($p->id_clacom),
                "store" => ""
            );

            $ProductStore = $this->_ProductApi->getProductStatus($p->id);
            $stores = array();
            if (!is_null($ProductStore)) {
                for ($i=0; $i < count($ProductStore); $i++) {
                    $id_price = $this->_ProductApi->getProductPriceStore($ProductStore[$i]["id_store"], $p->id);
                    $warehouses = [];
                    $price = 0;
                    $special_price = 0;
                    if (!is_null($id_price)) {
                        $price_array = $this->_ProductApi->getPriceProduct($id_price);
                        if (!is_null($price_array)) {
                            $price = $price_array["price"];
                            $special_price = $price_array["special_price"] == null ? "" : $price_array["special_price"];
                        }
                    }
                    $Stock = 0;
                    $WarehouseStore = $this->_ProductApi->getStoreWarehouse($p->id, $ProductStore[$i]["id_store"]);
                    foreach ($WarehouseStore as $WS) {
                        $WSInfo = $this->_ProductApi->getWarehouseName($WS["id_warehouse"]);
                        $code = "";
                        $name = "";
                        if (!is_null($WSInfo)) {
                            $name = $WSInfo["name"];
                            $code = $WSInfo["code"];
                        }
                        $warehouses[] = array(
                            "id_warehouse" => $WS["id_warehouse"],
                            "name_warehouse" => $name,
                            "code_warehouse" => $code,
                            "stock" => $WS["stock"],
                            "sumPrice" => $special_price == 0 ? ($price * $WS["stock"]) : ($special_price * $WS["stock"])
                        );
                    }
                    $Stock = intval($this->_ProductApi->getProductStockStore($p->id, $ProductStore[$i]["id_store"]));
                    $stores[] = array(
                        "id_store" => $ProductStore[$i]["id_store"],
                        "name_store" => $this->_ProductApi->readAllStore($Stores, $ProductStore[$i]["id_store"]),
                        "status" => $ProductStore[$i]["status"] == 0 ? "Disable" : "Enable",
                        "stock" => $Stock,
                        "warehouses" => $warehouses,
                        "price" => $price,
                        "special_price" => $special_price,
                        "sumPrice" => $special_price == 0 ? ($price * $Stock) : ($special_price * $Stock)
                    );
                }
                $Product["store"] = $stores;
            }
            $repsonse[] = $Product;
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

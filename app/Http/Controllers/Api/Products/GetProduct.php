<?php

namespace App\Http\Controllers\Api\Products;

set_time_limit(0);
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Classes\Import\Import;
use App\Classes\Product\ProductApi;
use App\Classes\Helper\Text;
use App\Classes\Helper\Status;
use Exception;
use Illuminate\Support\Facades\Log;
use App\Classes\Account\AccountApi;

class GetProduct extends Controller
{
    /**
     * @var Import
     */
    protected $import;
    /**
     * @var Text
     */
    protected $text;
    /**
     * @var Status
     */
    protected $status;
    /**
     * @var ProductApi
     */
    protected $productApi;
    /**
     * @var AccountApi
     */
    protected $accountApi;

    public function __construct() {
        $this->import     = new Import();
        $this->productApi = new ProductApi();
        $this->text       = new Text();
        $this->status     = new Status();
        $this->accountApi = new AccountApi();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $params = $request->all();
        try {
            $id_Partner = $this->accountApi->getPartnerId($this->accountApi->getAccountToken($request->header($this->text->getAuthorization())));
            $filter = $this->productApi->getFilter($params[0]["filter"], 0);
            $column = $this->productApi->getColumnFilter($filter);
            $products = $this->productApi->getProductsByDate(
                $column,
                $this->productApi->getValueFilter($filter),
                $id_Partner,
                $params[0]["maxItems"],
                $params[0]["minValue"]
            );
            if (count($products) == 0 && $this->productApi->getFilterAll($params[0]["filter"])) {
                $filter = $this->productApi->getFilter($params[0]["filter"], 1);
                $column = $this->productApi->getColumnFilter($filter);
                $products = $this->productApi->getProductsByDate(
                    $column,
                    $this->productApi->getValueFilter($filter),
                    $id_Partner,
                    $params[0]["maxItems"],
                    $params[0]["minValue"]
                );
            }
            $response = $this->text->getResponseApi($products, $this->text->getImportSuccess());
        } catch (Exception $th) {
            $response = $this->text->getResponseApi([], $th->getMessage());
        }
        return response()->json($response);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function searchProduct(Request $request)
    {
        $params = $request->all();
        try {
            $id_Partner = null;
            try {
                $id_Partner = $this->accountApi->getPartnerId($this->accountApi->getAccountToken($request->header($this->text->getAuthorization())));
            } catch (\Throwable $e) {
                $id_Partner = null;
            }
            $response = $this->text->getResponseApi(
                $this->productApi->getSearchProduct($params, $id_Partner),
                $this->text->getQuerySuccess()
            );
        } catch (Exception $th) {
            $response = $this->text->getResponseApi([], $th->getMessage());
        }
        return response()->json($response);
    }
}

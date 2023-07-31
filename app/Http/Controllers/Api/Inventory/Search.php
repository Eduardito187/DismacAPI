<?php

namespace App\Http\Controllers\Api\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Classes\Account\AccountApi;
use App\Classes\Helper\Text;
use App\Classes\Helper\Status;
use Exception;

class Search extends Controller
{
    protected $accountApi;
    protected $text;
    protected $status;

    public function __construct() {
        $this->accountApi = new AccountApi();
        $this->text       = new Text();
        $this->status     = new Status();
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function couponSearch(Request $request)
    {
        $coupons = array();
        try {
            $coupons = $this->accountApi->searchCoupon($request);
            $response = $this->text->getResponseApi($coupons, $this->text->getSuccessSearch());
        } catch (Exception $th) {
            $response = $this->text->getResponseApi($coupons, $th->getMessage());
        }
        return response()->json($response);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function categorySearch(Request $request)
    {
        $categorys = array();
        try {
            $categorys = $this->accountApi->searchCategory($request);
            $response = $this->text->getResponseApi($categorys, $this->text->getSuccessSearch());
        } catch (Exception $th) {
            $response = $this->text->getResponseApi($categorys, $th->getMessage());
        }
        return response()->json($response);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $catalogs = array();
        try {
            $catalogs = $this->accountApi->searchCatalog($request);
            $response = $this->text->getResponseApi($catalogs, $this->text->getSuccessSearch());
        } catch (Exception $th) {
            $response = $this->text->getResponseApi($catalogs, $th->getMessage());
        }
        return response()->json($response);
    }
}

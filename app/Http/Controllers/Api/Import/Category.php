<?php

namespace App\Http\Controllers\Api\Import;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Classes\Import\Import;
use App\Classes\Product\ProductApi;
use App\Classes\Helper\Text;
use App\Classes\Helper\Status;
use Exception;

class Category extends Controller
{
    protected $import;
    protected $text;
    protected $status;
    protected $productApi;
    public function __construct() {
        $this->import     = new Import();
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
        try {
            $ApiBlend = $this->import->importCategory($request->all());
            if ($ApiBlend["code"] == 200) {
                if (is_array($ApiBlend["object"])) {
                    $this->productApi->applyRequestAPI($ApiBlend["object"]);
                }
            }
            $response = $this->text->getResponseApi($this->status->getEnable(), $this->text->getImportSuccess());
        } catch (Exception $th) {
            $response = $this->text->getResponseApi($this->status->getDisable(), $th->getMessage());
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

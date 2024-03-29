<?php

namespace App\Http\Controllers\Api\Import;

set_time_limit(0);
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Classes\Import\Import;
use App\Classes\Product\ProductApi;
use App\Classes\Helper\Text;
use App\Classes\Helper\Status;
use Exception;
use Illuminate\Support\Facades\Log;

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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        set_time_limit(0);
        try {
            //$ApiBlend = $this->import->importCategory($request->all());
            $ApiBlend = $request->all();
            if ($ApiBlend[$this->text->getCode()] == 200) {
                if (is_array($ApiBlend[$this->text->getObject()])) {
                    $this->productApi->applyRequestAPI($ApiBlend[$this->text->getObject()], $request);
                }else{
                    //
                }
            }else{
                //
            }
            $response = $this->text->getResponseApi($this->status->getEnable(), $this->text->getImportSuccess());
        } catch (Exception $th) {
            $response = $this->text->getResponseApi($this->status->getDisable(), $th->getMessage());
        }
        return response()->json($response);
    }
}

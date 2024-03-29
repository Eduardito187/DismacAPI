<?php

namespace App\Http\Controllers\Api\Sales;

use App\Http\Controllers\Controller;
use App\Classes\Helper\Text;
use App\Classes\Helper\Status;
use App\Classes\Partner\PartnerApi;
use Illuminate\Http\Request;
use Exception;

class Order extends Controller
{
    protected $text;
    protected $status;
    protected $partnerApi;
    public function __construct() {
        $this->text       = new Text();
        $this->status     = new Status();
        $this->partnerApi = new PartnerApi;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $response = array();
        try {
            $response = $this->text->getResponseApi($this->partnerApi->searchSale($request->all()), $this->text->getSuccessSearch());
        } catch (Exception $th) {
            $response = $this->text->getResponseApi($this->status->getDisable(), $th->getMessage());
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
        $response = array();
        try {
            $response = $this->text->getResponseApi($this->partnerApi->createOrder($request->all(), $request->ip()), $this->text->getOrderSuccess());
        } catch (Exception $th) {
            $response = $this->text->getResponseApi($this->status->getDisable(), $th->getMessage());
        }
        return response()->json($response);
    }
    
    /**
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function cancelar(Request $request)
    {
        $response = array();
        try {
            $response = $this->text->getResponseApi($this->partnerApi->cancelarOrden($request->all()), $this->text->getOrdenCancelada());
        } catch (Exception $th) {
            $response = $this->text->getResponseApi($this->status->getDisable(), $th->getMessage());
        }
        return response()->json($response);
    }
    
    /**
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function completar(Request $request)
    {
        $response = array();
        try {
            $response = $this->text->getResponseApi($this->partnerApi->completarOrden($request->all()), $this->text->getOrdenCompletada());
        } catch (Exception $th) {
            $response = $this->text->getResponseApi($this->status->getDisable(), $th->getMessage());
        }
        return response()->json($response);
    }
    
    /**
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function cerrar(Request $request)
    {
        $response = array();
        try {
            $response = $this->text->getResponseApi($this->partnerApi->cerrarOrden($request->all()), $this->text->getOrdenCerrada());
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
        $response = array();
        try {
            $response = $this->text->getResponseApi($this->partnerApi->getOrder($id), $this->text->getQuerySuccess());
        } catch (Exception $th) {
            $response = $this->text->getResponseApi($this->status->getDisable(), $th->getMessage());
        }
        return response()->json($response);
    }
}
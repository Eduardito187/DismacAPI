<?php

namespace App\Http\Controllers\Api\Partner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Partner as PartnerModel;
use App\Classes\Helper\Ip;
use App\Classes\Account\AccountApi;
use App\Classes\Address\AddressApi;
use App\Classes\Partner\PartnerApi;
use App\Classes\Helper\Text;
use App\Classes\Helper\Status;
use Throwable;

class Partner extends Controller
{
    protected $addressApi;
    protected $accountApi;
    protected $partnerApi;
    protected $text;
    protected $status;

    public function __construct() {
        $this->addressApi = new AddressApi();
        $this->accountApi = new AccountApi();
        $this->partnerApi = new PartnerApi();
        $this->text       = new Text();
        $this->status     = new Status();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //return response()->json(PartnerModel::all());
        return response()->json(PartnerModel::all());
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
            $api_ip = new Ip(request()->ip());
            $this->addressApi->create($request->all()[$this->text->getPartner()][$this->text->getAddress()], $api_ip->getGeo());
            $this->accountApi->create($request->all()[$this->text->getAccount()]);
            $this->partnerApi->create($request->all()[$this->text->getPartner()], $this->addressApi->getAddressId());
            $this->partnerApi->setAccountDomain($this->partnerApi->getPartnerId(), $this->accountApi->getAccountId());
            $this->partnerApi->setSuperAdminAccount($this->accountApi->getAccountId());
            $response = $this->text->getResponseApi($this->status->getEnable(), $this->text->getAddSuccess());
        } catch (Throwable $th) {
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

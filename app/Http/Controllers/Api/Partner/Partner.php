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
use Exception;
use Illuminate\Support\Facades\Log;
use App\Classes\Pdf\GeneratePdf;

class Partner extends Controller
{
    /**
     * @var AddressApi
     */
    protected $addressApi;
    /**
     * @var AccountApi
     */
    protected $accountApi;
    /**
     * @var PartnerApi
     */
    protected $partnerApi;
    /**
     * @var Text
     */
    protected $text;
    /**
     * @var Status
     */
    protected $status;
    /**
     * @var GeneratePdf
     */
    protected $generatePdf;

    public function __construct() {
        $this->addressApi = new AddressApi();
        $this->accountApi = new AccountApi();
        $this->partnerApi = new PartnerApi();
        $this->text       = new Text();
        $this->status     = new Status();
        $this->generatePdf= new GeneratePdf();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $serverKey = 'AAAAoqaefyg:APA91bFxpj2TAd6IXz8cz6RjQx2qxlsYxTtP9uBwa4-4ij0BDuC8ayh-QJO0RKyKVTbaF_jrrCyDSUWa-2c1ybOm-mgq9L73EJdKOhzHHlHhUXieaj0jEQSbSvyAzIbvhgSR0xSuTtyG';

        // Datos de la notificación
        $notificationData = [
            'to' => 'fofveZuBRLuskqi6YuuPvS:APA91bHN9_iwToKLq6AdvhOcGO0K3sUzhA8X_bEf6qj5UCimtV5FpD91Bs4WCVYxprAnVua9O4-ApZY-jr0pJQfpOCrK1oHWvwEfen62B4VWj4XIf73C3tFjy5l_YCFHUb7FI-kGiHu-', // Reemplaza con el token de registro del dispositivo
            'notification' => [
                'title' => 'Título de la notificación',
                'body' => 'Cuerpo de la notificación'
            ]
        ];

        $ch = curl_init('https://fcm.googleapis.com/fcm/send');

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($notificationData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: key=' . $serverKey,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);

        echo 'Respuesta del servidor FCM: ' . $response;

        $response = array();
        try {
            $Account = $this->accountApi->getAccountByToken($request->header($this->text->getAuthorization()));
            $response = $this->text->getResponseApi($this->accountApi->getPartner($Account->accountPartner->Partner), $this->text->getQuerySuccess());
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
            $api_ip = new Ip(request()->ip());
            $this->addressApi->create($request->all()[$this->text->getPartner()][$this->text->getAddress()], $api_ip->getGeo());
            $this->accountApi->create($request->all()[$this->text->getAccount()]);
            $this->partnerApi->create($request->all()[$this->text->getPartner()], $this->addressApi->getAddressId());
            $this->partnerApi->setAccountDomain($this->partnerApi->getPartnerId(), $this->accountApi->getAccountId());
            $this->partnerApi->setSuperAdminAccount($this->accountApi->getAccountId());
            $response = $this->text->getResponseApi($this->status->getEnable(), $this->text->getAddSuccess());
        } catch (Exception $th) {
            $response = $this->text->getResponseApi($this->status->getDisable(), $th->getMessage());
        }
        return response()->json($response);
    }

    /**
     * Display the specified resource.
     *
     * @param \Illuminate\Http\Request  $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function campaignPartner(Request $request, int $id)
    {
        $response = array();
        try {
            $response = $this->text->getResponseApi($this->partnerApi->campaignPartner($id), $this->text->getQuerySuccess());
        } catch (Exception $th) {
            $response = $this->text->getResponseApi($this->status->getDisable(), $th->getMessage());
        }
        return response()->json($response);
    }
    
    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function listAnalytics(Request $request)
    {
        $response = array();
        try {
            $response = $this->text->getResponseApi($this->partnerApi->getAnalyticsType(), $this->text->getQuerySuccess());
        } catch (Exception $th) {
            $response = $this->text->getResponseApi($this->status->getDisable(), $th->getMessage());
        }
        return response()->json($response);
    }
    
    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function generateAnalyticsReportYear(Request $request)
    {
        $response = array();
        try {
            $params = $request->all();
            $response = $this->text->getResponseApi($this->partnerApi->generateAnalyticsReportYear($params[$this->text->getType()], $params[$this->text->getCode()]), $this->text->getQuerySuccess());
        } catch (Exception $th) {
            $response = $this->text->getResponseApi($this->status->getDisable(), $th->getMessage());
        }
        return response()->json($response);
    }
    
    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function generateAnalyticsReportDays(Request $request)
    {
        $response = array();
        try {
            $params = $request->all();
            $response = $this->text->getResponseApi($this->partnerApi->generateAnalyticsReportDays($params[$this->text->getType()], $params[$this->text->getCode()]), $this->text->getQuerySuccess());
        } catch (Exception $th) {
            $response = $this->text->getResponseApi($this->status->getDisable(), $th->getMessage());
        }
        return response()->json($response);
    }
    
    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function generateAnalyticsReportMonths(Request $request)
    {
        $response = array();
        try {
            $params = $request->all();
            $response = $this->text->getResponseApi($this->partnerApi->generateAnalyticsReportMonths($params[$this->text->getType()], $params[$this->text->getCode()]), $this->text->getQuerySuccess());
        } catch (Exception $th) {
            $response = $this->text->getResponseApi($this->status->getDisable(), $th->getMessage());
        }
        return response()->json($response);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function listAnalyticsEvent(Request $request)
    {
        $response = array();
        try {
            $params = $request->all();
            $response = $this->text->getResponseApi($this->partnerApi->getAnalyticsEventsType($params[$this->text->getType()]), $this->text->getQuerySuccess());
        } catch (Exception $th) {
            $response = $this->text->getResponseApi($this->status->getDisable(), $th->getMessage());
        }
        return response()->json($response);
    }
    
    /**
     * @return \Illuminate\Http\Response
     */
    public function countAccount(Request $request)
    {
        $response = array();
        try {
            $Account = $this->accountApi->getAccountByToken($request->header($this->text->getAuthorization()));
            $response = $this->text->getResponseApi($this->accountApi->getAccountsPartner($Account->accountPartner->Partner), $this->text->getQuerySuccess());
        } catch (Exception $th) {
            $response = $this->text->getResponseApi($this->status->getDisable(), $th->getMessage());
        }
        return response()->json($response);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function generatePdfByCategory(Request $request, int $id)
    {
        $response = array();
        try {
            $Account = $this->accountApi->getAccountByToken($request->header($this->text->getAuthorization()));
            $response = $this->text->getResponseApi(
                $this->generatePdf->generatePdfCategory($Account, $id),
                $this->text->getQuerySuccess()
            );
        } catch (Exception $th) {
            $response = $this->text->getResponseApi($this->status->getDisable(), $th->getMessage());
        }
        return response()->json($response);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function countProduct(Request $request)
    {
        $response = array();
        try {
            $Account = $this->accountApi->getAccountByToken($request->header($this->text->getAuthorization()));
            $response = $this->text->getResponseApi($this->partnerApi->getCountProduct($Account->accountPartner->Partner), $this->text->getQuerySuccess());
        } catch (Exception $th) {
            $response = $this->text->getResponseApi($this->status->getDisable(), $th->getMessage());
        }
        return response()->json($response);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function countWarehouse(Request $request)
    {
        $response = array();
        try {
            $Account = $this->accountApi->getAccountByToken($request->header($this->text->getAuthorization()));
            $response = $this->text->getResponseApi($this->partnerApi->getCountWareHouse($Account->accountPartner->Partner), $this->text->getQuerySuccess());
        } catch (Exception $th) {
            $response = $this->text->getResponseApi($this->status->getDisable(), $th->getMessage());
        }
        return response()->json($response);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getWarehousesList(Request $request)
    {
        $response = array();
        try {
            $Account = $this->accountApi->getAccountByToken($request->header($this->text->getAuthorization()));
            $response = $this->text->getResponseApi($this->partnerApi->getWarehousesList($Account->accountPartner->Partner), $this->text->getQuerySuccess());
        } catch (Exception $th) {
            $response = $this->text->getResponseApi($this->status->getDisable(), $th->getMessage());
        }
        return response()->json($response);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getStoresList(Request $request)
    {
        $response = array();
        try {
            $Account = $this->accountApi->getAccountByToken($request->header($this->text->getAuthorization()));
            $response = $this->text->getResponseApi($this->partnerApi->getStoresList($Account->accountPartner->Partner), $this->text->getQuerySuccess());
        } catch (Exception $th) {
            $response = $this->text->getResponseApi($this->status->getDisable(), $th->getMessage());
        }
        return response()->json($response);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function countStorePartner(Request $request)
    {
        $response = array();
        try {
            $Account = $this->accountApi->getAccountByToken($request->header($this->text->getAuthorization()));
            $response = $this->text->getResponseApi($this->partnerApi->getCountStorePartner($Account->accountPartner->Partner), $this->text->getQuerySuccess());
        } catch (Exception $th) {
            $response = $this->text->getResponseApi($this->status->getDisable(), $th->getMessage());
        }
        return response()->json($response);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function campaignsPartner(Request $request)
    {
        $response = array();
        try {
            $Account = $this->accountApi->getAccountByToken($request->header($this->text->getAuthorization()));
            $response = $this->text->getResponseApi($this->partnerApi->campaignsPartner($Account->accountPartner->Partner), $this->text->getQuerySuccess());
        } catch (Exception $th) {
            $response = $this->text->getResponseApi($this->status->getDisable(), $th->getMessage());
        }
        return response()->json($response);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function socialNetworkPartner(Request $request)
    {
        $response = array();
        try {
            $Account = $this->accountApi->getAccountByToken($request->header($this->text->getAuthorization()));
            $response = $this->text->getResponseApi($this->partnerApi->socialNetworkPartner($Account->accountPartner->Partner), $this->text->getQuerySuccess());
        } catch (Exception $th) {
            $response = $this->text->getResponseApi($this->status->getDisable(), $th->getMessage());
        }
        return response()->json($response);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function countSocialNetworkPartner(Request $request)
    {
        $response = array();
        try {
            $Account = $this->accountApi->getAccountByToken($request->header($this->text->getAuthorization()));
            $response = $this->text->getResponseApi($this->partnerApi->countSocialNetworkPartner($Account->accountPartner->Partner), $this->text->getQuerySuccess());
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
    public function setStorePartner(Request $request)
    {
        $response = array();
        try {
            $this->partnerApi->setStores(
                $this->accountApi->getAccountByToken($request->header($this->text->getAuthorization())), 
                $request->all()[$this->text->getStoresId()]
            );
            $response = $this->text->getResponseApi($this->status->getEnable(), $this->text->getQuerySuccess());
        } catch (Exception $th) {
            $response = $this->text->getResponseApi($this->status->getDisable(), $th->getMessage());
        }
        return response()->json($response);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function lastHistoryCategory(Request $request)
    {
        $response = array();
        try {
            $Account = $this->accountApi->getAccountByToken($request->header($this->text->getAuthorization()));
            $response = $this->text->getResponseApi($this->partnerApi->getLastHistoryCategory($Account->accountPartner->Partner), $this->text->getQuerySuccess());
        } catch (Exception $th) {
            $response = $this->text->getResponseApi($this->status->getDisable(), $th->getMessage());
        }
        return response()->json($response);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function lastHistoryProducts(Request $request)
    {
        $response = array();
        try {
            $Account = $this->accountApi->getAccountByToken($request->header($this->text->getAuthorization()));
            $response = $this->text->getResponseApi($this->partnerApi->getLastHistoryProducts($Account->accountPartner->Partner), $this->text->getQuerySuccess());
        } catch (Exception $th) {
            $response = $this->text->getResponseApi($this->status->getDisable(), $th->getMessage());
        }
        return response()->json($response);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function valuePartner(Request $request)
    {
        $response = array();
        try {
            $Account = $this->accountApi->getAccountByToken($request->header($this->text->getAuthorization()));
            $response = $this->text->getResponseApi($this->partnerApi->valuePartner($Account->accountPartner->Partner), $this->text->getQuerySuccess());
        } catch (Exception $th) {
            $response = $this->text->getResponseApi($this->status->getDisable(), $th->getMessage());
        }
        return response()->json($response);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function countCampaignsPartner(Request $request)
    {
        $response = array();
        try {
            $Account = $this->accountApi->getAccountByToken($request->header($this->text->getAuthorization()));
            $response = $this->text->getResponseApi($this->partnerApi->countCampaignsPartner($Account->accountPartner->Partner), $this->text->getQuerySuccess());
        } catch (Exception $th) {
            $response = $this->text->getResponseApi($this->status->getDisable(), $th->getMessage());
        }
        return response()->json($response);
    }
}

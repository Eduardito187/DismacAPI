<?php

use App\Http\Controllers\Api\Partner\Account\Activate;
use App\Http\Controllers\Api\Partner\Account\Disable;
use App\Http\Controllers\Api\Address\City;
use App\Http\Controllers\Api\Address\Country;
use App\Http\Controllers\Api\Address\Municipality;
use App\Http\Controllers\Api\Import\Category;
use App\Http\Controllers\Api\Store\Stores;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Partner\Partner;
use App\Http\Controllers\Api\Mail\SendCode;
use App\Http\Controllers\Api\Login\Login;
use App\Http\Middleware\CustomValidateToken;
use App\Http\Controllers\Api\Inventory\Catalog;
use App\Http\Controllers\Api\Account\Search as AccountSearch;
use App\Http\Controllers\Api\Account\Register;
use App\Http\Controllers\Api\Inventory\Search as InventorySearch;
use App\Http\Controllers\Api\Products\GetProduct;
use App\Http\Controllers\Api\Report\Products as ReportProducts;
use App\Http\Controllers\Api\Inventory\Category as CategoryInventory;
use App\Http\Controllers\Api\Inventory\AssignProduct;
use App\Http\Controllers\Api\Account\Session\Account as CurrentAccount;
use App\Http\Controllers\Api\Inventory\Prices\ChangePrices;
use App\Http\Controllers\Api\Inventory\Category\AddProducts;
use App\Http\Controllers\Api\Inventory\Category\RemoveProducts;
use App\Http\Controllers\Api\Inventory\Products\Product as PRODUCT_CATALOG;
use App\Http\Controllers\Api\Inventory\Products\Validate as VALIDATE_PRODUCT;
use App\Http\Controllers\Api\Sales\Order;
use App\Http\Controllers\Api\Inventory\Stock\Stock;
use App\Http\Controllers\Api\Upload\Upload;
use App\Http\Controllers\Api\Tools\IpSecurity;
use App\Http\Controllers\Api\Tools\System;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//params in api
// / => index <= get
// / => store <= post
// /show/{id} => show <= get
// /{id} => update <= patch
// /{id} => destroy <= delete

Route::middleware([CustomValidateToken::class])->group(function () {

    Route::controller(System::class)->group(function(){
        Route::post('system/verifyVersion', 'verifyVersion');
        Route::post('system/store', 'store');
        Route::post('system/delimitation', 'delimitation');
        Route::post('system/municipality', 'municipality');
        Route::post('system/warehouse', 'warehouse');
        Route::patch('store/enable/{id}', 'storeEnable');
        Route::patch('store/disable/{id}', 'storeDisable');
        Route::post('rol/modifyPermissions', 'modifyPermissions');
        Route::post('rol/addPermission', 'addPermission');
        Route::post('rol/removePermission', 'removePermission');
    });

    Route::controller(IpSecurity::class)->group(function(){
        Route::post('lockIp', 'lockIp');
        Route::post('unlockIp', 'unlockIp');
    });
    
    Route::controller(Upload::class)->group(function(){
        Route::post('uploadFile', 'actionFile');
        Route::get('process', 'index');
        Route::post('process', 'process');
        Route::post('changeProfile', 'changeProfile');
        Route::post('changeCover', 'changeCover');
        Route::post('uploadZipImages', 'uploadZipImages');
        Route::post('uploadPictures', 'uploadPictures');
        Route::post('deletePicture', 'deletePicture');
    });

    Route::controller(Stock::class)->group(function(){
        Route::post('stock', 'store');
    });

    Route::controller(Order::class)->group(function(){
        Route::post('order/register', 'store');
        Route::post('order/cancelar', 'cancelar');
        Route::post('order/completar', 'completar');
        Route::post('order/cerrar', 'cerrar');
        Route::post('order/search', 'search');
        Route::get('order/show/{id}', 'show');
    });

    Route::controller(Partner::class)->group(function(){
        Route::get('sociaList', 'sociaList');
        Route::get('partner', 'index');
        Route::get('partner/delimitations', 'delimitations');
        Route::post('partner', 'store');
        Route::post('partner/create/campaign', 'createCampaign');
        Route::post('partner/create/socials', 'createSocials');
        Route::patch('partner/Pdf/Category/{id}', 'generatePdfByCategory');
        Route::get('partner/countAccount', 'countAccount');
        Route::get('partner/countProduct', 'countProduct');
        Route::get('partner/countWarehouse', 'countWarehouse');
        Route::get('partner/countStorePartner', 'countStorePartner');
        Route::post('partner/setStorePartner', 'setStorePartner');
        Route::get('partner/countSocialNetworkPartner', 'countSocialNetworkPartner');
        Route::get('partner/socialNetworkPartner', 'socialNetworkPartner');
        Route::get('partner/lastHistoryCategory', 'lastHistoryCategory');
        Route::get('partner/lastHistoryProducts', 'lastHistoryProducts');
        Route::get('partner/valuePartner', 'valuePartner');
        Route::get('partner/valuePartnerStores', 'valuePartnerStores');
        Route::get('partner/countCampaignsPartner', 'countCampaignsPartner');
        Route::get('partner/campaignsPartner', 'campaignsPartner');
        Route::get('partner/campaignPartner/{id}', 'campaignPartner');
        Route::get('partner/listAnalytics', 'listAnalytics');
        Route::post('partner/listAnalyticsEvent', 'listAnalyticsEvent');
        Route::post('partner/generateAnalyticsReportDays', 'generateAnalyticsReportDays');
        Route::post('partner/generateAnalyticsReportYear', 'generateAnalyticsReportYear');
        Route::post('partner/generateAnalyticsReportMonths', 'generateAnalyticsReportMonths');
        Route::get('partner/getWarehousesList', 'getWarehousesList');
        Route::get('partner/getStoresList', 'getStoresList');
    });

    Route::controller(SendCode::class)->group(function(){
        Route::post('sendcode', 'store');
    });

    Route::controller(Login::class)->group(function(){
        Route::post('login', 'store');
    });

    Route::controller(Country::class)->group(function(){
    });

    Route::controller(City::class)->group(function(){
    });

    Route::controller(Municipality::class)->group(function(){
        Route::post('municipality', 'store');
    });

    Route::controller(Stores::class)->group(function(){
        Route::get('store', 'index');
        Route::get('getStores', 'getStores');
        Route::get('store/show/{id}', 'show');
    });

    Route::controller(Activate::class)->group(function(){
        Route::post('partner/account/enable', 'store');
    });

    Route::controller(Disable::class)->group(function(){
        Route::post('partner/account/disable', 'store');
    });

    Route::controller(Category::class)->group(function(){
        Route::get('import/category', 'index');
        Route::post('import/category', 'store');
        Route::get('import/category/show/{id}', 'show');
        Route::patch('import/category/{id}', 'update');
        Route::delete('import/category/{id}', 'destroy');
    });

    Route::controller(Catalog::class)->group(function(){
        Route::post('partner/inventory/catalog', 'store');
        Route::get('partner/inventory/catalog/show/{id}', 'show');
        Route::get('partner/inventory/catalog/info/{id}', 'info');
        Route::patch('partner/inventory/catalog/{id}', 'update');
    });

    Route::controller(AddProducts::class)->group(function(){
        Route::post('partner/inventory/AddProducts', 'store');
    });

    Route::controller(RemoveProducts::class)->group(function(){
        Route::post('partner/inventory/RemoveProducts', 'store');
    });
    
    Route::controller(PRODUCT_CATALOG::class)->group(function(){
        Route::post('product/seturl', 'seturl');
        Route::post('product/clacom', 'clacom');
        Route::get('product/status/{id}', 'getStatus');
        Route::get('product/prices/{id}', 'getPrices');
        Route::get('product/pos/{id}', 'getPosData');
        Route::get('product/attributes/{id}', 'getAttributes');
        Route::get('product/show/{id}', 'show');
        Route::patch('product/status/{id}', 'updateStatus');
        Route::patch('product/prices/{id}', 'updatePrices');
        Route::patch('product/attributes/{id}', 'updateAttributes');
    });
    
    Route::controller(ChangePrices::class)->group(function(){
        Route::post('changePrices', 'store');
    });
    
    Route::controller(CurrentAccount::class)->group(function(){
        Route::get('currentAccount', 'index');
        Route::get('rolsAccount', 'rolsAccount');
        Route::post('currentAccount/new/improvements', 'improvements');
        Route::post('currentAccount/new/support', 'support');
        Route::get('currentAccount/getImprovementsActive', 'getImprovementsActive');
        Route::get('currentAccount/getImprovementsInactive', 'getImprovementsInactive');
        Route::get('currentAccount/getTicketsAccount', 'getTicketsAccount');
        Route::get('currentAccount/getTicketsPartner', 'getTicketsPartner');
        Route::post('currentAccount/registerToken', 'registerToken');
        Route::get('allrol', 'allrol');
    });

    Route::controller(AccountSearch::class)->group(function(){
        Route::post('search/account', 'store');
    });

    Route::controller(InventorySearch::class)->group(function(){
        Route::post('search/inventory', 'store');
        Route::post('search/category', 'categorySearch');
        Route::post('search/coupon', 'couponSearch');
    });

    Route::controller(Register::class)->group(function(){
        Route::post('register/account', 'store');
        Route::get('account/show/{id}', 'show');
        Route::patch('account/edit/{id}', 'update');
        Route::patch('account/updatePassword/{id}', 'updatePassword');
        Route::patch('account/changeStatus/{id}', 'changeStatus');
    });

    Route::controller(GetProduct::class)->group(function(){
        Route::post('GetProduct', 'store');
        Route::post('searchProduct', 'searchProduct');
    });

    Route::controller(ReportProducts::class)->group(function(){
        Route::get('report/product', 'index');
    });

    Route::controller(Category::class)->group(function(){
        Route::post('partner/inventory/category', 'store');
    });

    Route::controller(CategoryInventory::class)->group(function(){
        Route::post('catalog/inventory/category', 'store');
        Route::get('catalog/inventory/category/show/{id_category}/{id_catalog}', 'show');
        Route::patch('catalog/inventory/category/{id}', 'update');
    });

    Route::controller(AssignProduct::class)->group(function(){
    });

    Route::controller(VALIDATE_PRODUCT::class)->group(function(){
        Route::post('partner/inventory/Validate', 'store');
    });
});
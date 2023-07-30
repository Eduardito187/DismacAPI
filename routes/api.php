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
        Route::get('stock', 'index');
        Route::post('stock', 'store');
        Route::get('stock/show/{id}', 'show');
        Route::patch('stock/{id}', 'update');
        Route::delete('stock/{id}', 'destroy');
    });

    Route::controller(Order::class)->group(function(){
        Route::get('order', 'index');
        Route::post('order/register', 'store');
        Route::post('order/cancelar', 'cancelar');
        Route::post('order/completar', 'completar');
        Route::post('order/cerrar', 'cerrar');
        Route::post('order/search', 'search');
        Route::get('order/show/{id}', 'show');
        Route::patch('order/{id}', 'update');
        Route::delete('order/{id}', 'destroy');
    });

    Route::controller(Partner::class)->group(function(){
        Route::get('partner', 'index');
        Route::post('partner', 'store');
        Route::get('partner/show/{id}', 'show');
        Route::patch('partner/{id}', 'update');
        Route::delete('partner/{id}', 'destroy');
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
        Route::get('partner/countCampaignsPartner', 'countCampaignsPartner');
        Route::get('partner/campaignsPartner', 'campaignsPartner');
        Route::get('partner/campaignPartner/{id}', 'campaignPartner');
    });

    Route::controller(SendCode::class)->group(function(){
        Route::get('sendcode', 'index');
        Route::post('sendcode', 'store');
        Route::get('sendcode/show/{id}', 'show');
        Route::patch('sendcode/{id}', 'update');
        Route::delete('sendcode/{id}', 'destroy');
    });

    Route::controller(Login::class)->group(function(){
        Route::get('login', 'index');
        Route::post('login', 'store');
        Route::get('login/show/{id}', 'show');
        Route::patch('login/{id}', 'update');
        Route::delete('login/{id}', 'destroy');
    });

    Route::controller(Country::class)->group(function(){
        Route::get('country', 'index');
        Route::post('country', 'store');
        Route::get('country/show/{id}', 'show');
        Route::patch('country/{id}', 'update');
        Route::delete('country/{id}', 'destroy');
    });

    Route::controller(City::class)->group(function(){
        Route::get('city', 'index');
        Route::post('city', 'store');
        Route::get('city/show/{id}', 'show');
        Route::patch('city/{id}', 'update');
        Route::delete('city/{id}', 'destroy');
    });

    Route::controller(Municipality::class)->group(function(){
        Route::get('municipality', 'index');
        Route::post('municipality', 'store');
        Route::get('municipality/show/{id}', 'show');
        Route::patch('municipality/{id}', 'update');
        Route::delete('municipality/{id}', 'destroy');
    });

    Route::controller(Stores::class)->group(function(){
        Route::get('store', 'index');
        Route::get('getStores', 'getStores');
        Route::post('store', 'store');
        Route::get('store/show/{id}', 'show');
        Route::patch('store/{id}', 'update');
        Route::delete('store/{id}', 'destroy');
    });

    Route::controller(Activate::class)->group(function(){
        Route::get('partner/account/enable', 'index');
        Route::post('partner/account/enable', 'store');
        Route::get('partner/account/enable/show/{id}', 'show');
        Route::patch('partner/account/enable/{id}', 'update');
        Route::delete('partner/account/enable/{id}', 'destroy');
    });

    Route::controller(Disable::class)->group(function(){
        Route::get('partner/account/disable', 'index');
        Route::post('partner/account/disable', 'store');
        Route::get('partner/account/disable/show/{id}', 'show');
        Route::patch('partner/account/disable/{id}', 'update');
        Route::delete('partner/account/disable/{id}', 'destroy');
    });

    Route::controller(Category::class)->group(function(){
        Route::get('import/category', 'index');
        Route::post('import/category', 'store');
        Route::get('import/category/show/{id}', 'show');
        Route::patch('import/category/{id}', 'update');
        Route::delete('import/category/{id}', 'destroy');
    });

    Route::controller(Catalog::class)->group(function(){
        Route::get('partner/inventory/catalog', 'index');
        Route::post('partner/inventory/catalog', 'store');
        Route::get('partner/inventory/catalog/show/{id}', 'show');
        Route::get('partner/inventory/catalog/info/{id}', 'info');
        Route::patch('partner/inventory/catalog/{id}', 'update');
        Route::delete('partner/inventory/catalog/{id}', 'destroy');
    });

    Route::controller(AddProducts::class)->group(function(){
        Route::get('partner/inventory/AddProducts', 'index');
        Route::post('partner/inventory/AddProducts', 'store');
        Route::get('partner/inventory/AddProducts/show/{id}', 'show');
        Route::patch('partner/inventory/AddProducts/{id}', 'update');
        Route::delete('partner/inventory/AddProducts/{id}', 'destroy');
    });

    Route::controller(RemoveProducts::class)->group(function(){
        Route::get('partner/inventory/RemoveProducts', 'index');
        Route::post('partner/inventory/RemoveProducts', 'store');
        Route::get('partner/inventory/RemoveProducts/show/{id}', 'show');
        Route::patch('partner/inventory/RemoveProducts/{id}', 'update');
        Route::delete('partner/inventory/RemoveProducts/{id}', 'destroy');
    });
    
    Route::controller(PRODUCT_CATALOG::class)->group(function(){
        Route::get('product', 'index');
        Route::post('product', 'store');
        Route::post('product/seturl', 'seturl');
        Route::post('product/clacom', 'clacom');
        Route::get('product/status/{id}', 'getStatus');
        Route::get('product/prices/{id}', 'getPrices');
        Route::get('product/pos/{id}', 'getPosData');
        Route::get('product/attributes/{id}', 'getAttributes');
        Route::get('product/show/{id}', 'show');
        Route::patch('product/status/{id}', 'updateStatus');
        Route::patch('product/prices/{id}', 'updatePrices');
        Route::delete('product/{id}', 'destroy');
    });
    
    Route::controller(ChangePrices::class)->group(function(){
        Route::get('changePrices', 'index');
        Route::post('changePrices', 'store');
        Route::get('changePrices/show/{id}', 'show');
        Route::patch('changePrices/{id}', 'update');
        Route::delete('changePrices/{id}', 'destroy');
    });
    
    Route::controller(CurrentAccount::class)->group(function(){
        Route::get('currentAccount', 'index');
        Route::get('rolsAccount', 'rolsAccount');
        Route::post('currentAccount', 'store');
        Route::get('currentAccount/show/{id}', 'show');
        Route::patch('currentAccount/{id}', 'update');
        Route::delete('currentAccount/{id}', 'destroy');
        Route::post('currentAccount/new/improvements', 'improvements');
        Route::post('currentAccount/new/support', 'support');
        Route::get('currentAccount/getImprovementsActive', 'getImprovementsActive');
        Route::get('currentAccount/getImprovementsInactive', 'getImprovementsInactive');
        Route::get('currentAccount/getTicketsAccount', 'getTicketsAccount');
        Route::get('currentAccount/getTicketsPartner', 'getTicketsPartner');
        Route::get('allrol', 'allrol');
    });

    Route::controller(AccountSearch::class)->group(function(){
        Route::get('search/account', 'index');
        Route::post('search/account', 'store');
        Route::get('search/account/show/{id}', 'show');
        Route::patch('search/account/{id}', 'update');
        Route::delete('search/account/{id}', 'destroy');
    });

    Route::controller(InventorySearch::class)->group(function(){
        Route::get('search/inventory', 'index');
        Route::post('search/inventory', 'store');
        Route::get('search/inventory/show/{id}', 'show');
        Route::patch('search/inventory/{id}', 'update');
        Route::delete('search/inventory/{id}', 'destroy');
    });

    Route::controller(Register::class)->group(function(){
        Route::get('register/account', 'index');
        Route::post('register/account', 'store');
        Route::get('account/show/{id}', 'show');
        Route::patch('account/edit/{id}', 'update');
        Route::patch('account/updatePassword/{id}', 'updatePassword');
        Route::patch('account/changeStatus/{id}', 'changeStatus');
        Route::delete('register/account/{id}', 'destroy');
    });

    Route::controller(GetProduct::class)->group(function(){
        Route::get('GetProduct', 'index');
        Route::post('GetProduct', 'store');
        Route::get('GetProduct/show/{id}', 'show');
        Route::patch('GetProduct/{id}', 'update');
        Route::delete('GetProduct/{id}', 'destroy');
        Route::post('searchProduct', 'searchProduct');
    });

    Route::controller(ReportProducts::class)->group(function(){
        Route::get('report/product', 'index');
        Route::post('report/product', 'store');
        Route::get('report/product/show/{id}', 'show');
        Route::patch('report/product/{id}', 'update');
        Route::delete('report/product/{id}', 'destroy');
    });

    Route::controller(Category::class)->group(function(){
        Route::get('partner/inventory/category', 'index');
        Route::post('partner/inventory/category', 'store');
        Route::get('partner/inventory/category/show/{id}', 'show');
        Route::patch('partner/inventory/category/{id}', 'update');
        Route::delete('partner/inventory/category/{id}', 'destroy');
    });

    Route::controller(CategoryInventory::class)->group(function(){
        Route::get('catalog/inventory/category', 'index');
        Route::post('catalog/inventory/category', 'store');
        Route::get('catalog/inventory/category/show/{id_category}/{id_catalog}', 'show');
        Route::patch('catalog/inventory/category/{id}', 'update');
        Route::delete('catalog/inventory/category/{id}', 'destroy');
    });

    Route::controller(AssignProduct::class)->group(function(){
        Route::get('partner/inventory/AssignProduct', 'index');
        Route::post('partner/inventory/AssignProduct', 'store');
        Route::get('partner/inventory/AssignProduct/show/{id}', 'show');
        Route::patch('partner/inventory/AssignProduct/{id}', 'update');
        Route::delete('partner/inventory/AssignProduct/{id}', 'destroy');
    });

    Route::controller(VALIDATE_PRODUCT::class)->group(function(){
        Route::get('partner/inventory/Validate', 'index');
        Route::post('partner/inventory/Validate', 'store');
        Route::get('partner/inventory/Validate/show/{id}', 'show');
        Route::patch('partner/inventory/Validate/{id}', 'update');
        Route::delete('partner/inventory/Validate/{id}', 'destroy');
    });
});
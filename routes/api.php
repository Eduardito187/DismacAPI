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
    Route::controller(Partner::class)->group(function(){
        Route::get('partner', 'index');
        Route::post('partner', 'store');
        Route::get('partner/show/{id}', 'show');
        Route::patch('partner/{id}', 'update');
        Route::delete('partner/{id}', 'destroy');
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
        Route::post('store', 'store');
        Route::get('store/show/{id}', 'show');
        Route::patch('store/{id}', 'update');
        Route::delete('store/{id}', 'destroy');
    });
    Route::controller(Activate::class)->group(function(){
        Route::get('partner/account/activate', 'index');
        Route::post('partner/account/activate', 'store');
        Route::get('partner/account/activate/show/{id}', 'show');
        Route::patch('partner/account/activate/{id}', 'update');
        Route::delete('partner/account/activate/{id}', 'destroy');
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
        Route::patch('partner/inventory/catalog/{id}', 'update');
        Route::delete('partner/inventory/catalog/{id}', 'destroy');
    });
});
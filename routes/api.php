<?php

use App\Http\Controllers\Api\Address\City;
use App\Http\Controllers\Api\Address\Country;
use App\Http\Controllers\Api\Address\Municipality;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Partner\Partner;
use App\Http\Controllers\Api\Mail\SendCode;
use App\Http\Controllers\Api\Login\Login;
use App\Http\Middleware\CustomValidateToken;

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
        Route::post('sendcode', 'store');
    });
    Route::controller(Login::class)->group(function(){
        Route::post('login', 'store');
    });
    Route::controller(Country::class)->group(function(){
        Route::get('country', 'index');
    });
    Route::controller(City::class)->group(function(){
        Route::get('city', 'index');
    });
    Route::controller(Municipality::class)->group(function(){
        Route::post('municipality', 'store');
    });
});
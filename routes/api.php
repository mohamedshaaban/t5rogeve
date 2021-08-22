<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('customer-register', 'Auth\RegisterController@customerRegister');
Route::post('customer-verifiy', 'Auth\RegisterController@customerVerifiy');
Route::post('customer-login', 'Auth\RegisterController@customerLogin');
Route::get('sliders', 'Sliders\SlidersController@index');

Route::post('forgetPasswordOtpVerify','Auth\AuthController@forgetPasswordOtpVerify');
Route::post('forgetPassword','Auth\AuthController@userForgetPassword');
Route::post('userResetPassword','Auth\AuthController@userResetPassword');


Route::post('userUpdatePassword','Auth\AuthController@userUpdatePassword');
Route::post('userUpdateProfile','Auth\AuthController@userUpdateProfile');


Route::group(['middleware' => 'auth:customers_api'], function() {

    Route::get('customer-profile', 'Auth\RegisterController@customerProfile');
    Route::post('bookingList','HomeController@bookingList');
    Route::post('get-transaction-by-userid','HomeController@GetTransactionByUserId');
    Route::post('sponsorplatinumList','HomeController@sponsorplatinumList');
    Route::post('NotificationsList','HomeController@NotificationsList');
    Route::post('userUpdatePhone','Auth\AuthController@userUpdatePhone');
    Route::post('usercheckPhone','Auth\AuthController@checkPhone');


});
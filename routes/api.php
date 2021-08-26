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

Route::post('userRegister', 'Auth\RegisterController@customerRegister');
Route::post('verifyOtp', 'Auth\RegisterController@customerVerifiy');
Route::post('userLogin', 'Auth\RegisterController@customerLogin');
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
    Route::post('updateDeviceToken','Auth\AuthController@updateDeviceToken');
    Route::post('ceremonyList','CeremonyController@ceremonyList');

    Route::post('userLogout','Auth\AuthController@userLogout');
//    Route::post('userCheck','Auth\AuthController@userDataCheck');


    Route::post('/ceremonyListcheck','CeremonyController@ceremonyListcheck');
    Route::post('/ceremonydetail','CeremonyController@ceremonydetail');
    Route::post('/eventList','CeremonyController@eventList');


    /*Route::post('/bookCeremonySeats','CeremonyController@bookCeremonySeats');
    Route::post('/updateRequestStatus','CeremonyController@updateRequestStatus');

    Route::post('/check-seat-availability','CeremonyController@checkSeatAvailability');
    Route::post('/increase-seat','CeremonyController@increaseSeat');
*/

    Route::post('/eventBooking','EventsController@booking');
    Route::post('/updateBooking','EventsController@updatebooking');
    Route::post('/freeeventBooking','EventsController@bookingfreeevent');
    Route::post('/eventBookingEdit','EventsController@bookingEdit');
    Route::post('/eventBookingDelete/{id}','EventsController@bookingDelete');




    Route::post('/eventBookingCsvfile','CeremonyBookingController@booking_csvfile');
    Route::post('/eventBookingAllcsvfile','CeremonyBookingController@booking_allcsvfile');
    Route::post('/bookingList','CeremonyBookingController@bookingList');
    Route::post('/onebookingList','CeremonyBookingController@onebookingList');
    Route::post('/changerobesize','CeremonyBookingController@changerobesize');
    Route::post('/addBookingPayment','CeremonyBookingController@addBookingPayment');



});
<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\PaytmPayment;
use App\Http\Controllers;
use App\Http\Controllers\Api\V1;
use App\Http\Controllers\Api\V1\PaymentController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

//Paytm Payment
Route::group(['prefix' => 'payment-paytm'], function () {
    Route::get('/', 'PaymentController@initiate_payment')->name('payment-paytm');
    Route::get('set-payment-method/{name}', 'PaymentController@set_payment_method')->name('set-payment-method');
});

Route::post('paytm-payment',[PaytmController::Class, 'paytmPayment'])->name('paytm.payment');
Route::post('paytm-callback',[PaytmController::Class, 'paytmCallback'])->name('paytm.callback');
Route::get('paytm-status', 'PaytmController@getPaymentStatus')->name('paytm-status');
Route::get('payment-success', 'PaymentController@success')->name('payment-success');
Route::get('payment-fail', 'PaymentController@fail')->name('payment-fail');

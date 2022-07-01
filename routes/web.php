<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\PaytmPayment;
use App\Http\Controllers;
use App\Http\Controllers\Api\V1;
use App\Http\Controllers\Api\V1\PaymentController;
use App\Http\Controllers\Api\V1\PaytmController;

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
    Route::get('/', [PaymentController::Class, 'initiate_payment'])->name('payment-paytm');
    Route::get('set-payment-method/{name}', [PaymentController::Class, 'set_payment_method'])->name('set-payment-method');
});

Route::post('paytm-payment',[PaytmController::Class, 'paytmPayment'])->name('paytm.payment');
Route::post('paytm-callback',[PaytmController::Class, 'paytmCallback'])->name('paytm.callback');
Route::get('paytm-status', [PaytmController::Class, 'getPaymentStatus'])->name('paytm-status');
Route::get('payment-success', [PaymentController::Class, 'success'])->name('payment-success');
Route::get('payment-fail', [PaymentController::Class, 'fail'])->name('payment-fail');

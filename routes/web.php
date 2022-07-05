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


Route::get('payment-paytm',[PaytmController::Class, 'initiatePayment'])->name('paytm.payment');
Route::post('paytm-callback',[PaytmController::Class, 'paytmCallback'])->name('paytm.callback');
Route::post('paytm-purchase',[PaytmController::Class, 'paytmPurchase'])->name('paytm.purchase');

Route::post('/payment/status', [PaytmController::class,'paymentCallback'])->name('paytm.callback');

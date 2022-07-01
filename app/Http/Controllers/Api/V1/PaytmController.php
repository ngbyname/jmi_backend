<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use PaytmWallet;
use Illuminate\Http\Request;

class PaytmController extends Controller
{
    /**
     * Redirect the user to the Payment Gateway.
     *
     * @return Response
     */
    public function initiatePayment(Request $request)
    {
        // dd($request->order['id'],$request->user);
        //{"order":{"id":23,"amount":"2"},"user":{"id":3,"phonenumber":"1111111111","email":"xxxxxx@yahoo.com"}}
        $payment = PaytmWallet::with('receive');
        $payment->prepare([
          'order' => $request->order['id'],
          'user' => $request->user['id'],
          'mobile_number' => $request->user['phonenumber'],
          'email' => $request->user['email'],
          'amount' => $request->order['amount'],
          'callback_url' => route('paytm.callback')
        ]);
        return $payment->receive();
    }

    /**
     * Obtain the payment information.
     *
     * @return Object
     */
    public function paymentCallback()
    {
        $transaction = PaytmWallet::with('receive');
        
        $response = $transaction->response(); // To get raw response as array
        //Check out response parameters sent by paytm here -> http://paywithpaytm.com/developer/paytm_api_doc?target=interpreting-response-sent-by-paytm
        
        if($transaction->isSuccessful()){
          //Transaction Successful
          return view('paytm-sucsess-page');
        }else if($transaction->isFailed()){
          //Transaction Failed
          return view('paytm.paytm-fail');
        }else if($transaction->isOpen()){
          //Transaction Open/Processing
          return view('paytm.paytm-fail');
        }
        $transaction->getResponseMessage(); //Get Response Message If Available
        //get important parameters via public methods
        $transaction->getOrderId(); // Get order id
        $transaction->getTransactionId(); // Get transaction id
    }    

    /**
    * Obtain the transaction status/information.
    *
    * @return Object
    */
    public function statusCheck(){
        $status = PaytmWallet::with('status');
        $status->prepare(['order' => $order->id]);
        $status->check();
        
        $response = $status->response(); // To get raw response as array
        //Check out response parameters sent by paytm here -> http://paywithpaytm.com/developer/paytm_api_doc?target=txn-status-api-description
        
        if($status->isSuccessful()){
          //Transaction Successful
        }else if($status->isFailed()){
          //Transaction Failed
        }else if($status->isOpen()){
          //Transaction Open/Processing
        }
        $status->getResponseMessage(); //Get Response Message If Available
        //get important parameters via public methods
        $status->getOrderId(); // Get order id
        $status->getTransactionId(); // Get transaction id
    }

    /**
    * Initiate refund.
    *
    * @return Object
    */
    public function refund(){
        $refund = PaytmWallet::with('refund');
        $refund->prepare([
            'order' => $order->id,
            'reference' => "refund-order-4", // provide refund reference for your future reference (should be unique for each order)
            'amount' => 300, // refund amount 
            'transaction' => $order->transaction_id // provide paytm transaction id referring to this order 
        ]);
        $refund->initiate();
        $response = $refund->response(); // To get raw response as array
        
        if($refund->isSuccessful()){
          //Refund Successful
        }else if($refund->isFailed()){
          //Refund Failed
        }else if($refund->isOpen()){
          //Refund Open/Processing
        }else if($refund->isPending()){
          //Refund Pending
        }
    }

     /**
    * Initiate refund.
    *
    * @return Object
    */
    public function refundStatus(){
        $refundStatus = PaytmWallet::with('refund_status');
        $refundStatus->prepare([
            'order' => $order->id,
            'reference' => "refund-order-4", // provide reference number (the same which you have entered for initiating refund)
        ]);
        $refundStatus->check();
        
        $response = $refundStatus->response(); // To get raw response as array
        
        if($refundStatus->isSuccessful()){
          //Refund Successful
        }else if($refundStatus->isFailed()){
          //Refund Failed
        }else if($refundStatus->isOpen()){
          //Refund Open/Processing
        }else if($refundStatus->isPending()){
          //Refund Pending
        }
    }

    /**
     * Paytm Payment Page
     * 
     * @return Object
     */

     public function paytmPurchase()
     {
        return view('paytm.payment-page');
     }
}
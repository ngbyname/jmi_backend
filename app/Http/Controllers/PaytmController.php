<?php

namespace App\Http\Controllers;

use Brian2694\Toastr\Facades\Toastr;
use App\CentralLogics\Helpers;
use App\CentralLogics\OrderLogic;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use PaytmWallet;

class PaytmController extends Controller
{
  public function __construct()
  {
      $paytm_conf = Config::get('paytm');
      
      $this->_api_context =  new \Paytm\Rest\ApiContext(new OAuthTokenCredential(
              $paytm_conf['merchant_id'],
              $paytm_conf['merchant_key'])
      );
      $this->_api_context->setConfig($paytm_conf['settings']);
  }

    /**
     * Redirect the user to the Payment Gateway.
     *
     * @return Response
     */
    public function paytmPayment(Request $request)
    {
      $order = Order::with(['details'])->where(['id' => session('order_id')])->first();
 
        $tr_ref = Str::random(6) . '-' . rand(1, 1000);

        $payer = new Payer();
        $payer->setPaymentMethod('paytm');
        $items_array = [];
        $item = new Item();
        $number = sprintf("%0.2f", $order['order_amount']);
        $item->setName(session('f_name'))
            ->setCurrency(Helpers::currency_code())
            ->setQuantity(1)
            ->setPrice($number);
         
        array_push($items_array, $item);

        $item_list = new ItemList();
        $item_list->setItems($items_array);

        $amount = new Amount();
     
        $amount->setCurrency(Helpers::currency_code())
            ->setTotal($number);
        \session()->put('transaction_reference', $tr_ref);
        $transaction = new Transaction();
        $transaction->setAmount($amount)
            ->setItemList($item_list)
            ->setDescription($tr_ref);

        $redirect_urls = new RedirectUrls();
        $redirect_urls->setReturnUrl(URL::route('paytm-status'))
          ->setCancelUrl(URL::route('payment-fail'));
          $payment = new Payment();
        $payment->setIntent('Sale')
            ->setPayer($payer)
            ->setRedirectUrls($redirect_urls)
            ->setTransactions(array($transaction));
        
        try {
            
            $payment->create($this->_api_context);
           
             /**
         * Get redirect url
         * The API response provides the url that you must redirect
         * the buyer to. Retrieve the url from the $payment->getLinks() method
         *
         */
    
        foreach ($payment->getLinks() as $key => $link) {
            
            if ($link->getRel() == 'approval_url') {
                
                $redirectUrl = $link->getHref();
                
                break;
            }
    
        }
     
            DB::table('orders')
                ->where('id', $order->id)
                ->update([
                    'transaction_reference' => $payment->getId(),
                    'payment_method' => 'paytm',
                    'order_status' => 'success',
                    'failed' => now(),
                    'updated_at' => now()
                ]);
       
            Session::put('paytm_payment_id', $payment->getId());
             
            if (isset($redirectUrl)) {
             
                return Redirect::away($redirectUrl);
            }else{
                dd("bye");
            }

        } catch (\Exception $ex) {
           dd($ex->getData());
               //   Toastr::error(trans($ex->getData(),['method'=>trans('messages.paytm')]));

            Toastr::error(trans('messages.your_currency_is_not_supported',['method'=>trans('messages.paytm')]));
            return back();
        }

        Session::put('error', trans('messages.config_your_account',['method'=>trans('messages.paytm')]));
        return back();

        // $payment = PaytmWallet::with('receive');
        // $payment->prepare([
        //   'order' => rand(),
        //   'user' => rand(10,1000),
        //   'mobile_number' => '123456789',
        //   'email' => 'paytmtest@gmail.com',
        //   'amount' => $request->amount,
        //   'callback_url' => route('paytm.callback'),
        // ]);
        // return $payment->receive();
    }


    /**
     * Obtain the payment information.
     *
     * @return Object
     */
    public function paytmCallback()
    {
        $transaction = PaytmWallet::with('receive');
        
        $response = $transaction->response(); // To get raw response as array
        //Check out response parameters sent by paytm here -> http://paywithpaytm.com/developer/paytm_api_doc?target=interpreting-response-sent-by-paytm
        
        if($transaction->isSuccessful()){
          //Transaction Successful
          return view('paytm.paytm-success-page');
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
     * Paytm Payment Page
     *
     * @return Object
     */
    public function paytmPurchase()
    {
        return view('paytm.payment-page');
    } 

    public function getPaymentStatus(Request $request)
    {
        $payment_id = Session::get('paytm_payment_id');
        if (empty($request['PayerID']) || empty($request['token'])) {
            Session::put('error', trans('messages.payment_failed'));
            return Redirect::back();
        }

        $payment = Payment::get($payment_id, $this->_api_context);
        $execution = new PaymentExecution();
        $execution->setPayerId($request['PayerID']);

        /**Execute the payment **/
        $result = $payment->execute($execution, $this->_api_context);
        $order = Order::where('transaction_reference', $payment_id)->first();
        if ($result->getState() == 'approved') {

            $order->transaction_reference = $payment_id;
            $order->payment_method = 'paytm';
            $order->payment_status = 'paid';
            $order->order_status = 'confirmed';
            $order->confirmed = now();
            $order->save();
            /*try {
                Helpers::send_order_notification($order);
            } catch (\Exception $e) {
            } */


            return redirect('&status=success');
            /*if ($order->callback != null) {
                return redirect($order->callback . '&status=success');
            }else{
                return \redirect()->route('payment-success');
            }*/
        }

        $order->order_status = 'failed';
        $order->failed = now();
        $order->save();
        return redirect('&status=fail');
        /*if ($order->callback != null) {
            return redirect($order->callback . '&status=fail');
        }else{
            return \redirect()->route('payment-fail');
        }*/
    }

}
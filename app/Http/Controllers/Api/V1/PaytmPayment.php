<?php
/*
* import checksum generation utility
* You can get this utility from https://developer.paytm.com/docs/checksum/
*/


namespace App\Http\Controllers\Api\V1;

class PaytmPayment{


public function callPaytmApi()
{

    require_once("app/Http/Controllers/Api/V1/PaytmChecksum.php");


    $paytmParams = array();

    $paytmParams["body"] = array(
    "requestType"  => "Payment",
    "mid" => "pFtReg33593395168554",
    "websiteName"  => "WEBSTAGING",
    "orderId" => "ORDERID_93761",
    "callbackUrl" => "https://<callback URL to be used by merchant>",
    "txnAmount"  => array(
    "value"  => "1.00",
    "currency"=> "INR",
    ),
    "userInfo" => array(
    "custId" => "CUST_001",
    ),
    );

    /*
    * Generate checksum by parameters we have in body
    * Find your Merchant Key in your Paytm Dashboard at https://dashboard.paytm.com/next/apikeys 
    */
    $checksum = PaytmChecksum::generateSignature(json_encode($paytmParams["body"], JSON_UNESCAPED_SLASHES), "iFW7_@9SZ%OEy%AG");

    $paytmParams["head"] = array(
    "signature"=> $checksum
    );

    $post_data = json_encode($paytmParams, JSON_UNESCAPED_SLASHES);

    /* for Staging */
    $url = "https://securegw-stage.paytm.in/theia/api/v1/initiateTransaction?mid=pFtReg33593395168554&orderId=ORDERID_93761";

    /* for Production */
    // $url = "https://securegw.paytm.in/theia/api/v1/initiateTransaction?mid=YOUR_MID_HERE&orderId=ORDERID_93761";

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
    $response = curl_exec($ch);
    print_r($response);

}

}
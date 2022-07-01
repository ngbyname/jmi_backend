<?php

return [
    'merchant_id' => env('PAYTM_MERCHANT_ID',''),
    'merchant_key' => env('PAYTM_MERCHANT_KEY',''),
    'merchant_website' => env('PAYTM_MERCHANT_WEBSITE',''),
    'channel' => env('PAYTM_CHANNEL',''),
    'industry_type' => env('PAYTM_INDUSTRY_TYPE',''),
    'settings' => array(
        'env' => env('PAYTM_ENVIRONMENT',''),
        'http.ConnectionTimeOut' => 30,
        'log.LogEnabled' => true,
        'log.FileName' => storage_path() . '/logs/paytm.log',
        'log.LogLevel' => 'ERROR'
    ),

];
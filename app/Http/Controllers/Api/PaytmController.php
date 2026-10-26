<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use paytm\paytmchecksum\PaytmChecksum;

class PaytmController extends Controller
{
    public function createOrder(Request $request)
    {
        $orderId = uniqid('ORDER_');
        $amount = $request->amount ?? 10;

        $paytmParams = [
            "MID" => env('PAYTM_MERCHANT_ID'),
            "ORDER_ID" => $orderId,
            "CUST_ID" => "CUST_" . uniqid(),
            "TXN_AMOUNT" => $amount,
            "CHANNEL_ID" => env('PAYTM_CHANNEL'),
            "WEBSITE" => env('PAYTM_MERCHANT_WEBSITE'),
            "INDUSTRY_TYPE_ID" => env('PAYTM_INDUSTRY_TYPE'),
            "CALLBACK_URL" => env('PAYTM_CALLBACK_URL'),
        ];

        // ✅ Generate checksum
        $paytmParams['CHECKSUMHASH'] = PaytmChecksum::generateSignature(
            $paytmParams,
            env('PAYTM_MERCHANT_KEY')
        );

        $paymentUrl = env('PAYTM_ENV') === 'staging'
            ? 'https://securegw-stage.paytm.in/order/process'
            : 'https://securegw.paytm.in/order/process';

        // ✅ Return only JSON (for Flutter)
        return response()->json([
            'status' => true,
            'message' => 'Paytm order created successfully',
            'order_id' => $orderId,
            'payment_url' => $paymentUrl,
            'params' => $paytmParams,
        ]);
    }

   public function callback(Request $request)
{
    // ✅ Test mode skip checksum
    if (env('PAYTM_TEST_MODE', true)) {
        return response()->json([
            'status' => 'success',
            'data' => $request->all(),
        ]);
    }

    $isValid = PaytmChecksum::verifySignature(
        $request->all(),
        env('PAYTM_MERCHANT_KEY'),
        $request->CHECKSUMHASH
    );

    if ($isValid) {
        return response()->json([
            'status' => 'success',
            'data' => $request->all(),
        ]);
    }

    return response()->json(['status' => 'failed']);
}
}
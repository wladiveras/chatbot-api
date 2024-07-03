<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use App\Services\Payment\PaymentService;
use App\Http\Requests\PaymentRequest;
use App\Http\Resources\PaymentResource;
use App\Http\Resources\PaymentCollection;

class PaymentController extends Controller
{
    private PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function pay(string $gateway, PaymentRequest $request)
    {
        Log::debug(__CLASS__.__FUNCTION__." => start", [
            'data' => [
                'request' => $request->validated(),
                'gateway' => $gateway,
            ],
        ]);

        $payment = $this->paymentService->gateway($gateway)->pay($request->validated());

        Log::debug(__CLASS__.__FUNCTION__." => end", [
            'data' => [
                'request' => $request->validated(),
                'gateway' => $gateway,
            ],
        ]);

        return new PaymentResource($payment);
    }

    public function checkPayment(string $gateway, int|string $id)
    {
        Log::debug(__CLASS__.__FUNCTION__." => start", [
            'data' => [
                'id' => $id,
                'gateway' => $gateway,
            ],
        ]);

        $payment = $this->paymentService->gateway($gateway)->checkPayment($id);

        Log::debug(__CLASS__.__FUNCTION__." => end", [
            'data' => [
                'id' => $id,
                'gateway' => $gateway,
            ],
        ]);
        return new PaymentResource($payment);
    }
}

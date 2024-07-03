<?php

namespace App\Http\Controllers;

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
        $payment = $this->paymentService->gateway($gateway)->pay($request->validated());

        return new PaymentResource($payment);
    }

    public function checkPayment(string $gateway, int|string $id)
    {
        $payment = $this->paymentService->gateway($gateway)->checkPayment($id);

        return new PaymentResource($payment);
    }
}

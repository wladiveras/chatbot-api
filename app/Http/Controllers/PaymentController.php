<?php

namespace App\Http\Controllers;

use App\Services\Payment\PaymentService;
use app\Http\Requests\User\UserRequest;
use App\Http\Resources\User\UserResource;
use App\Http\Resources\User\UserCollection;

class PaymentController extends Controller
{
    private PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function pay(string $gateway = 'braip', array $data)
    {
        $this->paymentService->gateway($gateway)->pay($data);
    }
    public function checkPayment(string $gateway = 'braip', int|string $id)
    {
        $this->paymentService->gateway($gateway)->checkPayment($id);
    }
}

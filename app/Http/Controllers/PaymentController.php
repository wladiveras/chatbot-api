<?php

namespace App\Http\Controllers;

use App\Http\Resources\PaymentResource;
use App\Services\Payment\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class PaymentController extends BaseController
{
    private PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function pay(string $gateway, Request $request): JsonResponse
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => start', [
            'request' => $request,
            'gateway' => $gateway,
        ]);

        try {
            $payment = $this->paymentService->gateway($gateway)->pay($request->validated());

            return $this->success(
                message: 'Pedido aberto com sucesso.',
                payload: new PaymentResource($payment)
            );

        } catch (\Exception $exception) {
            return $this->error(
                message: $exception->getMessage(),
                payload: $exception,
                code: 500
            );
        }
    }

    public function checkPayment(string $gateway, int|string $id): JsonResponse
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => start', [
            'id' => $id,
            'gateway' => $gateway,
        ]);

        try {
            $payment = $this->paymentService->gateway($gateway)->checkPayment($id);

            return $this->success(
                message: 'Pedido verificado com sucesso.',
                payload: new PaymentResource($payment)
            );

        } catch (\Exception $exception) {
            return $this->error(
                message: $exception->getMessage(),
                payload: $exception,
                code: 500
            );
        }
    }

}

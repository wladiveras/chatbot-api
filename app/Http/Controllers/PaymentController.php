<?php

namespace App\Http\Controllers;

use App\Http\Resources\ResponseResource;
use App\Http\Resources\ResponseCollection;
use App\Services\Payment\PaymentService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Validator;

class PaymentController extends BaseController
{
    private PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function pay(string $gateway, Request $request): JsonResponse
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running', [
            'request' => $request,
        ]);

        $data = Validator::make($request->all(), [
            'id' => 'string|max:255',
            'name' => 'string',
        ]);

        if ($data->fails()) {
            return $this->error(
                path: __CLASS__ . '.' . __FUNCTION__,
                code: 422
            );
        }

        $service = $this->paymentService->gateway($gateway)->pay($data->validate());

        if ($service->success) {
            return $this->success(
                title: "Redirecionando para o pagamento.",
                message: $service->message,
                payload: $service->payload
            );
        }

        return $this->error(
            path: __CLASS__ . '.' . __FUNCTION__,
            message: $service->message,
            code: $service->code
        );
    }

    public function checkPayment(string $gateway, int|string $id, Request $request): JsonResponse
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running', [
            'request' => $request,
        ]);

        $data = Validator::make($request->all(), [
            'id' => 'string|max:255',
            'name' => 'string',
        ]);

        if ($data->fails()) {
            return $this->error(
                path: __CLASS__ . '.' . __FUNCTION__,
                code: 422
            );
        }

        $service = $this->paymentService->gateway($gateway)->checkPayment($id);

        if ($service->success) {
            return $this->success(
                title: "Pagamento efetuado com sucesso!",
                message: $service->message,
                payload: $service->payload
            );
        }

        return $this->error(
            path: __CLASS__ . '.' . __FUNCTION__,
            message: $service->message,
            code: $service->code
        );
    }
}

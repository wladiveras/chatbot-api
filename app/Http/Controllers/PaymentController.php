<?php

namespace App\Http\Controllers;

use App\Http\Resources\PaymentResource;
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
        Log::debug(__CLASS__.'.'.__FUNCTION__.' => running', [
            'request' => $request,
        ]);

        $data = Validator::make($request->all(), [
            'id' => 'string|max:255',
            'name' => 'string',
        ]);

        if ($data->fails()) {
            return $this->error(
                path: __CLASS__.'.'.__FUNCTION__,
                response: Carbon::now()->toDateTimeString(),
                service: $data->errors(),
                code: 400
            );
        }

        try {
            $payment = $this->paymentService->gateway($gateway)->pay($data->validate());

            return $this->success(
                response: Carbon::now()->toDateTimeString(),
                service: new PaymentResource($payment)
            );

        } catch (\Exception $exception) {
            return $this->error(
                path: __CLASS__.'.'.__FUNCTION__,
                response: $exception->getMessage(),
                service: $request->all(),
                code: $exception->getCode()
            );
        }
    }

    public function checkPayment(string $gateway, int|string $id, Request $request): JsonResponse
    {
        Log::debug(__CLASS__.'.'.__FUNCTION__.' => running', [
            'request' => $request,
        ]);

        $data = Validator::make($request->all(), [
            'id' => 'string|max:255',
            'name' => 'string',
        ]);

        if ($data->fails()) {
            return $this->error(
                path: __CLASS__.'.'.__FUNCTION__,
                response: Carbon::now()->toDateTimeString(),
                service: $data->errors(),
                code: 400
            );
        }

        try {
            $payment = $this->paymentService->gateway($gateway)->checkPayment($id);

            return $this->success(
                response: Carbon::now()->toDateTimeString(),
                service: new PaymentResource($payment)
            );

        } catch (\Exception $exception) {
            return $this->error(
                path: __CLASS__.'.'.__FUNCTION__,
                response: $exception->getMessage(),
                service: $request->all(),
                code: $exception->getCode()
            );
        }
    }
}

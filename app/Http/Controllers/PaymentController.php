<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentRequest;
use App\Http\Resources\PaymentResource;
use App\Services\Payment\PaymentService;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    private PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function pay(string $gateway, PaymentRequest $request)
    {
        Log::debug(__CLASS__.'.'.__FUNCTION__.' => start', [
            'request' => $request,
            'gateway' => $gateway,
        ]);

        try {
            $payment = $this->paymentService->gateway($gateway)->pay($request->validated());
        } catch (\Exception $exception) {
            $this->error(data: $request, exception: $exception);
        }

        return new PaymentResource($payment);
    }

    public function checkPayment(string $gateway, int|string $id)
    {
        Log::debug(__CLASS__.'.'.__FUNCTION__.' => start', [
            'id' => $id,
            'gateway' => $gateway,
        ]);

        try {
            $payment = $this->paymentService->gateway($gateway)->checkPayment($id);

            return new PaymentResource($payment);
        } catch (\Exception $exception) {
            $this->error(data: [$id, $gateway], exception: $exception);
        }
    }

    private function error($data, $exception)
    {
        Log::error(__CLASS__.'.'.__FUNCTION__.' => error', [
            'data' => $data,
            'exception' => $exception,
            'message' => $exception->getMessage(),
        ]);

        abort(500, $exception->getMessage());
    }
}

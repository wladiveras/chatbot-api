<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use App\Services\Integration\IntegrationService;
use App\Http\Requests\PaymentRequest;
use App\Http\Resources\PaymentResource;
use App\Http\Resources\PaymentCollection;

class IntegrationController extends Controller
{
    private IntegrationService $integrationService;

    public function __construct(IntegrationService $integrationService)
    {
        $this->integrationService = $integrationService;
    }

    public function createInstance(string $integration, PaymentRequest $request)
    {
        Log::debug(__CLASS__.'.'.__FUNCTION__." => start", [
            'request' => $request,
            'integration' => $integration,
        ]);

        try {
            $payment = $this->integrationService->integration($integration)->createInstance($request->validated());
        }
        catch(\Exception $exception) {
            $this->error(data: $request, exception: $exception);
        }

        return new PaymentResource($payment);
    }

    public function connectInstance(string $integration, int|string $id)
    {
        Log::debug(__CLASS__.'.'.__FUNCTION__." => start", [
            'id' => $id,
            'integration' => $integration,
        ]);

        try {
            $payment = $this->integrationService->integration($integration)->connectInstance($id);
            return new PaymentResource($payment);
        }

        catch(\Exception $exception) {
            $this->error(data: [$id, $integration], exception: $exception);
        }
    }

    private function error($data, $exception) {
        Log::error(__CLASS__.'.'. __FUNCTION__." => error", [
            'data' => $data,
            'exception' => $exception,
            'message' => $exception->getMessage(),
        ]);

        abort(500, $exception->getMessage());
    }
}

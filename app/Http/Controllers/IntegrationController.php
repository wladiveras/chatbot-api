<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use App\Services\Integration\IntegrationService;
use App\Http\Requests\IntegrationRequest;
use App\Http\Resources\IntegrationResource;
use App\Http\Resources\IntegrationCollection;

class IntegrationController extends Controller
{
    private IntegrationService $integrationService;

    public function __construct(IntegrationService $integrationService)
    {
        $this->integrationService = $integrationService;
    }

    public function createInstance(string $connection, IntegrationRequest $request)
    {
        Log::debug(__CLASS__.'.'.__FUNCTION__." => start", [
            'request' => $request,
            'integration' => $connection,
        ]);

        try {
            $integration = $this->integrationService->integration($connection)->createInstance($request->validated());
        }
        catch(\Exception $exception) {
            $this->error(data: $request, exception: $exception);
        }

        return new IntegrationResource($integration);
    }

    public function connectInstance(string $connection, int|string $id)
    {
        Log::debug(__CLASS__.'.'.__FUNCTION__." => start", [
            'id' => $id,
            'integration' => $connection,
        ]);

        try {
            $integration = $this->integrationService->integration($connection)->connectInstance($id);
            return new IntegrationResource($integration);
        }

        catch(\Exception $exception) {
            $this->error(data: [$id, $connection], exception: $exception);
        }
    }

    public function sendPlainText(string $connection, IntegrationRequest $request)
    {
        Log::debug(__CLASS__.'.'.__FUNCTION__." => start", [
            'request' => $request,
            'integration' => $connection,
        ]);

        try {
            $integration = $this->integrationService->integration($connection)->sendPlainText($request->validated());
        }
        catch(\Exception $exception) {
            $this->error(data: $request, exception: $exception);
        }

        return new IntegrationResource($integration);
    }

    private function error($data, $exception) {
        Log::error(__CLASS__.'.'. __FUNCTION__." => error", [
            'message' => $exception->getMessage(),
            'data' => $data,
            'exception' => $exception,
        ]);

        abort(500, $exception->getMessage());
    }
}

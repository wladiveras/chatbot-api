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

    public function createConnection(string $connection, IntegrationRequest $request)
    {
        Log::debug(__CLASS__.'.'.__FUNCTION__." => start", [
            'request' => $request,
            'integration' => $connection,
        ]);

        try {
            $integration = $this->integrationService->integration($connection)->createConnection($request->validated());
        }
        catch(\Exception $exception) {
            $this->error(data: $request, exception: $exception);
        }

        return new IntegrationResource($integration);
    }

    public function connectInstance(string $connection, int|string $instance)
    {
        Log::debug(__CLASS__.'.'.__FUNCTION__." => start", [
            'instance' => $instance,
            'integration' => $connection,
        ]);

        try {
            $integration = $this->integrationService->integration($connection)->connectInstance($instance);
            return new IntegrationResource($integration);
        }

        catch(\Exception $exception) {
            $this->error(data: [$instance, $connection], exception: $exception);
        }
    }

    public function sendMessage(string $connection, IntegrationRequest $request)
    {
        Log::debug(__CLASS__.'.'.__FUNCTION__." => start", [
            'request' => $request,
            'integration' => $connection,
        ]);

        // Aqui vai definir qual vai ser o tipo de mensagem a ser enviada e chamar sua função expecifica.
        try {
            $integration = $this->integrationService->integration($connection)->sendTextMessage($request->validated());
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

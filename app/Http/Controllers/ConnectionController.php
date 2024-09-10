<?php

namespace App\Http\Controllers;

use App\Enums\MessagesType;
use App\Http\Resources\ResponseResource;
use App\Http\Resources\ResponseCollection;
use App\Services\Connection\ConnectionService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Validator;

class ConnectionController extends BaseController
{
    private ConnectionService $connectionService;

    public function __construct(ConnectionService $connectionService)
    {
        $this->connectionService = $connectionService;
    }

    public function index(Request $request): JsonResponse
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running', [
            'request' => $request,
        ]);

        try {
            $connectionService = $this->connectionService->fetchConnections();

            return $this->success(
                response: Carbon::now()->toDateTimeString(),
                service: new ResponseResource($connectionService)
            );

        } catch (\Exception $exception) {
            return $this->error(
                path: __CLASS__ . '.' . __FUNCTION__,
                response: $exception->getMessage(),
                service: $request->all(),
                code: $exception->getCode()
            );
        }
    }

    public function show(int|string $id, Request $request): JsonResponse
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running', [
            'request' => $request,
        ]);

        try {
            $connectionService = $this->connectionService->fetchConnection($id);

            return $this->success(
                response: Carbon::now()->toDateTimeString(),
                service: $connectionService
            );

        } catch (\Exception $exception) {
            return $this->error(
                path: __CLASS__ . '.' . __FUNCTION__,
                response: $exception->getMessage(),
                service: $request->all(),
                code: $exception->getCode()
            );
        }
    }

    public function createConnection(string $provinder, Request $request): JsonResponse
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running', [
            'request' => $request,
        ]);

        $data = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'description' => 'string',
            'connection_key' => 'string|unique:connections|max:255',
        ]);

        if ($data->fails()) {
            return $this->error(
                path: __CLASS__ . '.' . __FUNCTION__,
                response: Carbon::now()->toDateTimeString(),
                service: $data->errors(),
                code: 400
            );
        }

        try {
            $connectionService = $this->connectionService->integration($provinder)->createConnection($data->validate());

            return $this->success(
                response: Carbon::now()->toDateTimeString(),
                service: $connectionService
            );

        } catch (\Exception $exception) {
            return $this->error(
                path: __CLASS__ . '.' . __FUNCTION__,
                response: $exception->getMessage(),
                service: $request->all(),
                code: $exception->getCode()
            );
        }
    }

    public function connect(string $provinder, int|string $connection, Request $request): JsonResponse
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running', [
            'request' => $request,
        ]);

        try {
            $connectionService = $this->connectionService->integration($provinder)->connect($connection);

            return $this->success(
                response: Carbon::now()->toDateTimeString(),
                service: new ResponseResource($connectionService)
            );

        } catch (\Exception $exception) {
            return $this->error(
                path: __CLASS__ . '.' . __FUNCTION__,
                response: $exception->getMessage(),
                service: $request->all(),
                code: $exception->getCode()
            );
        }
    }

    public function selectFlow(int|string $provinder, int|string $connection_id, Request $request): JsonResponse
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running', [
            'request' => $request,
            'provinder' => $provinder,
        ]);

        $data = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'description' => 'string',
            'flow_id' => 'integer|nullable',
        ]);

        if ($data->fails()) {
            return $this->error(
                path: __CLASS__ . '.' . __FUNCTION__,
                response: Carbon::now()->toDateTimeString(),
                service: $data->errors(),
                code: 400
            );
        }

        try {
            $connectionService = $this->connectionService->changeConnectionFlow($connection_id, $data->validate());

            return $this->success(
                response: Carbon::now()->toDateTimeString(),
                service: new $connectionService
            );

        } catch (\Exception $exception) {
            return $this->error(
                path: __CLASS__ . '.' . __FUNCTION__,
                response: $exception->getMessage(),
                service: $request->all(),
                code: $exception->getCode()
            );
        }
    }

    public function profile(string $provinder, int|string $connection, Request $request): JsonResponse
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running', [
            'request' => $request,
        ]);

        $data = Validator::make($request->all(), [
            'number' => 'integer|required',
        ]);

        if ($data->fails()) {
            return $this->error(
                path: __CLASS__ . '.' . __FUNCTION__,
                response: Carbon::now()->toDateTimeString(),
                service: $data->errors(),
                code: 400
            );
        }

        try {
            $connectionService = $this->connectionService->integration($provinder)->getConnectionProfile($connection, $data->validate());

            return $this->success(
                response: Carbon::now()->toDateTimeString(),
                service: new ResponseResource($connectionService)
            );

        } catch (\Exception $exception) {
            return $this->error(
                path: __CLASS__ . '.' . __FUNCTION__,
                response: $exception->getMessage(),
                service: $request->all(),
                code: $exception->getCode()
            );
        }
    }

    public function disconnect(string $provinder, int|string $connection, Request $request): JsonResponse
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running', [
            'request' => $request,
        ]);

        try {
            $connectionService = $this->connectionService->integration($provinder)->disconnect($connection);

            return $this->success(
                response: Carbon::now()->toDateTimeString(),
                service: new ResponseResource($connectionService)
            );

        } catch (\Exception $exception) {
            return $this->error(
                path: __CLASS__ . '.' . __FUNCTION__,
                response: $exception->getMessage(),
                service: $request->all(),
                code: $exception->getCode()
            );
        }
    }

    public function delete(string $provinder, int|string $connection, Request $request): JsonResponse
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running', [
            'request' => $request,
        ]);

        try {
            $connectionService = $this->connectionService->integration($provinder)->delete($connection);

            return $this->success(
                response: Carbon::now()->toDateTimeString(),
                service: $connectionService
            );

        } catch (\Exception $exception) {
            return $this->error(
                path: __CLASS__ . '.' . __FUNCTION__,
                response: $exception->getMessage(),
                service: $request->all(),
                code: $exception->getCode()
            );
        }
    }

    public function sendMessage(string $provider, Request $request): JsonResponse
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running', [
            'request' => $request,
        ]);

        $data = Validator::make($request->all(), [
            'type' => [Rule::enum(MessagesType::class)],
            'value' => 'string',
            'connection' => 'string|required',
            'number' => 'string|required',
            'delay' => 'integer',
            'caption' => 'string',
        ]);

        if ($data->fails()) {
            return $this->error(
                path: __CLASS__ . '.' . __FUNCTION__,
                response: Carbon::now()->toDateTimeString(),
                service: $data->errors(),
                code: 400
            );
        }

        try {
            $connectionService = $this->connectionService->integration($provider)->send($data->validate());

            return $this->success(
                response: Carbon::now()->toDateTimeString(),
                service: $connectionService
            );

        } catch (\Exception $exception) {
            return $this->error(
                path: __CLASS__ . '.' . __FUNCTION__,
                response: $exception->getMessage(),
                service: $request->all(),
                code: $exception->getCode()
            );
        }
    }

    public function callback(string $provider, Request $request)
    {
        $request = $request->all();
        $event = Arr::get($request, 'data.event', Arr::get($request, 'event'));

        if ($event !== 'connection.update') {
            Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running', [
                'request' => $request,
            ]);
        }

        try {
            $connectionService = $this->connectionService->integration($provider)->callback($request);

            return $this->success(
                response: Carbon::now()->toDateTimeString(),
                service: $connectionService
            );

        } catch (\Exception $exception) {
            return $this->error(
                path: __CLASS__ . '.' . __FUNCTION__,
                response: $exception->getMessage(),
                service: $request,
                code: $exception->getCode()
            );
        }
    }
}

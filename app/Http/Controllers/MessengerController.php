<?php

namespace App\Http\Controllers;

use App\Enums\MessagesType;
use App\Http\Resources\MessengerResource;
use App\Services\Messenger\MessengerService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Validator;

class MessengerController extends BaseController
{
    private MessengerService $messengerService;

    public function __construct(MessengerService $messengerService)
    {
        $this->messengerService = $messengerService;
    }

    public function index(Request $request): JsonResponse
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running', [
            'request' => $request,
        ]);

        try {
            $messengerService = $this->messengerService->fetchConnections();

            return $this->success(
                response: Carbon::now()->toDateTimeString(),
                service: new MessengerResource($messengerService)
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
            $messengerService = $this->messengerService->fetchConnection($id);

            return $this->success(
                response: Carbon::now()->toDateTimeString(),
                service: new MessengerResource($messengerService)
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
            $messengerService = $this->messengerService->integration($provinder)->createConnection($data->validate());

            return $this->success(
                response: Carbon::now()->toDateTimeString(),
                service: $messengerService
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
            $messengerService = $this->messengerService->integration($provinder)->connect($connection);

            return $this->success(
                response: Carbon::now()->toDateTimeString(),
                service: new MessengerResource($messengerService)
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

    public function selectFlow(string $provinder, int|string $connection_id, Request $request): JsonResponse
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running', [
            'request' => $request,
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
            $messengerService = $this->messengerService->updateSelectFlow($connection_id, $data->validate());

            return $this->success(
                response: Carbon::now()->toDateTimeString(),
                service: new MessengerResource($messengerService)
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
            $messengerService = $this->messengerService->integration($provinder)->getConnectionProfile($connection, $data->validate());

            return $this->success(
                response: Carbon::now()->toDateTimeString(),
                service: new MessengerResource($messengerService)
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

    public function status(string $provinder, int|string $connection, Request $request): JsonResponse
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running', [
            'request' => $request,
        ]);

        try {
            $messengerService = $this->messengerService->integration($provinder)->status($connection);

            return $this->success(
                response: Carbon::now()->toDateTimeString(),
                service: new MessengerResource($messengerService)
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
            $messengerService = $this->messengerService->integration($provinder)->disconnect($connection);

            return $this->success(
                response: Carbon::now()->toDateTimeString(),
                service: new MessengerResource($messengerService)
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
            $messengerService = $this->messengerService->integration($provinder)->delete($connection);

            return $this->success(
                response: Carbon::now()->toDateTimeString(),
                service: new MessengerResource($messengerService)
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
            $messengerService = $this->messengerService->integration($provider)->send($data->validate());

            return $this->success(
                response: Carbon::now()->toDateTimeString(),
                service: new MessengerResource($messengerService)
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
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running', [
            'request' => $request,
        ]);

        try {
            $messengerService = $this->messengerService->integration($provider)->callback($request->all());

            return $this->success(
                response: Carbon::now()->toDateTimeString(),
                service: $messengerService
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
}

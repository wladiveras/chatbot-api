<?php

namespace App\Http\Controllers;

use App\Enums\MessagesType;
use App\Services\Connection\ConnectionService;
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

        $service = $this->connectionService->fetchConnections();

        if ($service->success) {
            return $this->success(
                title: "Conexões retornadas.",
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

    public function show(int|string $id, Request $request): JsonResponse
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running', [
            'request' => $request,
        ]);

        $service = $this->connectionService->fetchConnection($id);

        if ($service->success) {
            return $this->success(
                title: "Conexão retornada.",
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

    public function createConnection(string $provider, Request $request): JsonResponse
    {
        Log::debug(message: __CLASS__ . '.' . __FUNCTION__ . ' => running', context: [
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
                code: 422
            );
        }

        $service = $this->connectionService->integration(provider: $provider)->createConnection($data->validate());

        if ($service->success) {
            return $this->success(
                title: "Conexão criada.",
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

    public function connect(string $provider, int|string $connection, Request $request): JsonResponse
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running', [
            'request' => $request,
        ]);

        $service = $this->connectionService->integration(provider: $provider)->connect(connection: $connection);

        if ($service->success) {
            return $this->success(
                title: "Conexão estabelecida.",
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

    public function selectFlow(int|string $provider, int|string $connection_id, Request $request): JsonResponse
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running', [
            'request' => $request,
            'provider' => $provider,
        ]);

        $data = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'description' => 'string',
            'flow_id' => 'integer|nullable',
        ]);

        if ($data->fails()) {
            return $this->error(
                path: __CLASS__ . '.' . __FUNCTION__,
                code: 422
            );
        }

        $service = $this->connectionService->changeConnectionFlow($connection_id, $data->validate());

        if ($service->success) {
            return $this->success(
                title: "Fluxo selecionado.",
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

    public function profile(string $provider, int|string $connection, Request $request): JsonResponse
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
                code: 422
            );
        }

        $service = $this->connectionService->integration($provider)->getConnectionProfile($connection, $data->validate());

        if ($service->success) {
            return $this->success(
                title: "Whatsapp estabelecido.",
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

    public function disconnect(string $provider, int|string $connection, Request $request): JsonResponse
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running', [
            'request' => $request,
        ]);

        $service = $this->connectionService->integration($provider)->disconnect($connection);

        if ($service->success) {
            return $this->success(
                title: "Conexão encerrada.",
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

    public function delete(string $provider, int|string $connection, Request $request): JsonResponse
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running', [
            'request' => $request,
        ]);

        $service = $this->connectionService->integration($provider)->delete($connection);

        if ($service->success) {
            return $this->success(
                title: "Conexão deletada.",
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
                code: 422
            );
        }

        $service = $this->connectionService->integration($provider)->send($data->validate());

        if ($service->success) {
            return $this->success(
                title: "Mensagem enviada.",
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

    public function callback(string $provider, Request $request): JsonResponse
    {
        $request = $request->all();
        $event = Arr::get($request, 'data.event', Arr::get($request, 'event'));

        if ($event !== 'connection.update') {
            Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running', [
                'request' => $request,
            ]);
        }

        $service = $this->connectionService->integration($provider)->callback($request);

        if ($service->success) {
            return $this->success(
                title: "Callback recebido.",
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

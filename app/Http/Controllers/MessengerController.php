<?php

namespace App\Http\Controllers;


use App\Http\Resources\MessengerResource;
use App\Services\Messenger\MessengerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Validator;
use Illuminate\Validation\Rule;
use App\Enums\MessagesType;


class MessengerController extends BaseController
{
    private MessengerService $messengerService;

    public function __construct(MessengerService $messengerService)
    {
        $this->messengerService = $messengerService;
    }

    public function createConnection(string $provinder, Request $request): JsonResponse
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => start', [
            'request' => $request,
            'provinder' => $provinder,
        ]);

        $data = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'description' => 'string',
            'connection_key' => 'string|unique:connections|max:255',
        ]);

        if ($data->fails()) {
            return $this->error(
                message: 'Error de validação de dados.',
                payload: $data->errors(),
                code: 400
            );
        }

        try {
            $messengerService = $this->messengerService->integration($provinder)->createConnection($data->validated());

            return $this->success(
                message: 'Conexão Criada com sucesso.',
                payload: $messengerService
            );

        } catch (\Exception $exception) {
            return $this->error(
                message: $exception->getMessage(),
                payload: $request->all(),
                code: $exception->getCode()
            );
        }
    }

    public function connect(string $provinder, int|string $connection, Request $request): JsonResponse
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => start', [
            'connection' => $connection,
            'provinder' => $provinder,
        ]);

        try {
            $messengerService = $this->messengerService->integration($provinder)->connect($connection);

            return $this->success(
                message: 'Conexão retornada com sucesso.',
                payload: new MessengerResource($messengerService)
            );

        } catch (\Exception $exception) {
            return $this->error(
                message: $exception->getMessage(),
                payload: $request->all(),
                code: $exception->getCode()
            );
        }
    }

    public function status(string $provinder, int|string $connection, Request $request): JsonResponse
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => start', [
            'connection' => $connection,
            'provinder' => $provinder,
        ]);

        try {
            $messengerService = $this->messengerService->integration($provinder)->status($connection);

            return $this->success(
                message: 'Status da conexão retornado com sucesso.',
                payload: new MessengerResource($messengerService)
            );

        } catch (\Exception $exception) {
            return $this->error(
                message: $exception->getMessage(),
                payload: $request->all(),
                code: $exception->getCode()
            );
        }
    }

    public function disconnect(string $provinder, int|string $connection, Request $request): JsonResponse
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => start', [
            'connection' => $connection,
            'provinder' => $provinder,
        ]);

        try {
            $messengerService = $this->messengerService->integration($provinder)->disconnect($connection);

            return $this->success(
                message: 'Conexão desconectada com sucesso.',
                payload: new MessengerResource($messengerService)
            );

        } catch (\Exception $exception) {
            return $this->error(
                message: $exception->getMessage(),
                payload: $request->all(),
                code: $exception->getCode()
            );
        }
    }

    public function delete(string $provinder, int|string $connection, Request $request): JsonResponse
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => start', [
            'connection' => $connection,
            'provinder' => $provinder,
        ]);

        try {
            $messengerService = $this->messengerService->integration($provinder)->delete($connection);

            return $this->success(
                message: 'Conexão deletada com sucesso.',
                payload: new MessengerResource($messengerService)
            );

        } catch (\Exception $exception) {
            return $this->error(
                message: $exception->getMessage(),
                payload: $request->all(),
                code: $exception->getCode()
            );
        }
    }

    public function sendMessage(string $provider, Request $request): JsonResponse
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => start', [
            'request' => $request,
            'provider' => $provider,
        ]);

        $data = Validator::make($request->all(), [
            'type' => [Rule::enum(MessagesType::class)],
            'message' => 'string',
            'connection' => 'string|required',
            'number' => 'string|required',
            'delay' => 'integer',
            'caption' => 'string',
            'file_url' => 'string',
        ]);

        if ($data->fails()) {
            return $this->error(
                message: 'Error de validação de dados.',
                payload: $data->errors(),
                code: 400
            );
        }

        try {
            $messengerService = $this->messengerService->integration($provider)->send($data->validated());

            return $this->success(
                message: 'Mensagem enviada com sucesso.',
                payload: new MessengerResource($messengerService)
            );

        } catch (\Exception $exception) {
            return $this->error(
                message: $exception->getMessage(),
                payload: $request->all(),
                code: $exception->getCode()
            );
        }
    }

    public function callback(string $provider, Request $request)
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => start', [
            'request' => $request,
            'provider' => $provider,
        ]);

        try {
            $messengerService = $this->messengerService->integration($provider)->callback($request->all());

            return $this->success(
                message: 'Callback recebido com sucesso.',
                payload: $messengerService
            );

        } catch (\Exception $exception) {
            return $this->error(
                message: $exception->getMessage(),
                payload: $request->all(),
                code: $exception->getCode()
            );
        }
    }

}

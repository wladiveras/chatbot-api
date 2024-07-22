<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateConnectionRequest;
use App\Http\Requests\MessengerRequest;
use App\Http\Resources\MessengerResource;
use App\Services\Messenger\MessengerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MessengerController extends BaseController
{
    private MessengerService $messengerService;

    public function __construct(MessengerService $messengerService)
    {
        $this->messengerService = $messengerService;
    }

    public function createConnection(string $provinder, CreateConnectionRequest $request)
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => start', [
            'request' => $request,
            'provinder' => $provinder,
        ]);

        try {
            $messengerService = $this->messengerService->integration($provinder)->createConnection($request->validated());

            $this->response(message: 'Conexão Criada com sucesso.', payload: $messengerService);

        } catch (\Exception $exception) {
            $this->error(message: $exception->getMessage(), payload: $request, code: 500);
        }

        return new MessengerResource($messengerService);
    }

    public function connect(string $provinder, int|string $connection)
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => start', [
            'connection' => $connection,
            'provinder' => $provinder,
        ]);

        try {
            $messengerService = $this->messengerService->integration($provinder)->connect($connection);

            return $this->response(
                message: 'Conexão retornada com sucesso.',
                payload: new MessengerResource($messengerService)
            );

        } catch (\Exception $exception) {
            $this->error(message: $exception->getMessage(), payload: $exception, code: 500);
        }
    }

    public function status(string $provinder, int|string $connection)
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => start', [
            'connection' => $connection,
            'provinder' => $provinder,
        ]);

        try {
            $messengerService = $this->messengerService->integration($provinder)->status($connection);

            return $this->response(
                message: 'Status da conexão retornado com sucesso.',
                payload: new MessengerResource($messengerService)
            );

        } catch (\Exception $exception) {
            $this->error(message: $exception->getMessage(), payload: $exception, code: 500);
        }
    }

    public function disconnect(string $provinder, int|string $connection)
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => start', [
            'connection' => $connection,
            'provinder' => $provinder,
        ]);

        try {
            $messengerService = $this->messengerService->integration($provinder)->disconnect($connection);

            return $this->response(
                message: 'Conexão desconectada com sucesso.',
                payload: new MessengerResource($messengerService)
            );

        } catch (\Exception $exception) {
            $this->error(message: $exception->getMessage(), payload: $exception, code: 500);
        }
    }

    public function delete(string $provinder, int|string $connection)
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => start', [
            'connection' => $connection,
            'provinder' => $provinder,
        ]);

        try {
            $messengerService = $this->messengerService->integration($provinder)->delete($connection);

            return $this->response(
                message: 'Conexão deletada com sucesso.',
                payload: new MessengerResource($messengerService)
            );

        } catch (\Exception $exception) {
            $this->error(message: $exception->getMessage(), payload: $exception, code: 500);
        }
    }

    public function sendMessage(string $provider, MessengerRequest $request)
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => start', [
            'request' => $request,
            'provider' => $provider,
        ]);

        // Aqui vai definir qual vai ser o tipo de mensagem a ser enviada e chamar sua função expecifica.
        try {
            $messengerService = $this->messengerService->integration($provider)->send($request->validated());

            return $this->response(
                message: 'Mensagem enviada com sucesso.',
                payload: new MessengerResource($messengerService)
            );

        } catch (\Exception $exception) {
            $this->error(message: $exception->getMessage(), payload: $request, code: 500);
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

            return $this->response(
                message: 'Callback recebido com sucesso.',
                payload: $messengerService
            );

        } catch (\Exception $exception) {
            $this->error(message: $exception->getMessage(), payload: $request, code: 500);
        }
    }

}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateConnectionRequest;
use App\Http\Requests\MessengerRequest;
use App\Http\Resources\MessengerResource;
use App\Services\Messenger\MessengerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MessengerController extends Controller
{
    private MessengerService $messengerService;

    public function __construct(MessengerService $messengerService)
    {
        $this->messengerService = $messengerService;
    }

    public function createConnection(string $provinder, CreateConnectionRequest $request)
    {
        Log::debug(__CLASS__.'.'.__FUNCTION__.' => start', [
            'request' => $request,
            'provinder' => $provinder,
        ]);

        try {
            $messengerService = $this->messengerService->integration($provinder)->createConnection($request->validated());
        } catch (\Exception $exception) {
            $this->error(data: $request, exception: $exception);
        }

        return new MessengerResource($messengerService);
    }

    public function connect(string $provinder, int|string $connection)
    {
        Log::debug(__CLASS__.'.'.__FUNCTION__.' => start', [
            'connection' => $connection,
            'provinder' => $provinder,
        ]);

        try {
            $messengerService = $this->messengerService->integration($provinder)->connect($connection);

            return new MessengerResource($messengerService);
        } catch (\Exception $exception) {
            $this->error(data: [$connection, $provinder], exception: $exception);
        }
    }

    public function status(string $provinder, int|string $connection)
    {
        Log::debug(__CLASS__.'.'.__FUNCTION__.' => start', [
            'connection' => $connection,
            'provinder' => $provinder,
        ]);

        try {
            $messengerService = $this->messengerService->integration($provinder)->status($connection);

            return new MessengerResource($messengerService);
        } catch (\Exception $exception) {
            $this->error(data: [$connection, $provinder], exception: $exception);
        }
    }

    public function disconnect(string $provinder, int|string $connection)
    {
        Log::debug(__CLASS__.'.'.__FUNCTION__.' => start', [
            'connection' => $connection,
            'provinder' => $provinder,
        ]);

        try {
            $messengerService = $this->messengerService->integration($provinder)->disconnect($connection);

            return new MessengerResource($messengerService);
        } catch (\Exception $exception) {
            $this->error(data: [$connection, $provinder], exception: $exception);
        }
    }

    public function delete(string $provinder, int|string $connection)
    {
        Log::debug(__CLASS__.'.'.__FUNCTION__.' => start', [
            'connection' => $connection,
            'provinder' => $provinder,
        ]);

        try {
            $messengerService = $this->messengerService->integration($provinder)->delete($connection);

            return new MessengerResource($messengerService);
        } catch (\Exception $exception) {
            $this->error(data: [$connection, $provinder], exception: $exception);
        }
    }

    public function sendMessage(string $provider, MessengerRequest $request)
    {
        Log::debug(__CLASS__.'.'.__FUNCTION__.' => start', [
            'request' => $request,
            'provider' => $provider,
        ]);

        // Aqui vai definir qual vai ser o tipo de mensagem a ser enviada e chamar sua função expecifica.
        try {
            $messengerService = $this->messengerService->integration($provider)->send($request->validated());
        } catch (\Exception $exception) {
            $this->error(data: $request, exception: $exception);
        }

        return new MessengerResource($messengerService);
    }

    public function callback(string $provider, Request $request)
    {
        Log::debug(__CLASS__.'.'.__FUNCTION__.' => start', [
            'request' => $request,
            'provider' => $provider,
        ]);

        try {
            $messengerService = $this->messengerService->integration($provider)->callback($request->all());
        } catch (\Exception $exception) {
            $this->error(data: $request, exception: $exception);
        }

        return (object) $messengerService;
    }

    private function error($data, $exception)
    {
        Log::error(__CLASS__.'.'.__FUNCTION__.' => error', [
            'message' => $exception->getMessage(),
            'data' => $data,
            'exception' => $exception,
        ]);

        abort(500, $exception->getMessage());
    }
}

<?php

namespace App\Services\Connection;

use App\Repositories\Connection\ConnectionRepository;
use App\Services\BaseService;
use App\Services\Connection\Provinder\WhatsappProvinder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use stdClass;

class ConnectionService extends BaseService
{
    private ConnectionRepository $connectionRepository;

    public function __construct()
    {
        $this->connectionRepository = App::make(ConnectionRepository::class);
    }

    public static function integration(string $provinder): ConnectionServiceInterface
    {
        return match ($provinder) {
            'whatsapp' => new WhatsappProvinder,
            default => throw new \InvalidArgumentException('Invalid integration service.', 404),
        };
    }

    public function fetchConnections(): ?stdClass
    {
        Log::debug(__CLASS__.'.'.__FUNCTION__.' => running');

        try {
            $connections = $this->connectionRepository->getUserConnections();

            if (! $connections) {
                return $this->error(
                    path: __CLASS__.'.'.__FUNCTION__,
                    message: 'Não deu certo, não foi possivel trazer as conexões.',
                    code: 400
                );
            }

            return $this->success(
                message: 'Tudo certo, as conexões foi retornado.',
                payload: $connections
            );

        } catch (\Exception $e) {
            return $this->error(
                path: __CLASS__.'.'.__FUNCTION__,
                message: $e->getMessage(),
                code: $e->getCode()
            );
        }
    }

    public function fetchConnection($id): ?stdClass
    {
        Log::debug(__CLASS__.'.'.__FUNCTION__.' => running');

        try {

            $connections = $this->connectionRepository->getUserConnection($id);

            if (! $connections) {
                return $this->error(
                    path: __CLASS__.'.'.__FUNCTION__,
                    message: 'Não deu certo, não foi possivel trazer as conexão.',
                    code: 400
                );
            }

            return $this->success(
                message: 'Tudo certo, a conexão foi retornado.',
                payload: $connections
            );

        } catch (\Exception $e) {
            return $this->error(
                path: __CLASS__.'.'.__FUNCTION__,
                message: $e->getMessage(),
                code: $e->getCode()
            );
        }
    }

    public function changeConnectionFlow($connection_id, $data): ?stdClass
    {
        Log::debug(__CLASS__.'.'.__FUNCTION__.' => running');

        try {
            $connection = $this->connectionRepository->changeConnectionFlow($connection_id, $data);

            if (! $connection) {
                return $this->error(
                    path: __CLASS__.'.'.__FUNCTION__,
                    message: 'Não deu certo, não foi alterar o fluxo.',
                    code: 400
                );
            }

            $this->connectionRepository->deleteUserConnectionCacheKey($connection_id);

            return $this->success(
                message: 'Tudo certo, o fluxo foi alterado com sucesso.',
                payload: $connection
            );

        } catch (\Exception $e) {
            return $this->error(
                path: __CLASS__.'.'.__FUNCTION__,
                message: $e->getMessage(),
                code: $e->getCode()
            );
        }
    }
}

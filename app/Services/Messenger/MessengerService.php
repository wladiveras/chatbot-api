<?php

namespace App\Services\Messenger;

use App\Repositories\Connection\ConnectionRepository;
use App\Services\BaseService;
use App\Services\Messenger\Provinder\WhatsappProvinder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use stdClass;

class MessengerService extends BaseService
{
    private ConnectionRepository $connectionRepository;

    public function __construct()
    {
        $this->connectionRepository = App::make(ConnectionRepository::class);
    }

    public static function integration(string $provinder): MessengerServiceInterface
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running');

        return match ($provinder) {
            'whatsapp' => new WhatsappProvinder,
            default => throw new \InvalidArgumentException('Invalid integration service.', 404),
        };
    }

    public function fetchConnections(): ?stdClass
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running');

        try {

            $connections = $this->connectionRepository->getUserConnections();

            if (!$connections) {
                return $this->error(
                    path: __CLASS__ . '.' . __FUNCTION__,
                    message: 'Não deu certo, não foi possivel trazer as conexões.',
                    code: 400
                );
            }

            return $this->success(
                message: 'Tudo certo, os conexões foi retornado.',
                payload: $connections
            );

        } catch (\Exception $e) {
            return $this->error(
                path: __CLASS__ . '.' . __FUNCTION__,
                message: $e->getMessage(),
                code: $e->getCode()
            );
        }
    }

    public function fetchConnection($id): ?stdClass
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running');

        try {

            $connections = $this->connectionRepository->getUserConnection($id);

            if (!$connections) {
                return $this->error(
                    path: __CLASS__ . '.' . __FUNCTION__,
                    message: 'Não deu certo, não foi possivel trazer as conexões.',
                    code: 400
                );
            }

            return $this->success(
                message: 'Tudo certo, os conexões foi retornado.',
                payload: $connections
            );

        } catch (\Exception $e) {
            return $this->error(
                path: __CLASS__ . '.' . __FUNCTION__,
                message: $e->getMessage(),
                code: $e->getCode()
            );
        }
    }
}

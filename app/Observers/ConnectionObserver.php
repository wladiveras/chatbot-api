<?php

namespace App\Observers;

use App\Models\Connection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Events\ConnectionStatusEvent;
use App\Repositories\Connection\ConnectionRepository;

class ConnectionObserver
{
    protected $connectionRepository;

    public function __construct()
    {
        $this->connectionRepository = App::make(ConnectionRepository::class);
    }

    /**
     * Handle the Connection "created" event.
     */
    public function created(Connection $connection): void
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => observer created', [
            'connection' => $connection,
        ]);

        $this->connectionRepository->deleteUserConnectionsCacheKey($connection->user_id);
    }

    /**
     * Handle the Connection "updated" event.
     */
    public function updated(Connection $connection): void
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => observer updated', [
            'connection' => $connection,
        ]);

        if ($connection === null) {
            return;
        }

        if ($connection->isDirty("is_active")) {
            event(new ConnectionStatusEvent($connection));
        }


        $this->connectionRepository->deleteUserConnectionsCacheKey($connection->user_id);
    }

    /**
     * Handle the Connection "deleted" event.
     */
    public function deleted(Connection $connection): void
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => observer deleted', [
            'connection' => $connection,
        ]);

        $this->connectionRepository->deleteUserConnectionsCacheKey($connection->user_id);
    }

    /**
     * Handle the Connection "restored" event.
     */
    public function restored(Connection $connection): void
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => observer restored', [
            'connection' => $connection,
        ]);

        $this->connectionRepository->deleteUserConnectionsCacheKey($connection->user_id);
    }

    /**
     * Handle the Connection "force deleted" event.
     */
    public function forceDeleted(Connection $connection): void
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => observer forceDeleted', [
            'connection' => $connection,
        ]);

        $this->connectionRepository->deleteUserConnectionsCacheKey($connection->user_id);
    }
}

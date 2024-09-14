<?php

namespace App\Observers;

use App\Models\ConnectionProfile;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Repositories\Connection\ConnectionRepository;

class ConnectionProfileObserver
{
    protected $connectionRepository;

    public function __construct()
    {
        $this->connectionRepository = App::make(ConnectionRepository::class);
    }

    /**
     * Handle the ConnectionProfile "created" event.
     */
    public function created(ConnectionProfile $connectionProfile): void
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => observer created', [
            'connectionProfile' => $connectionProfile,
        ]);

        $this->connectionRepository->deleteUserConnectionsCacheKey($connectionProfile->user_id);
    }

    /**
     * Handle the ConnectionProfile "updated" event.
     */
    public function updated(ConnectionProfile $connectionProfile): void
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => observer updated', [
            'connectionProfile' => $connectionProfile,
        ]);

        if ($connectionProfile === null) {
            return;
        }

        $this->connectionRepository->deleteUserConnectionsCacheKey($connectionProfile->user_id);
    }

    /**
     * Handle the ConnectionProfile "deleted" event.
     */
    public function deleted(ConnectionProfile $connectionProfile): void
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => observer deleted', [
            'connectionProfile' => $connectionProfile,
        ]);

        $this->connectionRepository->deleteUserConnectionsCacheKey($connectionProfile->user_id);
    }

    /**
     * Handle the ConnectionProfile "restored" event.
     */
    public function restored(ConnectionProfile $connectionProfile): void
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => observer restored', [
            'connectionProfile' => $connectionProfile,
        ]);

        $this->connectionRepository->deleteUserConnectionsCacheKey($connectionProfile->user_id);
    }

    /**
     * Handle the ConnectionProfile "force deleted" event.
     */
    public function forceDeleted(ConnectionProfile $connectionProfile): void
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => observer forceDeleted', [
            'connectionProfile' => $connectionProfile,
        ]);

        $this->connectionRepository->deleteUserConnectionsCacheKey($connectionProfile->user_id);
    }
}

<?php

namespace App\Repositories\Connection;

use App\Models\Connection;
use App\Repositories\BaseRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class ConnectionRepository extends BaseRepository implements ConnectionRepositoryInterface
{
    protected $cacheTime = 120;

    protected $user;

    public function __construct(Connection $model)
    {
        parent::__construct($model);
        $this->user = auth()->user();
    }

    /**
     * Get all connections for the authenticated user.
     */
    public function getUserConnections(): ?Collection
    {
        $cacheKey = $this->getUserConnectionsCacheKey($this->user->id);

        return Cache::remember($cacheKey, $this->cacheTime, function () {
            return $this->model
                ->auth()
                ->with(['connectionProfile'])
                ->orderBy('id', 'desc')
                ->select('id', 'name', 'description', 'is_active')
                ->get();
        });
    }

    /**
     * Get a specific connection for the authenticated user by ID.
     */
    public function getUserConnection(int|string $id): ?Connection
    {
        return $this->model
            ->auth()
            ->where('id', $id)
            ->orderBy('id', 'desc')
            ->first();
    }

    /**
     * Update the selected flow for a connection.
     */
    public function changeConnectionFlow(int $connection_id, array $data): ?bool
    {
        return $this->model->where('id', $connection_id)
            ->auth()
            ->update([
                'flow_id' => $data['flow_id'],
            ]);
    }

    // Caches control
    private function getUserConnectionsCacheKey(int $UserId): string
    {
        return "user_connections_{$UserId}";
    }

    public function deleteUserConnectionsCacheKey(int $userId): bool
    {
        $cacheKey = $this->getUserConnectionsCacheKey($userId);

        return $this->deleteCacheKey($cacheKey);
    }

}

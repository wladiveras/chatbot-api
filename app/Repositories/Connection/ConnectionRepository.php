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
        $cacheKey = $this->getUserConnectionsCacheKey();

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
        $cacheKey = $this->getUserConnectionCacheKey($id);

        return Cache::remember($cacheKey, $this->cacheTime, function () use ($id) {
            return $this->model
                ->auth()
                ->where('id', $id)
                ->orderBy('id', 'desc')
                ->first();
        });
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

    //

    private function getUserConnectionsCacheKey(): string
    {
        return "user_connections_{$this->user->id}";
    }

    /**
     * Delete the user flows cache key if it exists.
     *
     * @return bool True if the cache key was deleted, false otherwise.
     */
    public function deleteUserConnectionsCacheKey(): bool
    {
        $cacheKey = $this->getUserConnectionsCacheKey();

        return $this->deleteCacheKey($cacheKey);
    }

    /**
     * Generate cache key for a specific user flow.
     */
    private function getUserConnectionCacheKey(int|string $id): string
    {
        return "user_{$this->user->id}_connection_{$id}";
    }

    /**
     * Delete the user flow cache key if it exists.
     *
     * @return bool True if the cache key was deleted, false otherwise.
     */
    public function deleteUserConnectionCacheKey(int|string $id): bool
    {
        $cacheKey = $this->getUserConnectionCacheKey($id);

        return $this->deleteCacheKey($cacheKey);
    }
}

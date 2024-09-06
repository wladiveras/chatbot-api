<?php

namespace App\Repositories\Flow;

use App\Models\Flow;
use App\Repositories\BaseRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class FlowRepository extends BaseRepository implements FlowRepositoryInterface
{
    protected $cacheTime = 120;
    protected $user;

    public function __construct(Flow $model)
    {
        parent::__construct($model);
        $this->user = auth()->user();
    }

    /**
     * Get all flows for the authenticated user.
     *
     * @return Collection|null
     */
    public function getUserFlows(): ?Collection
    {
        $cacheKey = $this->getUserFlowsCacheKey();

        return Cache::remember($cacheKey, $this->cacheTime, function () {
            return $this->model->UserScope()->where('user_id', $this->user->id)

                ->orderBy('id', 'desc')
                ->select(
                    'name',
                    'id',
                    'description',
                    'updated_at',
                    'created_at',
                    'is_active',
                    'recovery_flow_id',
                    'recovery_days',
                    'finished_flow_id',
                    'finished_days',
                    'type'
                )->get();
        });
    }

    /**
     * Get a specific flow for the authenticated user by ID.
     *
     * @param int $id
     * @return Flow|null
     */
    public function getUserFlow(int $id): ?Flow
    {
        $cacheKey = $this->getUserFlowCacheKey($id);

        return Cache::remember($cacheKey, $this->cacheTime, function () use ($id) {
            return $this->model->where('user_id', $this->user->id)
                ->where('id', $id)
                ->first();
        });
    }

    /**
     * Generate cache key for user flows.
     *
     * @return string
     */
    private function getUserFlowsCacheKey(): string
    {
        return "user_flows_{$this->user->id}";
    }

    /**
     * Delete the user flows cache key if it exists.
     *
     * @return bool True if the cache key was deleted, false otherwise.
     */
    public function deleteUserFlowsCacheKey(): bool
    {
        $cacheKey = $this->getUserFlowsCacheKey();
        return $this->deleteCacheKey($cacheKey);
    }

    /**
     * Generate cache key for a specific user flow.
     *
     * @param int $id
     * @return string
     */
    private function getUserFlowCacheKey(int $id): string
    {
        return "user_{$this->user->id}_flow_{$id}";
    }

    /**
     * Delete the user flow cache key if it exists.
     *
     * @param int $id
     * @return bool True if the cache key was deleted, false otherwise.
     */
    public function deleteUserFlowCacheKey(int $id): bool
    {
        $cacheKey = $this->getUserFlowCacheKey($id);
        return $this->deleteCacheKey($cacheKey);
    }

    /**
     * Delete a cache key if it exists.
     *
     * @param string $cacheKey
     * @return bool True if the cache key was deleted, false otherwise.
     */
    private function deleteCacheKey(string $cacheKey): bool
    {
        if (!Cache::has($cacheKey)) {
            return false;
        }

        Cache::forget($cacheKey);
        return true;
    }
}

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
     */
    public function getUserFlows(): ?Collection
    {
        $cacheKey = $this->getUserFlowsCacheKey();

        return Cache::remember($cacheKey, $this->cacheTime, function () {
            return $this->model->auth()
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
     */
    public function getUserFlow(int $id): ?Flow
    {
        $cacheKey = $this->getUserFlowCacheKey($id);

        return Cache::remember($cacheKey, $this->cacheTime, function () use ($id) {
            return $this->model->auth()
                ->where('id', $id)
                ->first();
        });
    }

    /**
     * Generate cache key for user flows.
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
     */
    private function getUserFlowCacheKey(int $id): string
    {
        return "user_{$this->user->id}_flow_{$id}";
    }

    /**
     * Delete the user flow cache key if it exists.
     *
     * @return bool True if the cache key was deleted, false otherwise.
     */
    public function deleteUserFlowCacheKey(int $id): bool
    {
        $cacheKey = $this->getUserFlowCacheKey($id);

        return $this->deleteCacheKey($cacheKey);
    }
}

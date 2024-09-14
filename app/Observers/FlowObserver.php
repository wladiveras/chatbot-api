<?php

namespace App\Observers;

use App\Models\Flow;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Repositories\Flow\FlowRepository;

class FlowObserver
{
    protected $flowRepository;

    public function __construct()
    {
        $this->flowRepository = App::make(FlowRepository::class);
    }

    /**
     * Handle the Flow "created" event.
     */
    public function created(Flow $flow): void
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => observer created', [
            'flow' => $flow,
        ]);

        $this->flowRepository->deleteUserFlowsCacheKey($flow->user_id);
    }

    /**
     * Handle the Flow "updated" event.
     */
    public function updated(Flow $flow): void
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => observer updated', [
            'flow' => $flow,
        ]);

        if ($flow === null) {
            return;
        }

        $this->flowRepository->deleteUserFlowsCacheKey($flow->user_id);
    }

    /**
     * Handle the Flow "deleted" event.
     */
    public function deleted(Flow $flow): void
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => observer deleted', [
            'flow' => $flow,
        ]);

        $this->flowRepository->deleteUserFlowsCacheKey($flow->user_id);
    }

    /**
     * Handle the Flow "restored" event.
     */
    public function restored(Flow $flow): void
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => observer restored', [
            'flow' => $flow,
        ]);

        $this->flowRepository->deleteUserFlowsCacheKey($flow->user_id);
    }

    /**
     * Handle the Flow "force deleted" event.
     */
    public function forceDeleted(Flow $flow): void
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => observer forceDeleted', [
            'flow' => $flow,
        ]);

        $this->flowRepository->deleteUserFlowsCacheKey($flow->user_id);
    }
}

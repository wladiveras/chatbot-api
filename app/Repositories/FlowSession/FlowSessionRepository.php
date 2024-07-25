<?php

namespace App\Repositories\FlowSession;

use App\Models\FlowSession;
use App\Repositories\BaseRepository;

class FlowSessionRepository extends BaseRepository implements FlowSessionRepositoryInterface
{
    public function __construct(FlowSession $model)
    {
        parent::__construct($model);
    }

    public function clientFlowSession(?int $flow_id, ?int $connection_id, ?string $session_key): ?FlowSession
    {
        $flowSession = $this->model->where('flow_id', $flow_id)->where('connection_id', $connection_id)->where('session_key', $session_key)->first();

        if (! $flowSession) {
            $flowSession = $this->model->create([
                'flow_id' => $flow_id,
                'connection_id' => $connection_id,
                'session_key' => $session_key,
                'step' => 1,
                'is_running' => 1,
                'last_active' => now(),
                'session_start' => now(),
            ]);
        }

        return $flowSession;
    }

    public function clientFinishSession(?int $flow_id, ?int $connection_id, ?string $session_key, ?string $last_step): ?FlowSession
    {
        $flowSession = $this->model->where('flow_id', $flow_id)->where('connection_id', $connection_id)->where('session_key', $session_key)->first();

        if ($flowSession) {
            $flowSession->step += 1;
            $flowSession->is_running = 0;
            $flowSession->last_active = now();
            if ($flowSession->step >= $last_step) {
                $flowSession->session_end = now();
            }
            $flowSession->save();
        }

        return $flowSession;

    }
}

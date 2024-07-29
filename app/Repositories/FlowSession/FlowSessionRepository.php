<?php

namespace App\Repositories\FlowSession;

use App\Models\FlowSession;
use App\Models\FlowSessionMetas;

use App\Repositories\BaseRepository;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;

class FlowSessionRepository extends BaseRepository implements FlowSessionRepositoryInterface
{
    private FlowSessionMetas $flowSessionMetas;

    public function __construct(FlowSession $model)
    {
        parent::__construct($model);
        $this->flowSessionMetas = App::make(FlowSessionMetas::class);
    }

    public function clientSession(?int $flow_id, ?int $connection_id, ?string $session_key): ?FlowSession
    {
        $flowSession = $this->findSession($flow_id, $connection_id, $session_key);

        if (!$flowSession) {
            $flowSession = $this->createSession($flow_id, $connection_id, $session_key);
        }

        return $flowSession;
    }

    public function setSessionMeta(int $flow_session_id, string $key, string $value, string $type = 'input'): void
    {
        $this->flowSessionMetas->create([
            'flow_session_id' => $flow_session_id,
            'key' => $key,
            'value' => $value,
            'type' => $type,
        ]);
    }

    public function getSessionMeta(?int $flow_session_id, ?string $key): ?FlowSessionMetas
    {
        return $this->flowSessionMetas->where('flow_session_id', $flow_session_id)
            ->where('key', $key)
            ->first();
    }

    private function createSession(?int $flow_id, ?int $connection_id, ?string $session_key): FlowSession
    {
        return $this->model->create([
            'flow_id' => $flow_id,
            'connection_id' => $connection_id,
            'session_key' => $session_key,
            'step' => 1,
            'is_running' => 0,
            'last_active' => now(),
            'session_start' => now(),
        ]);
    }

    public function updateSession(?int $flow_id, ?int $connection_id, ?string $session_key, ?string $last_step): ?FlowSession
    {
        $flowSession = $this->findSession($flow_id, $connection_id, $session_key);

        if ($flowSession) {
            $this->updateFlowSession($flowSession, $last_step);
        }

        return $flowSession;
    }

    private function findSession(?int $flow_id, ?int $connection_id, ?string $session_key): ?FlowSession
    {
        return $this->model->where('flow_id', $flow_id)
            ->where('connection_id', $connection_id)
            ->where('session_key', $session_key)
            ->first();
    }

    private function updateFlowSession(FlowSession $flowSession, ?string $last_step): void
    {
        $flowSession->step += 1;

        if ($flowSession->step > $last_step) {
            $flowSession->session_end = now();
        }

        $flowSession->last_active = now();
        $flowSession->save();
    }

}

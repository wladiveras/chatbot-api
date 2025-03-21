<?php

namespace App\Repositories\FlowSession;

use App\Models\FlowSession;
use App\Models\FlowSessionMetas;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\App;

class FlowSessionRepository extends BaseRepository implements FlowSessionRepositoryInterface
{
    private FlowSessionMetas $flowSessionMetas;

    public function __construct(FlowSession $model)
    {
        parent::__construct($model);
        $this->flowSessionMetas = App::make(FlowSessionMetas::class);
    }

    /**
     * Get or create a client session.
     */
    public function clientSession(?int $flow_id, ?int $connection_id, ?string $session_key): ?FlowSession
    {
        $flowSession = $this->findSession($flow_id, $connection_id, $session_key);

        if (!$flowSession) {
            $flowSession = $this->createSession($flow_id, $connection_id, $session_key);
        }

        return $flowSession;
    }

    /**
     * Set session meta data.
     */
    public function setSessionMeta(int $flow_session_id, string $key, string $value, string $type = 'input'): void
    {
        $this->flowSessionMetas->create([
            'flow_session_id' => $flow_session_id,
            'key' => $key,
            'value' => $value,
            'type' => $type,
        ]);
    }

    /**
     * Update the waiting client response status.
     */
    public function waitingClientResponse(?int $flow_id, ?int $connection_id, ?string $session_key, ?int $is_waiting): void
    {
        $flowSession = $this->findSession($flow_id, $connection_id, $session_key);

        if ($flowSession) {
            $this->setWaitingClientResponse($flowSession, $is_waiting);
        }
    }

    /**
     * Get session meta data.
     */
    public function getSessionMeta(?int $flow_session_id, ?string $key): ?FlowSessionMetas
    {
        return $this->flowSessionMetas->where('flow_session_id', $flow_session_id)
            ->where('key', $key)
            ->first();
    }

    /**
     * Move to the next session step.
     */
    public function nextSessionStep(?int $flow_id, ?int $connection_id, ?string $session_key, ?string $last_step): ?FlowSession
    {
        $flowSession = $this->findSession($flow_id, $connection_id, $session_key);

        if ($flowSession) {
            $this->setNextSessionStep($flowSession, $last_step);
        }

        return $flowSession;
    }

    /**
     * Fetch a client session.
     */
    public function fetchClientSession(?int $flow_id, ?int $connection_id, ?string $session_key, ?int $id = null): ?FlowSession
    {
        return $this->model->where('flow_id', $flow_id)
            ->where('connection_id', $connection_id)
            ->where('session_key', $session_key)
            ->when($id, function ($query) use ($id) {
                return $query->where('id', $id);
            })
            ->first();
    }

    /**
     * Reset flow sessions by flow ID.
     */
    public function resetFlowSession(?int $flow_id): ?bool
    {
        return $this->model->where('flow_id', $flow_id)->delete();
    }

    /**
     * Find a session by flow ID, connection ID, and session key.
     */
    private function findSession(?int $flow_id, ?int $connection_id, ?string $session_key): ?FlowSession
    {
        return $this->model->where('flow_id', $flow_id)
            ->where('connection_id', $connection_id)
            ->where('session_key', $session_key)
            ->first();
    }

    /**
     * Create a new session.
     */
    private function createSession(?int $flow_id, ?int $connection_id, ?string $session_key = null): FlowSession
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

    /**
     * Set the waiting client response status for a session.
     */
    private function setWaitingClientResponse(FlowSession $flowSession, ?int $is_waiting): void
    {
        $flowSession->is_waiting = $is_waiting;
        $flowSession->last_active = now();
        $flowSession->save();
    }

    /**
     * Move to the next step in the session.
     */
    private function setNextSessionStep(FlowSession $flowSession, ?string $last_step): void
    {
        $flowSession->step += 1;

        if ($flowSession->step > $last_step) {
            $flowSession->session_end = now();
        }

        $flowSession->last_active = now();
        $flowSession->save();
    }
}

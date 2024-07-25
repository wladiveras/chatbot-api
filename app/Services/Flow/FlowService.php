<?php

namespace App\Services\Flow;

use App\Jobs\ExecuteFlowJob;
use App\Repositories\Flow\FlowRepository;
use App\Repositories\FlowSession\FlowSessionRepository;
use App\Services\BaseService;
use App\Services\Messenger\MessengerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use stdClass;

class FlowService extends BaseService implements FlowServiceInterface
{
    private $flowRepository;

    private $flowSessionRepository;

    public $messengerService;

    public $session_key;

    public $total_steps;

    public $connection;

    public $session;

    public $data;

    public function __construct()
    {

        $this->flowRepository = App::make(FlowRepository::class);
        $this->flowSessionRepository = App::make(FlowSessionRepository::class);
        $this->messengerService = App::make(MessengerService::class);
    }

    public function parse(array $data): JsonResponse
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running');

        try {
            $createFlow = $this->flowRepository->create($data);

            if (!$createFlow) {
                return $this->error(
                    path: __CLASS__ . '.' . __FUNCTION__,
                    message: 'Não deu certo.',
                    code: 400
                );
            }

            return $this->success(
                message: 'Tudo certo, vamos continuar.',
                payload: $createFlow
            );

        } catch (\Exception $e) {
            return $this->error(
                path: __CLASS__ . '.' . __FUNCTION__,
                message: $e->getMessage(),
                code: $e->getCode()
            );
        }
    }

    public function userFlows(): ?stdClass
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running');

        try {

            $flows = (object) $this->flowRepository->userFlows();

            if (!$flows) {
                return $this->error(
                    path: __CLASS__ . '.' . __FUNCTION__,
                    message: 'Não deu certo, não foi possivel trazer os fluxos.',
                    code: 400
                );
            }

            return $this->success(
                message: 'Tudo certo, os fluxos foram retornados.',
                payload: $flows
            );

        } catch (\Exception $e) {
            return $this->error(
                path: __CLASS__ . '.' . __FUNCTION__,
                message: $e->getMessage(),
                code: $e->getCode()
            );
        }
    }

    public function create(array $data): ?stdClass
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running');

        try {
            $user = Auth::user();

            $payload = [
                'user_id' => $user->id,
                'name' => $data['name'],
                'description' => $data['description'],
                'payload' => json_encode($data['payload']),
                'commands' => json_encode($data['commands']),
            ];

            $flow = $this->flowRepository->create($payload);

            if (!$flow) {
                return $this->error(
                    path: __CLASS__ . '.' . __FUNCTION__,
                    message: 'Não deu certo, não foi possível criar um fluxo.',
                    code: 400
                );
            }

            return $this->success(
                message: 'Tudo certo, seu fluxo foi criado.',
                payload: $flow
            );

        } catch (\Exception $e) {
            return $this->error(
                path: __CLASS__ . '.' . __FUNCTION__,
                message: $e->getMessage(),
                code: $e->getCode()
            );
        }
    }

    // Commands

    public function connection($connection): self
    {
        $this->connection = $connection;

        return $this;
    }

    public function session($data): self
    {
        $session_key = Arr::get($data, 'data.key.remoteJid');
        $session_key = Str::before($session_key, '@');
        $this->session_key = $session_key;

        $session = $this->flowSessionRepository->clientFlowSession(
            flow_id: $this->connection->flow_id,
            connection_id: $this->connection->id,
            session_key: $this->session_key
        );

        $this->data = $data;
        $this->session = $session;

        return $this;
    }

    public function trigger()
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running');

        try {

            $flow = $this->flowRepository->find($this->connection->flow_id);
            $text = Arr::get($this->data, 'data.message.extendedTextMessage.text', Arr::get($this->data, 'data.message.conversation', 'Entendi...'));

            $commands = collect(json_decode($flow->commands, true));

            $step = $this->session->step;
            $this->total_steps = $commands->count();

            $nextCommands = $commands->filter(function ($command) use ($step) {
                return $command['step'] >= $step;
            });

            foreach ($nextCommands as $command) {
                if ($command['action'] === 'input' || $step >= $this->total_steps) {
                    Log::debug('Executing input command step: ' . $step);
                    break;
                }

                $delay = (int) Arr::get($command, 'value', 1);
                $jobs[] = (new ExecuteFlowJob($this->connection, $this->session_key, $text, $command, $this->total_steps))->delay($delay);
            }

            if (!empty($jobs)) {
                Bus::chain($jobs)->dispatch();
            }

        } catch (\Exception $exception) {
            return $this->error(
                path: __CLASS__ . '.' . __FUNCTION__,
                message: $exception->getMessage(),
                code: 400
            );
        }
    }
}

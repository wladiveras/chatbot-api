<?php

namespace App\Services\Flow;

use App\Jobs\ExecuteFlow;
use App\Jobs\RunningFlow;
use App\Models\Flow;
use App\Models\FlowSession;
use App\Repositories\Flow\FlowRepository;
use App\Repositories\FlowSession\FlowSessionRepository;
use App\Services\BaseService;
use App\Services\Messenger\MessengerService;
use Illuminate\Bus\Batch;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
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
    private $userInput = null;

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

            $flows = (object) $this->flowRepository->getUserFlows();

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

    public function delete(string|int $id): array|object
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running');

        try {

            $deleteFlow = $this->flowRepository->delete($id);


            if ($deleteFlow) {
                return $this->success(message: 'Fluxo deletado com sucesso.', payload: $deleteFlow);
            }

            return $this->error(
                path: __CLASS__ . '.' . __FUNCTION__,
                message: 'Não foi possível deletar esse fluxo.',
                code: 400
            );

        } catch (\Exception $exception) {
            return $this->error(
                path: __CLASS__ . '.' . __FUNCTION__,
                message: $exception->getMessage(),
                code: 400
            );
        }
    }

    public function fetchFlow($flow_id): ?stdClass
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running');

        try {

            $flow = $this->flowRepository->getUserFlow($flow_id);

            if (!$flow) {
                return $this->error(
                    path: __CLASS__ . '.' . __FUNCTION__,
                    message: 'Não deu certo, não foi possivel trazer o fluxo.',
                    code: 400
                );
            }

            return $this->success(
                message: 'Tudo certo, os fluxo foi retornado.',
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

    public function create(array $data): ?stdClass
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running');

        try {
            $user = auth()->user();
            $payload = $this->createPayload($data, $user->id);
            $flow = $this->createFlow($payload);

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
            Log::error('Error saving flow: ' . $e->getMessage(), [
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
                'data' => $payload,
                'flow' => $flow
            ]);
            return $this->error(
                path: __CLASS__ . '.' . __FUNCTION__,
                message: $e->getMessage(),
                code: $e->getCode()
            );
        }
    }

    public function update(int $id, array $data): ?stdClass
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running');

        try {
            $user = auth()->user();
            $payload = $this->createPayload($data, $user->id);

            $flow = $this->updateFlow($id, $payload);

            if (!$flow) {
                return $this->error(
                    path: __CLASS__ . '.' . __FUNCTION__,
                    message: 'Não deu certo, não foi possível atualizar o fluxo.',
                    code: 400
                );
            }

            return $this->success(
                message: 'Tudo certo, seu fluxo foi atualizado.',
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

    private function createPayload(array $data, int $userId): array
    {
        return [
            'user_id' => $userId,
            'name' => $data['name'],
            'description' => $data['description'],
            'node' => json_encode($data['node']),
            'edge' => json_encode($data['edge']),
            'commands' => json_encode($data['commands']),
        ];
    }
    private function createFlow(array $payload): ?Flow
    {
        return $this->flowRepository->create($payload);
    }

    private function updateFlow(int $id, array $payload): ?Flow
    {
        return $this->flowRepository->update($id, $payload);
    }

    public function connection($connection): self
    {
        $this->connection = $connection;

        return $this;
    }

    public function session($data): self
    {
        $this->session_key = $this->extractSessionKey($data);
        $this->session = $this->initializeSession();
        $this->data = $data;

        return $this;
    }

    private function extractSessionKey($data): string
    {
        $session_key = Arr::get($data, 'data.key.remoteJid');

        return Str::before($session_key, '@');
    }

    private function initializeSession(): FlowSession
    {
        return $this->flowSessionRepository->clientSession(
            flow_id: $this->connection->flow_id,
            connection_id: $this->connection->id,
            session_key: $this->session_key
        );
    }

    public function trigger()
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running');

        try {
            $flow = $this->getFlow();
            $text = $this->getText();
            $commands = $this->getCommands($flow);

            $step = $this->session->step;
            $this->total_steps = $commands->count();

            $nextCommands = $this->getNextCommands($commands, $step);

            if (!$this->session->is_running) {
                $this->session->is_running = true;
                $this->session->save();

                $jobs = $this->createJobs($nextCommands, $text, $step);

                if (!empty($jobs)) {
                    Bus::chain($jobs)
                        ->catch(function (\Throwable $e) {
                            Log::error('Batch failed: ' . $e->getMessage());
                        })
                        ->dispatch();
                }
            }

        } catch (\Exception $exception) {
            return $this->error(
                path: __CLASS__ . '.' . __FUNCTION__,
                message: $exception->getMessage(),
                code: 400
            );
        }
    }

    private function getFlow()
    {
        return $this->flowRepository->find($this->connection->flow_id);
    }

    private function getText()
    {
        return Arr::get($this->data, 'data.message.extendedTextMessage.text', Arr::get($this->data, 'data.message.conversation', 'undefined'));
    }

    private function getCommands($flow)
    {
        $commands = collect(json_decode($flow->commands, true));

        $commands = $commands->map(function ($command, $index) {
            $command['step'] = $index + 1;
            return $command;
        });

        $steps = $commands->count() + 1;

        $newCommand = [
            "name" => "finished",
            "type" => "Input",
            "label" => "Variável",
            "action" => "input",
            "nodeId" => "0",
            "step" => $steps
        ];

        $commands->push($newCommand);

        return $commands;
    }

    private function getNextCommands($commands, $step)
    {
        $filteredCommands = collect($commands)->filter(function ($command) use ($step) {
            return $command['step'] >= $step;
        })->values();

        $inputIndices = $filteredCommands->keys()->filter(function ($key) use ($filteredCommands) {
            return $filteredCommands[$key]['action'] === 'input';
        });

        if ($inputIndices->isEmpty()) {
            return $filteredCommands;
        }

        $nextInputIndex = $inputIndices->first(function ($index) use ($step, $filteredCommands) {
            return $filteredCommands[$index]['step'] > $step;
        });

        if ($nextInputIndex !== null && $nextInputIndex === 0) {
            $filteredCommands = $filteredCommands->slice($nextInputIndex)->values();
            return $filteredCommands;
        }

        if ($nextInputIndex !== false && $nextInputIndex !== $filteredCommands->count() - 1) {
            $filteredCommands = $filteredCommands->slice(0, $nextInputIndex + 1)->values();
            return $filteredCommands;
        }

        return $filteredCommands;
    }

    public function resetFlowSession($flow_id)
    {
        return $this->flowSessionRepository->resetFlowSession(
            flow_id: $flow_id,
        );
    }

    private function createJobs($nextCommands, $text, $step)
    {
        $jobs = [];

        foreach ($nextCommands as $command) {
            if ($step > $this->total_steps) {
                break;
            }

            if ($command['action'] === 'input') {
                $jobs[] = new ExecuteFlow([
                    'connection' => $this->connection,
                    'session' => $this->session,
                    'text' => $this->session->is_waiting ? $text : null,
                    'command' => $command,
                    'steps' => $step,
                ]);

                if (!$this->session->is_waiting) {
                    break;
                } else {
                    $text = null;
                }

            } else {
                $jobs[] = new ExecuteFlow([
                    'connection' => $this->connection,
                    'session' => $this->session,
                    'text' => $text,
                    'command' => $command,
                    'steps' => $step,
                ]);
            }
        }

        $jobs[] = new RunningFlow($this->session, 0);

        return $jobs;
    }
}

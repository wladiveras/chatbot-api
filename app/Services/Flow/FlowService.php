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
use Illuminate\Support\Facades\Storage;
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

        $data = $this->parsePayloadToS3($data);

        return [
            'user_id' => $userId,
            'name' => $data['name'],
            'description' => $data['description'],
            'node' => json_encode($data['node']),
            'edge' => json_encode($data['edge']),
            'commands' => json_encode($data['commands']),
        ];
    }


    private function parsePayloadToS3($payload)
    {
        // Decodificar o payload JSON
        $nodes = $payload['node'];

        foreach ($nodes as &$node) {
            foreach ($node['data']['commands'] as &$command) {
                if (isset($command['value'])) {
                    // Obter o valor base64 da imagem
                    $base64Image = $command['value'];

                    // Extrair o tipo MIME usando uma expressão regular
                    if (preg_match('/^data:(\w+\/\w+);base64,/', $base64Image, $matches)) {
                        $mimeType = $matches[1];

                        // Mapeamento de tipos MIME para extensões de arquivo
                        $mimeToExt = [
                            'image/jpeg' => 'jpg',
                            'image/png' => 'png',
                            'image/gif' => 'gif',
                            'video/mp4' => 'mp4',
                            'video/ogg' => 'ogv',
                            'video/webm' => 'webm',
                            'audio/mpeg' => 'mpeg',
                            'audio/mp3' => 'mp3',
                            // Adicione mais mapeamentos conforme necessário
                        ];

                        // Obter a extensão do arquivo a partir do tipo MIME
                        $extension = isset($mimeToExt[$mimeType]) ? $mimeToExt[$mimeType] : 'bin';

                        // Remover o prefixo 'data:image/jpeg;base64,' se existir
                        $base64Image = preg_replace('/^data:\w+\/\w+;base64,/', '', $base64Image);

                        // Decodificar a string base64
                        $imageData = base64_decode($base64Image);

                        // Gerar um UUID para o nome do arquivo
                        $uuid = Str::uuid()->toString();

                        // Definir o caminho no S3 com o UUID e a extensão dinâmica
                        $path = 'uploads/' . $uuid . '.' . $extension;

                        // Armazenar o arquivo no S3 com permissões públicas
                        $stored = Storage::disk('s3')->put($path, $imageData, 'public');

                        // Verificar se o arquivo foi armazenado corretamente
                        if ($stored) {
                            $url = Storage::disk('s3')->url($path);

                            // Substituir o valor base64 pelo URL do S3
                            $command['value'] = $url;
                        } else {
                            // Tratar o erro de armazenamento, se necessário

                        }
                    }
                }
            }
        }

        $payload['node'] = $nodes;

        return $payload;
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
                $jobs = $this->createJobs($nextCommands, $text, $step);

                if (!empty($jobs)) {
                    Bus::chain($jobs)
                        ->catch(function (Batch $batch, \Throwable $e) {
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
        return collect(json_decode($flow->commands, true));
    }

    private function getNextCommands($commands, $step)
    {
        // Filtrar comandos com step >= step fornecido
        Log::debug('testado 1: ', [$commands]);

        $filteredCommands = collect($commands)->filter(function ($command) use ($step) {
            return $command['step'] >= $step;
        });

        // Encontrar o primeiro comando com ação 'input'
        $inputCommand = $filteredCommands->first(function ($command) {
            return $command['action'] === 'input';
        });

        // Converter para valores
        $filteredCommands = $filteredCommands->values();

        // Encontrar o índice do comando 'input'
        $inputIndex = $filteredCommands->search($inputCommand);

        // Se o comando 'input' estiver no início, retornar todos os comandos a partir desse ponto
        if ($inputIndex === 0) {
            return $filteredCommands->slice($inputIndex)->values();
        }

        // Se o comando 'input' não estiver no início, retornar todos os comandos até o ponto do comando 'input'
        if ($inputIndex !== false) {
            return $filteredCommands->slice(0, $inputIndex)->values();
        }

        Log::debug('testado 2: ', [$filteredCommands]);


        // Se não houver comando 'input', retornar todos os comandos filtrados
        return $filteredCommands;


    }

    private function createJobs($nextCommands, $text, $step)
    {

        $jobs = [];
        $jobs[] = new RunningFlow($this->session, 1);

        foreach ($nextCommands as $command) {
            if ($step > $this->total_steps) {
                break;
            }

            $jobs[] = new ExecuteFlow([
                'connection' => $this->connection,
                'session' => $this->session,
                'text' => $text,
                'command' => $command,
                'steps' => $this->total_steps,
            ]);
        }

        $jobs[] = new RunningFlow($this->session, 0);

        return $jobs;
    }
}

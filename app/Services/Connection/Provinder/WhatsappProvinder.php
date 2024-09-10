<?php

namespace App\Services\Connection\Provinder;

use App\Repositories\Connection\ConnectionProfileRepository;
use App\Repositories\Connection\ConnectionRepository;
use App\Repositories\Message\MessageRepository;
use App\Services\BaseService;
use App\Services\Connection\ConnectionServiceInterface;
use App\Services\Flow\FlowService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class WhatsappProvinder extends BaseService implements ConnectionServiceInterface
{
    private mixed $url;

    private mixed $key;

    private mixed $request;

    private mixed $callback_url;

    private FlowService $flowService;

    private MessageRepository $messageRepository;

    private ConnectionRepository $connectionRepository;

    private ConnectionProfileRepository $connectionProfileRepository;

    public function __construct()
    {
        $this->url = Config::get('evolution.url');
        $this->key = Config::get('evolution.key');
        $this->callback_url = Config::get('app.url');

        $this->flowService = App::make(FlowService::class);

        $this->messageRepository = App::make(MessageRepository::class);
        $this->connectionRepository = App::make(ConnectionRepository::class);
        $this->connectionProfileRepository = App::make(ConnectionProfileRepository::class);

        $this->request = Http::withHeaders([
            'apikey' => $this->key,
        ])
            ->timeout(60)
            ->acceptJson();

    }

    public function createConnection(array|object $data): array|object
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running');

        $instanceName = Str::uuid()->toString();
        $token = Str::uuid()->toString();
        $number = Arr::get($data, 'connection_key');
        $webhook = "{$this->callback_url}/api/integration/whatsapp/callback";
        $events = ['MESSAGES_UPSERT', 'CONNECTION_UPDATE', 'CALL'];

        $payload = [
            'instanceName' => $instanceName,
            'token' => $token,
            'qrcode' => true,
            'number' => $number,
            'webhook' => $webhook,
            'webhook_by_events' => false,
            'events' => $events,
        ];

        try {
            if ($this->connectionRepository->exists(column: 'connection_key', value: $number)) {
                return $this->error(
                    path: __CLASS__ . '.' . __FUNCTION__,
                    message: 'Nao foi possivel criar uma nova conexão.',
                    code: 400
                );
            }

            if ($this->connectionRepository->exists(column: 'connection_key', value: $number)) {
                return $this->error(
                    path: __CLASS__ . '.' . __FUNCTION__,
                    message: 'Já existe uma conexão com esse número.',
                    code: 400
                );
            }

            $response = $this->request->post("{$this->url}/instance/create", $payload);

            if (!$response->successful()) {
                return $this->error(
                    path: __CLASS__ . '.' . __FUNCTION__,
                    message: $response,
                    code: 400
                );
            }

            $response = $response->json();

            $name = Arr::get($data, 'name', 'Conexão padrão');
            $description = Arr::get($data, 'description', "Nova conexão com o número $number.");

            $instance = Arr::get($response, 'instance.instanceName');
            $qrcodePairingCode = Arr::get($response, 'qrcode.pairingCode');
            $qrcodeBase64 = Arr::get($response, 'qrcode.base64');

            if ($this->connectionRepository->exists(column: 'token', value: $instance)) {
                return $this->error(
                    path: __CLASS__ . '.' . __FUNCTION__,
                    message: 'A conexão já existe, não foi possível criar uma nova.',
                    code: 400
                );
            }

            $user = auth()->user();

            $createConnection = $this->connectionRepository->create([
                'user_id' => $user->id,
                'name' => $name,
                'description' => $description,
                'type' => 'whatsapp',
                'connection_key' => $number,
                'token' => $instance,
                'country' => 'BR',
                'is_active' => 0,
                'payload' => json_encode($response),
            ]);

            $qrcode = [
                'qrcode' => [
                    'token' => $instance,
                    'pairingCode' => $qrcodePairingCode,
                    'base64' => $qrcodeBase64,
                ],
            ];

            $this->connectionRepository->deleteUserConnectionsCacheKey();

            $payload = (object) array_merge((array) $createConnection, $qrcode);

            return $this->success(message: 'Nova conexão criada com sucesso.', payload: $payload);
        } catch (\Exception $exception) {
            return $this->error(
                path: __CLASS__ . '.' . __FUNCTION__,
                message: $exception->getMessage(),
                code: 400
            );
        }
    }

    public function connect(int|string $connection): array|object
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running');

        try {
            if (!$this->isConnectionActive(connection: $connection, active: 0)) {
                return $this->error(
                    path: __CLASS__ . '.' . __FUNCTION__,
                    message: 'Não foi possivel retornar essa conexão.',
                    code: 400
                );
            }

            $response = $this->request->get("{$this->url}/instance/connect/{$connection}");

            if ($response->successful()) {
                return $this->success(message: 'Conexão retornada com sucesso.', payload: $response->json());
            }

            return $this->error(
                path: __CLASS__ . '.' . __FUNCTION__,
                message: 'Não foi possivel retornar essa conexão.',
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

    public function send(array|object $data): array|object
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running');

        $payload = $this->parse($data);

        $endpoint = match ($data['type']) {
            'text', 'link' => 'sendText',
            'audio' => 'sendWhatsAppAudio',
            'image' => 'sendMedia',
            'video' => 'sendMedia',
            'media_audio' => 'sendMedia',
            'list' => 'sendList',
            'pool' => 'sendPoll',
            'status' => 'sendStatus',
            default => throw new \Exception('Type not found'),
        };

        try {
            $connection = $this->connectionRepository->find(column: 'token', value: $data['connection']);
            $this->connectionExists($connection);

            $response = $this->request->post("{$this->url}/message/{$endpoint}/{$data['connection']}", $payload);

            if ($response->successful()) {
                $createMessage = $this->createMessage(
                    flowId: $connection->flow_id,
                    connectionId: $connection->id,
                    data: $data,
                    payload: $response->json(),
                    origin: 'system'
                );

                return $this->success(message: 'Mensagem enviada com sucesso.', payload: $createMessage);
            }

            return $this->error(
                path: __CLASS__ . '.' . __FUNCTION__,
                message: 'Não foi possível enviar a mensagem.',
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

    public function parse($data): array|object
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running');

        $options = [];
        $message = [];
        $delay = $data['delay'] ? ($data['delay'] * 1000) : 0;

        // Default options
        if ($data['type'] !== 'status') {
            $options = [
                'number' => $data['number'],
                'options' => [
                    'delay' => $delay,
                    'linkPreview' => true,
                    'presence' => $data['type'] === 'audio' ? 'recording' : 'composing',
                ],
            ];
        }

        // Media files
        if (in_array($data['type'], ['video', 'image', 'media_audio'])) {

            $message = [
                "number" => $data['number'],
                "mediatype" => $data['type'] === 'media_audio' ? 'audio' : $data['type'],
                "media" => $data['value'],
                "delay" => $delay,
                "caption" => $data['caption'],
            ];
        }

        // Text message and link
        if ($data['type'] === 'text' || $data['type'] === 'link') {
            $data['type'] = 'text';
            $message = [

                "number" => $data['number'],
                "text" => $data['value'],
                "delay" => $delay,
                "linkPreview" => true

            ];
        }

        // Recording audio message
        if ($data['type'] === 'audio') {
            $message = [
                'audioMessage' => [
                    "number" => $data['number'],
                    "encoding" => true,
                    "delay" > $delay,
                    'audio' => $data['value'],
                ],
            ];
        }

        // Merge data to send
        $message = array_merge($options, $message);

        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => parse data', [
            'type' => $data['type'],
            'message' => $message,
        ]);

        return $message;
    }

    public function getConnectionProfile(string|int $connection, $data): array|object
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running');

        try {
            if ($this->isConnectionActive(connection: $connection, active: 1)) {

                $response = $this->request->post("{$this->url}/chat/fetchProfile/{$connection}", [
                    'number' => $data['number'],
                ]);

                $connection = $this->connectionRepository->find(column: 'token', value: $connection);

                if ($response->successful()) {
                    $response = $response->json();
                    $number = Arr::get($response, 'wuid', null);
                    $user = auth()->user();

                    $payload = [
                        'connection_key' => $number ? explode('@', $number)[0] : null,
                        'user_id' => $user->id,
                        'connection_id' => $connection->id,
                        'name' => Arr::get($response, 'name', null),
                        'number_exists' => Arr::get($response, 'numberExists', null),
                        'picture' => Arr::get($response, 'picture', null),
                        'is_business' => Arr::get($response, 'isBusiness', null),
                        'email' => Arr::get($response, 'email', null),
                        'description' => Arr::get($response, 'description', null),
                        'website' => Arr::get($response, 'website', null),
                    ];

                    $this->connectionProfileRepository->createOrUpdateProfile($payload);

                    return $this->success(message: 'Perfil retornado com sucesso.', payload: $response);
                }
            }

            return $this->error(
                path: __CLASS__ . '.' . __FUNCTION__,
                message: 'Não foi possivel retornar essa conexão.',
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

    public function disconnect(string|int $connection): array|object
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running');

        try {

            if ($this->isConnectionActive(connection: $connection, active: 1)) {

                $response = $this->request->delete("{$this->url}/instance/logout/{$connection}");

                if ($response->successful()) {
                    $this->connectionRepository->update(column: 'token', value: $connection, data: ['is_active' => 0]);

                    return $this->success(message: 'Conexão desconectada com sucesso.', payload: $response->json());
                }
            }

            return $this->success(message: 'A conexão já está desconectada.', payload: []);

        } catch (\Exception $exception) {
            return $this->error(
                path: __CLASS__ . '.' . __FUNCTION__,
                message: $exception->getMessage(),
                code: 400
            );
        }
    }

    public function delete(string|int $connection): array|object
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running');

        try {
            $response = $this->request->delete("{$this->url}/instance/delete/{$connection}");

            $this->connectionRepository->delete(column: 'token', value: $connection);

            $this->connectionRepository->deleteUserConnectionsCacheKey();

            return $this->success(message: 'Conexão deletada com sucesso.', payload: $response->json());

        } catch (\Exception $exception) {
            return $this->error(
                path: __CLASS__ . '.' . __FUNCTION__,
                message: $exception->getMessage(),
                code: 400
            );
        }
    }

    public function callback(array|object $data): array|object
    {

        $event = Arr::get($data, 'data.event', Arr::get($data, 'event'));

        if ($event !== 'connection.update') {
            Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running');
        }

        return match ($event) {
            'connection.update' => $this->TriggerConnection($data),
            'messages.upsert' => $this->triggerFlow($data),
            default => [
                'data' => $data,
            ],
        };
    }

    public function triggerConnection(array|object $data): array|object
    {

        $connection = Arr::get($data, 'instance');
        $state = Arr::get($data, 'data.state', 'close');

        if ($this->connectionRepository->exists(column: 'token', value: $connection)) {
            if ($state === 'open') {
                $this->connectionRepository->update(column: 'token', value: $connection, data: ['is_active' => 1]);
            } else {
                $this->connectionRepository->update(column: 'token', value: $connection, data: ['is_active' => 0]);
            }

            return $this->success(message: 'Conexão Atualizada.', payload: $data);
        }

        return [];
    }

    public function triggerFlow(array|object $data): array|object
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running');

        try {
            $connection = Arr::get($data, 'instance');
            $FromOwner = Arr::get($data, 'data.key.fromMe');

            $connection = $this->connectionRepository->find(column: 'token', value: $connection);

            $this->connectionExists($connection);

            $origin = $FromOwner ? 'owner' : 'user';

            $createMessage = $this->createMessage(
                flowId: $connection->flow_id,
                connectionId: $connection->id,
                data: $data,
                origin: $origin,
                payload: $data,
            );

            if (!$FromOwner) {
                $this->flowService->connection($connection)->session($data)->trigger();
            }

            return $this->success(
                message: 'Fluxo disparado com sucesso.',
                payload: $createMessage
            );

        } catch (\Exception $exception) {
            return $this->error(
                path: __CLASS__ . '.' . __FUNCTION__,
                message: $exception->getMessage(),
                code: 400
            );
        }
    }

    private function createMessage(?int $flowId, ?int $connectionId, $data, $payload = [], $origin = 'system'): array|object
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running');

        if (empty($payload)) {
            $payload = $data;
        }

        $type = Arr::get($data, 'type', 'text');
        $text = Arr::get($data, 'data.message.extendedTextMessage.text', Arr::get($data, 'data.message.conversation', 'Entendi...'));

        $message = match ($origin) {
            'user', 'owner' => $text,
            default => Arr::get($data, 'value', ''),
        };

        $createMessage = $this->messageRepository->create([
            'flow_id' => $flowId,
            'connection_id' => $connectionId,
            'flow_session_id' => Arr::get($data, 'flow_session_id', null),
            'name' => Arr::get($data, 'data.pushName', $origin),
            'content' => $message,
            'type' => $type,
            'origin' => $origin,
            'payload' => json_encode($payload),
        ]);

        return $createMessage;
    }

    private function connectionExists($connection): array|object|bool
    {
        if (!$connection) {
            return $this->error(
                path: __CLASS__ . '.' . __FUNCTION__,
                message: 'Conexão não identificada.',
                code: 400
            );
        }

        if ($connection->is_active === 0) {
            return $this->error(
                path: __CLASS__ . '.' . __FUNCTION__,
                message: 'Conexão está inativa.',
                code: 400
            );
        }

        return true;
    }

    private function isConnectionActive(string|int $connection, $active = 0): bool
    {
        return $this->connectionRepository->exists(column: 'token', value: $connection) &&
            $this->connectionRepository->exists(column: 'is_active', value: $active);
    }
}

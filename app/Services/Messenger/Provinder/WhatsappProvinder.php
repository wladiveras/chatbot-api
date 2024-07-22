<?php

namespace App\Services\Messenger\Provinder;

use App\Repositories\Connection\ConnectionRepository;
use App\Repositories\Message\MessageRepository;
use App\Services\Messenger\MessengerServiceInterface;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class WhatsappProvinder implements MessengerServiceInterface
{
    private mixed $url;

    private mixed $key;

    private mixed $request;

    private mixed $callback_url;

    private MessageRepository $messageRepository;

    private ConnectionRepository $connectionRepository;

    public function __construct()
    {
        $this->url = Config::get('evolution.url');
        $this->key = Config::get('evolution.key');
        $this->callback_url = Config::get('app.url');

        $this->messageRepository = App::make(MessageRepository::class);
        $this->connectionRepository = App::make(ConnectionRepository::class);

        $this->request = Http::withHeaders([
            'apikey' => $this->key,
        ])
            ->timeout(60)
            ->acceptJson();

    }

    public function createConnection(array|object $data): array|object
    {
        $instanceName = Str::uuid()->toString();
        $token = Str::uuid()->toString();
        $number = Arr::get($data, 'connection_key');
        $webhook = "{$this->callback_url}/api/integration/whatsapp/callback";
        $events = ['MESSAGES_UPSERT', 'CONNECTION_UPDATE', 'CALL'];
        $name = Arr::get($data, 'name', 'Conexão padrão');
        $description = Arr::get($data, 'description', "Nova conexão com o número $number.");

        $payload = [
            'instanceName' => $instanceName,
            'token' => $token,
            'qrcode' => true,
            'number' => $number,
            'webhook' => $webhook,
            'webhook_by_events' => false,
            'events' => $events,
        ];

        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => start', [
            'data' => $data,
            'payload' => $payload,
        ]);

        try {
            if ($this->connectionRepository->exists(column: 'connection_key', value: $number)) {
                return $this->response(success: false, message: 'Já existe uma conexão com esse número.');
            }

            $response = $this->request->post("{$this->url}/instance/create", $payload);

            if (!$response->successful()) {
                return $this->response(success: false, message: 'Não foi possível criar uma nova conexão.');
            }

            $response = $response->json();
            $instance = Arr::get($response, 'instance.instanceName');
            $qrcodePairingCode = Arr::get($response, 'qrcode.pairingCode');
            $qrcodeBase64 = Arr::get($response, 'qrcode.base64');

            if ($this->connectionRepository->exists(column: 'token', value: $instance)) {
                return $this->response(success: false, message: 'Não foi possível criar uma nova conexão.');
            }

            $createConnection = $this->connectionRepository->create([
                'user_id' => 1,
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
                    'pairingCode' => $qrcodePairingCode,
                    'base64' => $qrcodeBase64,
                ],
            ];

            $payload = array_merge((array) $createConnection, $qrcode);

            return $this->response(success: true, message: 'Nova conexão criada com sucesso.', payload: $payload);
        } catch (\Exception $exception) {
            return $this->response(success: false, message: $exception->getMessage());
        }
    }

    public function connect(int|string $connection): array|object
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => start', [
            'connection' => $connection,
        ]);

        try {
            if (!$this->isConnectionActive(connection: $connection, active: 0)) {
                return $this->response(success: false, message: 'Não foi possivel retornar essa conexão.');
            }

            $response = $this->request->get("{$this->url}/instance/connect/{$connection}");

            if ($response->successful()) {
                return $this->response(success: true, message: 'Conexão retornada com sucesso.', payload: $response->json());
            }

            return $this->response(success: false, message: 'Não foi possivel retornar essa conexão.');

        } catch (\Exception $exception) {
            return $this->response(success: false, message: $exception->getMessage());
        }
    }

    public function send(array|object $data): array|object
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => start', [
            'data' => $data,
        ]);

        $payload = $this->parse($data);

        $endpoint = match ($data['type']) {
            'text' => 'sendText',
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
            $connection = $this->connectionRepository->first(column: 'token', value: $data['connection']);
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

                return $this->response(success: true, message: 'Mensagem enviada com sucesso.', payload: $createMessage);
            }

            return $this->response(success: false, message: 'Não foi possível enviar a mensagem.');

        } catch (\Exception $exception) {
            return $this->response(success: false, message: $exception->getMessage());
        }
    }

    public function parse($data): array|object
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => start', [
            'data' => $data,
        ]);

        $options = [];
        $message = [];

        // Default options
        if ($data['type'] !== 'status') {
            $options = [
                'number' => $data['number'],
                'options' => [
                    'delay' => $data['delay'] ?? 1200,
                    'presence' => $data['type'] === 'audio' ? 'recording' : 'composing',
                ],
            ];
        }

        // Media files
        if ($data['type'] === 'video' || $data['type'] === 'image' || $data['type'] === 'media_audio') {
            $message = [
                'mediaMessage' => [
                    'mediatype' => $data['type'] === 'media_audio' ? 'audio' : $data['type'],
                    'caption' => $data['caption'],
                    'media' => $data['file_url'],
                ],
            ];
        }

        // A list message
        if ($data['type'] === 'list') {
            $message = [
                'listMessage' => [
                    'title' => 'Title',
                    'description' => 'Description',
                    'buttonText' => 'Button',
                    'footerText' => 'Footer',
                    'sections' => [
                        [
                            'rows' => [
                                [
                                    'title' => 'row title',
                                    'description' => 'row description',
                                    'rowId' => '21515020',
                                ],
                            ],
                            'title' => 'Section Title',
                        ],
                    ],
                ],
            ];
        }

        // Post a status
        if ($data['type'] === 'status') {
            $message = [
                'statusMessage' => [
                    'type' => 'text',
                    'content' => 'Hi, how are you 2today?',
                    'backgroundColor' => '#008000',
                    'font' => 1,
                    'allContacts' => true,
                    'statusJidList' => [
                        '5521969098986@s.whatsapp.net',
                    ],
                ],
            ];
        }

        // Create a quiz message
        if ($data['type'] === 'pool') {
            $message = [
                'pollMessage' => [
                    'name' => 'Title',
                    'selectableCount' => 2,
                    'values' => [
                        'option 1',
                        'option 2',
                        'option 3',
                    ],
                ],
            ];
        }

        // Texto message
        if ($data['type'] === 'text') {
            $message = [
                'textMessage' => [
                    'text' => $data['message'],
                ],
            ];
        }

        // Recording audio message
        if ($data['type'] === 'audio') {
            $message = [
                'audioMessage' => [
                    'audio' => $data['file_url'],
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

    public function fetch(string|int $connection): array|object
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => start', [
            'connection' => $connection,
        ]);

        // Esse fetch vai trazer todas as conexoes vinculada ao usuario
        return (object) [
            'connection' => $connection,
        ];
    }

    public function status(string|int $connection): array|object
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => start', [
            'connection' => $connection,
        ]);

        try {
            if ($this->isConnectionActive(connection: $connection, active: 0)) {

                $response = $this->request->delete("{$this->url}/instance/connectionState/{$connection}");

                if ($response->successful()) {
                    return $this->response(success: true, message: 'Status da conexão retornado com sucesso.', payload: $response->json());
                }
            }

            return $this->response(success: false, message: 'Não foi possivel retornar essa conexão.');

        } catch (\Exception $exception) {
            return $this->response(success: false, message: $exception->getMessage());
        }
    }

    public function disconnect(string|int $connection): array|object
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => start', [
            'connection' => $connection,
        ]);

        try {
            if ($this->isConnectionActive(connection: $connection, active: 1)) {

                $response = $this->request->delete("{$this->url}/instance/logout/{$connection}");

                if ($response->successful()) {
                    $this->connectionRepository->update(column: 'token', value: $connection, data: ['is_active' => 0]);

                    return $this->response(success: true, message: 'Conexão desconectada com sucesso.', payload: $response->json());
                }
            }

            return $this->response(success: false, message: 'Não foi possivel desconectar essa conexão.');

        } catch (\Exception $exception) {
            return $this->response(success: false, message: $exception->getMessage());
        }
    }

    public function delete(string|int $connection): array|object
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => start', [
            'connection' => $connection,
        ]);

        try {
            if ($this->isConnectionActive(connection: $connection, active: 0)) {
                $response = $this->request->delete("{$this->url}/instance/delete/{$connection}");

                if ($response->successful()) {
                    $this->connectionRepository->delete(column: 'token', value: $connection);

                    return $this->response(success: true, message: 'Conexão desconectada com sucesso.', payload: $response->json());
                }
            }

            return $this->response(success: false, message: 'Não foi possível desconectar essa conexão.');

        } catch (\Exception $exception) {
            return $this->response(success: false, message: $exception->getMessage());
        }
    }

    public function callback(array|object $data): array|object
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => start', [
            'data' => $data,
        ]);

        $event = Arr::get($data, 'data.event', Arr::get($data, 'event'));

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
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => CONNECTION_UPDATE TRIGGER', [
            'data' => $data,
        ]);

        $connection = Arr::get($data, 'instance');
        $state = Arr::get($data, 'data.state', 'close');

        if ($this->connectionRepository->exists(column: 'token', value: $connection)) {
            if ($state === 'open') {
                $this->connectionRepository->update(column: 'token', value: $connection, data: ['is_active' => 1]);
            } else {
                $this->connectionRepository->update(column: 'token', value: $connection, data: ['is_active' => 0]);
            }

            return $this->response(success: true, message: 'Conexão Atualizada.', payload: $data);
        }

        return $this->response(success: false, message: 'Conexão não foi possivel atualizar a conexão.');
    }

    public function triggerFlow(array|object $data): array|object
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => MESSAGES_UPSERT', [
            'data' => $data,
        ]);

        $connection = Arr::get($data, 'instance');
        $FromOwner = Arr::get($data, 'data.key.fromMe');

        if ($FromOwner) {
            return $this->response(success: false, message: 'Não foi possível disparar o fluxo do mesmo numero de telefone da conexão.');
        }

        try {
            $connection = $this->connectionRepository->first(column: 'token', value: $connection);
            $this->connectionExists($connection);

            $createMessage = $this->createMessage(
                flowId: $connection->flow_id,
                connectionId: $connection->id,
                data: $data,
                origin: 'client'
            );

            return $this->response(success: true, message: 'Fluxo disparado com sucesso.', payload: $createMessage);

        } catch (\Exception $exception) {
            return $this->response(success: false, message: $exception->getMessage());
        }
    }

    private function createMessage(string|int|null $flowId, string|int|null $connectionId, $data, $payload = [], $origin = 'system'): array|object
    {
        if ($payload) {
            $payload = $data;
        }
        if ($data['type'] === 'text') {
            $message = $data['message'];
        } else {
            $message = $data['file_url'];
        }

        $createMessage = $this->messageRepository->create([
            'flow_id' => $flowId ?? null,
            'connection_id' => $connectionId ?? null,
            'flow_session_id' => Arr::get($data, 'flow_session_id', null),
            'content' => $message,
            'type' => Arr::get($data, 'type', 'text'),
            'origin' => $origin,
            'payload' => json_encode($payload),
        ]);

        return $createMessage;
    }

    private function connectionExists($connection): array|object|bool
    {
        if (!$connection) {
            return $this->response(success: false, message: 'Conexão não identificada.');
        }

        if ($connection->is_active === 0) {
            return $this->response(success: false, message: 'Conexão está inativa.');
        }

        return true;
    }

    private function isConnectionActive(string|int $connection, $active = 0): bool
    {
        return $this->connectionRepository->exists(column: 'token', value: $connection) &&
            $this->connectionRepository->exists(column: 'is_active', value: $active);
    }

    private function response(bool $success, string $message, mixed $payload = []): object
    {
        if ($success === false) {
            throw new \Exception($message);
        }

        return (object) [
            'success' => $success,
            'message' => $message,
            'payload' => $payload,
        ];
    }
}

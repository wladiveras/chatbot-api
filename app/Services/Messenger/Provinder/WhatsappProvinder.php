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

    private $connectionExists;

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
        $events = ['QRCODE_UPDATED', 'MESSAGES_UPSERT', 'CONNECTION_UPDATE', 'CALL'];
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
            if (!$this->connectionRepository->exists(column: 'connection_key', value: $number)) {
                $response = $this->request->post("{$this->url}/instance/create", $payload);

                if ($response->successful()) {
                    $response = $response->json();
                    $instance = Arr::get($response, 'instance.instanceName');

                    if (!$this->connectionRepository->exists(column: 'token', value: $instance)) {

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

                        return (object) [
                            'data' => [
                                'success' => true,
                                'message' => 'Nova conexão criada com sucesso.',
                                'payload' => $createConnection,
                            ],
                        ];
                    }
                }
            }

            return (object) [
                'data' => [
                    'success' => false,
                    'message' => 'Não foi possível criar uma nova conexão.',
                ],
            ];
        } catch (\Exception $exception) {
            return (object) [
                'data' => [
                    'success' => false,
                    'message' => $exception->getMessage(),
                ],
            ];
        }
    }

    public function connect(int|string $connection): array|object
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => start', [
            'connection' => $connection,
        ]);

        try {
            if (
                $this->connectionRepository->exists(column: 'token', value: $connection) &&
                $this->connectionRepository->exists(column: 'is_active', value: 0)
            ) {
                $response = $this->request->get("{$this->url}/instance/connect/{$connection}");

                if ($response->successful()) {

                    $response = $response->json();

                    return (object) [
                        'data' => [
                            'success' => true,
                            'message' => 'Conexão retornada com sucesso.',
                            'payload' => (object) $response,
                        ],
                    ];
                }
            }

            return (object) [
                'data' => [
                    'success' => false,
                    'message' => 'Não foi possivel retornar essa conexão.',
                ],
            ];

        } catch (\Exception $exception) {
            return (object) [
                'data' => [
                    'success' => false,
                    'message' => $exception->getMessage(),
                ],
            ];
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
            'image', 'video',
            'media_audio' => 'sendMedia',
            default => throw new \Exception('Type not found'),
        };

        try {
            $connection = $this->connectionRepository->first(column: 'token', value: $data['connection']);

            if (!$connection) {
                return (object) [
                    'data' => [
                        'success' => false,
                        'message' => 'Não foi possível enviar a mensagem.',
                    ],
                ];
            }

            if ($connection->is_active === 0) {
                return (object) [
                    'data' => [
                        'success' => false,
                        'message' => 'Conexão inativa.',
                    ],
                ];
            }

            $response = $this->request->post("{$this->url}/message/{$endpoint}/{$data['connection']}", $payload);

            if ($response->successful()) {
                $response = $response->json();

                $createMessage = $this->messageRepository->create([
                    'flow_id' => $connection->flow_id ?? null,
                    'flow_session_id' => Arr::get($data, 'flow_session_id', null),
                    'content' => Arr::get($data, 'message', 'Entendi...'),
                    'type' => Arr::get($data, 'type', 'text'),
                    'origin' => 'system',
                    'payload' => json_encode($payload),
                ]);

                return (object) [
                    'data' => [
                        'success' => true,
                        'message' => 'Mensagem enviada com sucesso.',
                        'payload' => (object) $createMessage,
                    ],
                ];
            }

            return (object) [
                'data' => [
                    'success' => false,
                    'message' => 'Não foi possível enviar a mensagem.',
                ],
            ];

        } catch (\Exception $exception) {
            return (object) [
                'data' => [
                    'success' => false,
                    'message' => $exception->getMessage(),
                ],
            ];
        }
    }

    public function parse($data): array|object
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => start', [
            'data' => $data,
        ]);

        $options = [
            'number' => $data['number'],
            'options' => [
                'delay' => $data['delay'] ?? 1200,
                'presence' => $data['type'] === 'audio' ? 'recording' : 'composing',
            ],
        ];

        $mediaMessage = [];
        $textMessage = [];
        $audioMessage = [];

        if ($data['type'] === 'video' || $data['type'] === 'image' || $data['type'] === 'media_audio') {
            $mediaMessage = [
                'mediaMessage' => [
                    'mediatype' => $data['type'] === 'media_audio' ? 'audio' : $data['type'],
                    'caption' => $data['caption'],
                    'media' => $data['file_url'],
                ],
            ];
        }

        if ($data['type'] === 'text') {
            $textMessage = [
                'textMessage' => [
                    'text' => $data['message'],
                ],
            ];
        }

        if ($data['type'] === 'audio') {
            $audioMessage = [
                'audioMessage' => [
                    'audio' => $data['file_url'],
                ],
            ];
        }

        $message = array_merge($options, $mediaMessage, $textMessage, $audioMessage);

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
            if (
                $this->connectionRepository->exists(column: 'token', value: $connection) &&
                $this->connectionRepository->exists(column: 'is_active', value: 1)
            ) {

                $response = $this->request->delete("{$this->url}/instance/connectionState/{$connection}");

                if ($response->successful()) {

                    $response = $response->json();

                    return (object) [
                        'data' => [
                            'success' => true,
                            'message' => 'Status da conexão retornado com sucesso.',
                            'payload' => (object) $response,
                        ],
                    ];
                }
            }

            return (object) [
                'data' => [
                    'success' => false,
                    'message' => 'Não foi possivel retornar essa conexão.',
                ],
            ];

        } catch (\Exception $exception) {
            return (object) [
                'data' => [
                    'success' => false,
                    'message' => $exception->getMessage(),
                ],
            ];
        }
    }

    public function disconnect(string|int $connection): array|object
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => start', [
            'connection' => $connection,
        ]);

        try {
            if (
                $this->connectionRepository->exists(column: 'token', value: $connection) &&
                $this->connectionRepository->exists(column: 'is_active', value: 1)
            ) {

                $response = $this->request->delete("{$this->url}/instance/logout/{$connection}");

                if ($response->successful()) {

                    $response = $response->json();
                    $this->connectionRepository->update(column: 'token', value: $connection, data: ['is_active' => 0]);

                    return (object) [
                        'data' => [
                            'success' => true,
                            'message' => 'Conexão desconectada com sucesso.',
                            'payload' => (object) $response,
                        ],
                    ];
                }
            }

            return (object) [
                'data' => [
                    'success' => false,
                    'message' => 'Não foi possivel desconectar essa conexão.',
                ],
            ];

        } catch (\Exception $exception) {
            return (object) [
                'data' => [
                    'success' => false,
                    'message' => $exception->getMessage(),
                ],
            ];
        }
    }

    public function delete(string|int $connection): array|object
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => start', [
            'connection' => $connection,
        ]);

        try {
            if (
                $this->connectionRepository->exists(column: 'token', value: $connection) &&
                $this->connectionRepository->exists(column: 'is_active', value: 0)
            ) {
                $response = $this->request->delete("{$this->url}/instance/delete/{$connection}");

                if ($response->successful()) {

                    $response = $response->json();
                    $this->connectionRepository->delete(column: 'token', value: $connection);

                    return (object) [
                        'data' => [
                            'success' => true,
                            'message' => 'Conexão desconectada com sucesso.',
                            'payload' => (object) $response,
                        ],
                    ];
                }
            }

            return (object) [
                'data' => [
                    'success' => false,
                    'message' => 'Não foi possivel desconectar essa conexão.',
                ],
            ];

        } catch (\Exception $exception) {
            return (object) [
                'data' => [
                    'success' => false,
                    'message' => $exception->getMessage(),
                ],
            ];
        }
    }

    // Function to handle the webhook
    public function callback(array|object $data): array|object
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => start', [
            'data' => $data,
        ]);

        $event = Arr::get($data, 'data.event', Arr::get($data, 'event'));

        return match ($event) {
            'connection.update' => $this->TriggerConnection($data),
            'qrcode.updated' => $this->triggerQrcode($data),
            'messages.upsert' => $this->triggerFlow($data),
            default => [
                'data' => $data,
            ],
        };
    }

    public function TriggerConnection(array|object $data): array|object
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => CONNECTION_UPDATE TRIGGER', [
            'data' => $data,
        ]);

        $instance = Arr::get($data, 'instance');
        $state = Arr::get($data, 'data.state', 'close');

        match ($state) {
            'open' => $this->connectionRepository->update(column: 'token', value: $instance, data: ['is_active' => 1]),
            default => $this->connectionRepository->update(column: 'token', value: $instance, data: ['is_active' => 0]),
        };

        return [
            'data' => [
                'success' => true,
                'message' => 'CONNECTION_UPDATE',
                'payload' => $data,
            ],
        ];
    }

    public function triggerQrcode(array|object $data): array|object
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => QRCODE_UPDATED TRIGGER', [
            'data' => $data,
        ]);

        $data = (object) Arr::get($data, 'data');

        return [
            'data' => [
                'success' => true,
                'message' => 'QRCODE_UPDATED',
                'payload' => $data,
            ],
        ];
    }

    public function triggerFlow(array|object $data): array|object
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => MESSAGES_UPSERT', [
            'data' => $data,
        ]);

        $connection = Arr::get($data, 'instance');
        $fromConnectionNumber = Arr::get($data, 'data.key.fromMe');

        if ($fromConnectionNumber) {
            return (object) [
                'data' => [
                    'success' => false,
                    'message' => 'Não foi possível enviar a mensagem.',
                ],
            ];
        }

        try {
            $connection = $this->connectionRepository->first(column: 'token', value: $connection);

            if (!$connection) {
                return (object) [
                    'data' => [
                        'success' => false,
                        'message' => 'Não foi possivel identificar a conexão.',
                    ],
                ];
            }

            if ($connection->is_active === 0) {
                return (object) [
                    'data' => [
                        'success' => false,
                        'message' => 'Conexão inativa.',
                    ],
                ];
            }

            // Chama o serviço de fluxo para processar a mensagem e enviar para o cliente

            // Criar uma funçao depois para criar essa mensagem no banco de dados
            $createMessage = $this->messageRepository->create([
                'flow_id' => $connection->flow_id ?? null,
                'flow_session_id' => Arr::get($data, 'flow_session_id', null),
                'content' => Arr::get($data, 'message', 'Entendi...'),
                'type' => Arr::get($data, 'type', 'text'),
                'origin' => 'user',
                'payload' => json_encode($data),
            ]);

            return (object) [
                'data' => [
                    'success' => true,
                    'message' => 'Mensagem recebida com sucesso.',
                    'payload' => (object) $createMessage,
                ],
            ];

        } catch (\Exception $exception) {
            return (object) [
                'data' => [
                    'success' => false,
                    'message' => $exception->getMessage(),
                ],
            ];
        }
    }
}

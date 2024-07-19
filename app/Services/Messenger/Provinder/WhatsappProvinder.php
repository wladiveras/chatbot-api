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
        $events = ['QRCODE_UPDATED', 'MESSAGES_UPSERT'];
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

        Log::debug(__CLASS__.'.'.__FUNCTION__.' => start', [
            'data' => $data,
            'payload' => $payload,
        ]);

        try {
            if (! $this->connectionRepository->exists(column: 'connection_key', value: $number)) {
                $response = $this->request->post("{$this->url}/instance/create", $payload);

                if ($response->successful()) {
                    $response = $response->json();
                    $instance = Arr::get($response, 'instance.instanceName');

                    if (! $this->connectionRepository->exists(column: 'token', value: $instance)) {
                        $createConnection = $this->connectionRepository->create([
                            'user_id' => rand(1, 5),
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
            throw new \Exception($exception->getMessage());
        }
    }

    public function connect(int|string $connection): array|object
    {
        Log::debug(__CLASS__.'.'.__FUNCTION__.' => start', [
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
            throw new \Exception($exception->getMessage());
        }
    }

    public function send(array|object $data): array|object
    {
        Log::debug(__CLASS__.'.'.__FUNCTION__.' => start', [
            'data' => $data,
        ]);

        $payload = $this->parse($data);

        $endpoint = match ($data['type']) {
            'text' => 'sendText',
            'audio' => 'sendWhatsAppAudio',
            'image' => 'sendMedia',
            'video' => 'sendMedia',
            'media_audio' => 'sendMedia',
            default => throw new \Exception('Type not found'),
        };

        $response = $this->request->post("{$this->url}/message/{$endpoint}/{$data['connection']}", $payload);

        $connection = $this->connectionRepository->exists(column: 'is_active', value: 0);

        $createMessage = $this->messageRepository->create([
            'user_id' => rand(1, 5),

            'is_active' => 0,
            'payload' => json_encode($response),
        ]);

        return (object) [
            'data' => $response->json() ?? [],
        ];
    }

    public function parse($data): array|object
    {
        Log::debug(__CLASS__.'.'.__FUNCTION__.' => start', [
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

        Log::debug(__CLASS__.'.'.__FUNCTION__.' => parse data', [
            'type' => $data['type'],
            'message' => $message,
        ]);

        return $message;
    }

    public function fetch(string|int $connection): array|object
    {
        Log::debug(__CLASS__.'.'.__FUNCTION__.' => start', [
            'connection' => $connection,
        ]);

        // Esse fetch vai trazer todas as conexoes vinculada ao usuario
        return (object) [
            'connection' => $connection,
        ];
    }

    public function status(string|int $connection): array|object
    {
        Log::debug(__CLASS__.'.'.__FUNCTION__.' => start', [
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
            throw new \Exception($exception->getMessage());
        }
    }

    public function disconnect(string|int $connection): array|object
    {
        Log::debug(__CLASS__.'.'.__FUNCTION__.' => start', [
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
            throw new \Exception($exception->getMessage());
        }
    }

    public function delete(string|int $connection): array|object
    {
        Log::debug(__CLASS__.'.'.__FUNCTION__.' => start', [
            'connection' => $connection,
        ]);

        try {
            if (
                $this->connectionRepository->exists(column: 'token', value: $connection) &&
                $this->connectionRepository->exists(column: 'is_active', value: 1)
            ) {
                $response = $this->request->delete("{$this->url}/instance/delete/{$connection}");

                if ($response->successful()) {

                    $response = $response->json();

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
            throw new \Exception($exception->getMessage());
        }
    }

    // Function to handle the webhook
    public function callback(array|object $data): array|object
    {
        Log::debug(__CLASS__.'.'.__FUNCTION__.' => start', [
            'data' => $data,
        ]);

        $event = Arr::get($data, 'data.event', false);

        match ($event) {
            'qrcode.updated' => $this->triggerQrcode($data),
            'messages.upsert' => $this->triggerFlow($data),
            default => false,
        };

        return (object) [
            'data' => $data,
        ];
    }

    public function triggerQrcode(array|object $data): array|object
    {
        Log::debug(__CLASS__.'.'.__FUNCTION__.' => QRCODE_UPDATED TRIGGER', [
            'data' => $data,
        ]);
        // atualiza status da conexao is_active com base no callback caso o usuario desconecte pelo telefone.

        return [
            'data' => $data,
        ];
    }

    public function triggerFlow(array|object $data): array|object
    {
        Log::debug(__CLASS__.'.'.__FUNCTION__.' => MESSAGES_UPSERT', [
            'data' => $data,
        ]);

        // Recebe a mensagem enviada pelo usuario

        return [
            'data' => $data,
        ];
    }
}

<?php
namespace App\Services\Messenger\Provinder;

use App\Services\Messenger\MessengerServiceInterface;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Enums\MessagesType;

class WhatsappProvinder implements MessengerServiceInterface
{
    private mixed $url;
    private mixed $key;
    private mixed $request;
    private mixed $callback_url;

    public function __construct()
    {
        $this->url = Config::get('evolution.url');
        $this->key = Config::get('evolution.key');
        $this->callback_url = Config::get('app.url');

        $this->request = Http::withHeaders([
            'apikey' => $this->key,
        ])
        ->acceptJson();
    }

    public function createConnection(array|object $data): array|object
    {
        $payload = [
            "instanceName" => Str::uuid()->toString(),
            "token" => Str::uuid()->toString(),
            "qrcode" => true,
            "number" => $data['connection_key'],
            "webhook" => "{$this->callback_url}/api/integration/whatsapp/callback",
            "webhook_by_events" => false,
            "events" => [
                "QRCODE_UPDATED",
                "MESSAGES_UPSERT",
            ]
        ];

        Log::debug(__CLASS__.'.'.__FUNCTION__." => start", [
            'data' => $data,
            'payload' => $payload,
        ]);

        try {
            $response = $this->request->post("{$this->url}/instance/create", $payload);

            return (object) [
                'data' => $response->json(),
            ];

        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }
    }

    public function send(array|object $data): array|object
    {
        Log::debug(__CLASS__.'.'.__FUNCTION__." => start", [
            'data' => $data,
        ]);

        $payload = $this->parse($data);

        $endpoint = match ($data['type']) {
            'text' => 'sendText',
            'audio' => 'sendWhatsAppAudio',
            'image' => 'sendMedia',
            'video' => 'sendMedia',
            'media_audio' => 'sendMedia',
            'sticker' => 'sendSticker',
            default => throw new \Exception("Type not found"),
        };

        $response = $this->request->post("{$this->url}/message/{$endpoint}/{$data['connection']}", $payload);

        return (object) [
            'data' => $response->json() ?? [],
        ];
    }

    public function parse($data): array|object
    {
        $options = [
            "number" => $data['number'],
            'options' => [
                "delay" => $data['delay'] ?? 1200,
                "presence" => $data['type'] === 'audio' ? "recording" : "composing",
            ]
        ];

        $mediaMessage = [];
        $textMessage = [];
        $audioMessage = [];

        if ($data['type'] === 'video' || $data['type'] === 'image' || $data['type'] === 'media_audio') {
            $mediaMessage = [
                "mediaMessage" => [
                    "mediatype" => $data['type'] === 'media_audio' ? 'audio' : $data['type'],
                    "caption" => $data['caption'],
                    "media" => $data['file_url']
                ]
            ];
        }

        if ($data['type'] === 'text') {
            $textMessage = [
                "textMessage" => [
                    "text" => $data['message'],
                ]
            ];
        }

        if ($data['type'] === 'audio') {
            $audioMessage = [
                "audioMessage" => [
                    "audio" => $data['file_url'],
                ]
            ];
        }

        $message = array_merge($options, $mediaMessage, $textMessage, $audioMessage);

        Log::debug(__CLASS__.'.'.__FUNCTION__." => parse data", [
            'type' => $data['type'],
            'message' => $message,
        ]);

        return $message;
    }

    public function connect(int|string $connection): array|object
    {
        $response = $this->request->get("{$this->url}/instance/connect/{$connection}");

        return (object) [
            'data' => $response->json() ?? [],
        ];
    }
    public function fetch(string|int $connection): array|object
    {
        // Esse fetch vai trazer todas as conexoes vinculada ao usuario
        return (object) [
            'connection' => $connection,
        ];
    }
    public function status(string|int $connection): array|object
    {
        $response = $this->request->delete("{$this->url}/instance/connectionState/{$connection}");

        return (object) [
            'data' => $response->json() ?? [],
        ];
    }
    public function disconnect(string|int $connection): array|object
    {
        $response = $this->request->delete("{$this->url}/instance/logout/{$connection}");

        return (object) [
            'data' => $response->json() ?? [],
        ];
    }

    public function delete(string|int $connection): array|object
    {
        $response = $this->request->delete("{$this->url}/instance/delete/{$connection}");

        return (object) [
            'data' => $response->json() ?? [],
        ];
    }

    // Function to handle the webhook
    public function callback(array|object $data): array|object
    {
        Log::debug(__CLASS__.'.'.__FUNCTION__." => start", [
            'data' => $data,
        ]);

        return (object) [
            'data' => $data,
        ];
    }


}



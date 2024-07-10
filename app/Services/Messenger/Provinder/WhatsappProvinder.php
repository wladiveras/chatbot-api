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

    public function __construct()
    {
        $this->url = Config::get('evolution.url');
        $this->key = Config::get('evolution.key');

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
            "webhook" => "http://laravel.test/api/integration/whatsapp/callback",
            "webhook_by_events" => false,
            "events" => [
                "QRCODE_UPDATED",
                "MESSAGES_UPSERT",
                "CONNECTION_UPDATE",
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

        if($data['type'] === 'text'){
            $response = $this->request->post("{$this->url}/message/sendText/{$data['connection']}", $payload);
        }

        return (object) [
            'data' => $response->json() ?? [],
        ];
    }

    public function parse($data): array|object
    {
        return match ($data['type']) {
            'text' => $this->parseTextMessage($data),
            default => throw new \InvalidArgumentException('Type of message not found.'),
        };
    }

    public function connect(int|string $connection): array|object
    {
        return (object) [
            'connection' => $connection,
        ];
    }
    public function fetch(string|int $connection): array|object
    {
        return (object) [
            'connection' => $connection,
        ];
    }
    public function status(string|int $connection): array|object
    {
        return (object) [
            'connection' => $connection,
        ];
    }
    public function disconnect(string|int $connection): array|object
    {
        return (object) [
            'connection' => $connection,
        ];
    }
    public function delete(string|int $connection): array|object
    {
        return (object) [
            'connection' => $connection,
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

    //  All code above can be called private to avoid the use of the function in the controller and maintain the integrity of the code.
    private function sendTextMessage(array|object $data): array|object
    {

        $payload = $this->parse($data);

        $response = $this->request->post("{$this->url}/message/sendText/{$data['connection']}", $payload);

        return (object) [
            'data' => $response->json(),
        ];
    }
    private function parseTextMessage(array|object $data): array|object
    {
        $data = [
            "number" => $data['number'],
            "options" => [
                "delay" => $data['delay'] ?? 1200,
                "presence" => "composing",
                "linkPreview" => false
            ],
            "textMessage" => [
                "text" => $data['message']
            ]
        ];

        return $data;
    }


}



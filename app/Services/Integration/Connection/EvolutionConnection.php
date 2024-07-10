<?php
namespace App\Services\Integration\Connection;

use App\Services\Integration\IntegrationServiceInterface;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;

class EvolutionConnection implements IntegrationServiceInterface
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

        $instanceToken = Hash::make(rand(1, 9999) + time());

        $payload = [
            "instanceName" => $data['instance'],
            "token" => $instanceToken,
            "qrcode" => true,
            "number" => $data['number'],
            "webhook" => 'https://webhook.site/1b1b1b1b-1b1b-1b1b-1b1b-1b1b1b1b1b1b',
            "webhook_by_events" => true,
            "events" => [
                "QRCODE_UPDATED",
                "MESSAGES_UPSERT",
                "CONNECTION_UPDATE",
            ]
        ];

        $response = $this->request->post("{$this->url}/message/sendText/{$data['instance']}", $payload);

        return (object) [
            'response' => $response->json(),
        ];
    }

    public function connect(int|string $instance): array|object
    {
        return (object) [
            'id' => $instance,
        ];
    }
    public function fetch(string|int $id): array|object
    {
        return (object) [
            'id' => $id,
        ];
    }
    public function status(string|int $id): array|object
    {
        return (object) [
            'id' => $id,
        ];
    }
    public function disconnect(string|int $id): array|object
    {
        return (object) [
            'id' => $id,
        ];
    }
    public function delete(string|int $id): array|object
    {
        return (object) [
            'id' => $id,
        ];
    }

    // Function to handle the webhook
    public function callback(array|object $data): array|object
    {
        return [
            'data' => $data,
        ];
    }

    // Message section
    public function sendTextMessage(array|object $data): array|object
    {

        $payload = [
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

        $response = $this->request->post("{$this->url}/message/sendText/{$data['instance']}", $payload);

        return (object) [
            'id' => $response->json(),
        ];
    }

}



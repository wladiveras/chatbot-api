<?php
namespace App\Services\Integration\Connection;

use App\Services\Integration\IntegrationServiceInterface;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;


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

    public function createInstance(array|object $data): array|object
    {

        $payload = [
            "number" => $data['number'],
            "options" => [
                "delay" => 1200,
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

    public function connectInstance(int|string $id): array|object
    {
        return (object) [
            'id' => $id,
        ];
    }
    public function fetchInstance(string|int $id): array|object
    {
        return (object) [
            'id' => $id,
        ];
    }
    public function instanceStatus(string|int $id): array|object
    {
        return (object) [
            'id' => $id,
        ];
    }
    public function disconnectInstance(string|int $id): array|object
    {
        return (object) [
            'id' => $id,
        ];
    }
    public function deleteInstance(string|int $id): array|object
    {
        return (object) [
            'id' => $id,
        ];
    }

    // Message section
    public function sendPlainText(array|object $data): array|object
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
            'id' => $response,
        ];
    }

}



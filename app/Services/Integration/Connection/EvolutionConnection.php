<?php
namespace App\Services\Integration\Connection;

use App\Services\Integration\IntegrationServiceInterface;
use Illuminate\Support\Facades\Http;

class EvolutionConnection implements IntegrationServiceInterface
{
    private mixed $url;
    private mixed $key;
    private mixed $request;

    public function __construct()
    {
        $this->url = config(['evolution.url']);
        $this->key = config(['evolution.key']);

        $this->request = Http::withHeaders([
            'apikey' => $this->key,
        ]);
    }

    public function createInstance(array|object $data): array|object
    {
        return (object) [
            'url' => $this->url,
            'key' => $this->key,
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
}



<?php
namespace App\Services\Integration\Connection;

use App\Services\Integration\IntegrationServiceInterface;


class EvolutionConnection implements IntegrationServiceInterface
{
    private $url = config(['evolution.url']);
    private $key = config(['evolution.key']);

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
}



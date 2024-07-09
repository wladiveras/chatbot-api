<?php
namespace App\Services\Integration;

interface IntegrationServiceInterface
{
    public function __construct();
    public function createInstance(array|object $data): array|object;
    public function connectInstance(string|int $id): array|object;
    public function fetchInstance(string|int $id): array|object;
    public function instanceStatus(string|int $id): array|object;
    public function disconnectInstance(string|int $id): array|object;
    public function deleteInstance(string|int $id): array|object;
}

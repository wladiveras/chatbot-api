<?php
namespace App\Services\Integration;

interface IntegrationServiceInterface
{

    public function createInstance(array|object $data): array|object;

    public function connectInstance(string|int $id): array|object;
}

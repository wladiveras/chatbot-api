<?php

namespace App\Services\Connection;

interface ConnectionServiceInterface
{
    public function createConnection(array|object $data): array|object;

    public function send(array|object $data): array|object;

    public function connect(string|int $connection): array|object;

    public function disconnect(string|int $connection): array|object;

    public function delete(string|int $connection): array|object;

    public function callback(array|object $data): array|object;

    public function parse(array|object $data): array|object;

    public function getConnectionProfile(string|int $connection, $data): array|object;
}

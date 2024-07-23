<?php

namespace App\Services\Messenger;

interface MessengerServiceInterface
{
    public function createConnection(array|object $data): array|object;

    public function send(array|object $data): array|object;

    public function connect(string|int $connection): array|object;

    public function fetch(string|int $connection): array|object;

    public function status(string|int $connection): array|object;

    public function disconnect(string|int $connection): array|object;

    public function delete(string|int $connection): array|object;

    public function callback(array|object $data): array|object;

    public function parse(array|object $data): array|object;

    private function response(bool $success, string $message, mixed $payload = []): object;
}

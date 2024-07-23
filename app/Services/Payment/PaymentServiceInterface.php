<?php

namespace App\Services\Payment;

interface PaymentServiceInterface
{
    public function pay(array|object $data): array|object;

    public function checkPayment(string|int $id): array|object;
    private function response(bool $success, string $message, mixed $payload = []): object;

}

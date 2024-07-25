<?php

namespace App\Services\Payment;

interface PaymentServiceInterface
{
    public function pay(array|object $data): array|object;

    public function checkPayment(string|int $id): array|object;
}

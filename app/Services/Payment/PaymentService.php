<?php

namespace App\Services\Payment;

use App\Services\Payment\Gateway\BraipGateway;

class PaymentService
{
    public static function gateway(string $gateway): PaymentServiceInterface
    {
        return match ($gateway) {
            'braip' => new BraipGateway,
            default => throw new \InvalidArgumentException('Invalid payment gateway'),
        };
    }
}

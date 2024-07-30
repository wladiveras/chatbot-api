<?php

namespace App\Services\Payment;

use App\Services\Payment\Gateway\BraipGateway;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    public static function gateway(string $gateway): PaymentServiceInterface
    {
        Log::debug(__CLASS__.'.'.__FUNCTION__.' => running');

        return match ($gateway) {
            'braip' => new BraipGateway,
            default => throw new \InvalidArgumentException('Invalid payment gateway selected.', 404),
        };
    }
}

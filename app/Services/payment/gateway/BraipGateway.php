<?php
namespace App\Services\Payment\Gateway;

use App\Services\Payment\PaymentServiceInterface;
use App\Enums\PaymentStatus;

class BraipGateway implements PaymentServiceInterface
{
    public function pay(array $data): PaymentStatus
    {
        return PaymentStatus::PROCESSING;
    }

    public function checkPayment(int|string $id): PaymentStatus
    {
        return PaymentStatus::SUCCESS;
    }
}



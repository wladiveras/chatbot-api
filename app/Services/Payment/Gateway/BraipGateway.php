<?php
namespace App\Services\Payment\Gateway;

use App\Services\Payment\PaymentServiceInterface;
use App\Enums\PaymentStatus;
//use App\Models\Order;

class BraipGateway implements PaymentServiceInterface
{
    public function pay(array|object $data): array|object
    {
        return (object) [
            'id' => null,
            'gateway' => 'braip',
            'status' => $data,
        ];
    }

    public function checkPayment(int|string $id): array|object
    {
        return (object) [
            'id' => $id,
            'gateway' => 'braip',
            'status' => 'paid',
        ];
    }
}



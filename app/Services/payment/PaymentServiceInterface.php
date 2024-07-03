<?php
namespace App\Services\Payment;

//use App\Models\Order;
use App\Enums\PaymentStatus;

interface PaymentServiceInterface
{

    public function pay(array $data): PaymentStatus;

    public function checkPayment(string|int $id): PaymentStatus;
}

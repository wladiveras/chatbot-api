<?php

namespace App\Services\Payment\Gateway;

use App\Services\Payment\PaymentServiceInterface;

//use App\Models\Order;

class BraipGateway implements PaymentServiceInterface
{
    public function pay(array|object $data): array|object
    {
        $paid = false;

        if (!$paid) {
            return $this->response(success: false, message: 'Não foi possível redirecionar.', );
        }

        return $this->response(success: true, message: 'Redirecionado para pagamento.');

    }

    public function checkPayment(int|string $id): array|object
    {
        $paid = true;

        if (!$paid) {
            return $this->response(success: false, message: 'Pagamento recusado.');
        }

        return $this->response(success: true, message: 'Pagamento confirmado, produto adquirido.');

    }

    private function response(bool $success, string $message, mixed $payload = []): object
    {

        if ($success === false) {
            throw new \Exception($message, 502); // bad gateway
        }

        return (object) [
            'success' => $success,
            'message' => $message,
            'payload' => $payload,
        ];
    }
}

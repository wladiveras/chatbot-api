<?php

namespace App\Services\Payment\Gateway;

use App\Repositories\Order\OrderRepository;
use App\Repositories\PaymentRequest\PaymentRequestRepository;
use App\Services\BaseService;
use App\Services\Payment\PaymentServiceInterface;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class BraipGateway extends BaseService implements PaymentServiceInterface
{
    private $orderRepository;

    private $paymentRequestRepository;

    public function __construct()
    {
        $this->orderRepository = App::make(OrderRepository::class);
        $this->paymentRequestRepository = App::make(PaymentRequestRepository::class);
    }

    public function pay(array|object $data): array|object
    {
        Log::debug(__CLASS__.'.'.__FUNCTION__.' => running');

        try {
            $orderCreate = $this->orderRepository->create($data);

            if (! $orderCreate) {
                return $this->error(
                    path: __CLASS__.'.'.__FUNCTION__,
                    message: 'Não foi possível redirecionar.',
                    code: 502
                );
            }

            return $this->success(message: 'Redirecionado para pagamento.');

        } catch (\Exception $e) {
            return $this->error(
                path: __CLASS__.'.'.__FUNCTION__,
                message: $e->getMessage(),
                code: $e->getCode()
            );
        }
    }

    public function checkPayment(int|string $id): array|object
    {
        Log::debug(__CLASS__.'.'.__FUNCTION__.' => running');

        try {
            $orderCreate = $this->orderRepository->first(column: 'id', value: $id);

            if (! $orderCreate) {
                return $this->error(
                    path: __CLASS__.'.'.__FUNCTION__,
                    message: 'pagamento não confirmado.',
                    code: 502
                );
            }

            return $this->success(message: 'Pagamento confirmado.');

        } catch (\Exception $e) {
            return $this->error(
                path: __CLASS__.'.'.__FUNCTION__,
                message: $e->getMessage(),
                code: $e->getCode()
            );
        }
    }
}

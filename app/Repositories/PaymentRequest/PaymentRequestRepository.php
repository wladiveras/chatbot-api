<?php

namespace App\Repositories\PaymentRequest;

use App\Models\PaymentRequest;
use App\Repositories\BaseRepository;

class PaymentRequestRepository extends BaseRepository implements PaymentRequestRepositoryInterface
{
    public function __construct(PaymentRequest $model)
    {
        parent::__construct($model);
    }
}

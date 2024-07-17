<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case PROCESSING = 'processing';

    case SUCCESS = 'success';

    case FAILURE = 'failure';
}

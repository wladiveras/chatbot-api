<?php

namespace App\Services\Flow;

interface FlowServiceInterface
{
    public function validate(array $data);

    public function create();
}

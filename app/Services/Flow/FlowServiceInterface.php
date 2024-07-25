<?php

namespace App\Services\Flow;

interface FlowServiceInterface
{
    public function parse(array $data);

    public function create(array $data);
}

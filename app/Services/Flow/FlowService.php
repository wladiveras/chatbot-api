<?php

namespace App\Services\Flow;

use App\Repositories\Flow\FlowRepositoryInterface;
use App\Services\Flow\FlowServiceInterface;

class FlowService implements FlowServiceInterface
{
    private $flowRepository;

    public function __construct(FlowRepositoryInterface $flowRepository)
    {
        $this->flowRepository = $flowRepository;
    }

    public function findAllUsers()
    {
        return $this->flowRepository->paginate(10);
    }


}

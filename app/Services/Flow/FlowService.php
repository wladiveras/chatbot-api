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

    public function validate(array $data): void
    {
        if($data) {

            $data = [
                'user_id' => $data['name'],
                'description' => $data['description'],
            ];

            $this->flowRepository->create($data);

        }

        throw new \Exception('Data is empty');
    }

    public function create(): array
    {
        return [
            'data' => $this->flowRepository->all(),
        ];
    }
}

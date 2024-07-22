<?php

namespace App\Services\Flow;

use App\Repositories\Flow\FlowRepository;
use Illuminate\Support\Facades\App;

class FlowService implements FlowServiceInterface
{
    private $flowRepository;

    public function __construct()
    {

        $this->flowRepository = App::make(FlowRepository::class);
    }

    public function validate(array $data): void
    {
        if ($data) {

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

<?php

namespace App\Services\ExampleService;


use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

use App\Repositories\Example\ExampleRepository;

use App\Services\BaseService;


class ExampleService extends BaseService implements ExampleServiceInterface
{
    private $exampleRepository;

    public function __construct()
    {
        $this->exampleRepository = App::make(ExampleRepository::class);
    }

    public function functionExample(array $data): JsonResponse
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running');

        try {
            $createExample = $this->exampleRepository->create($data);

            if (!$createExample) {
                return $this->error(message: 'Não deu certo.', code: 400);
            }

            return $this->success(
                message: 'Tudo certo, vamos continuar.',
                payload: $createExample
            );

        } catch (\Exception $e) {
            return $this->error(message: $e->getMessage(), code: $e->getCode());
        }
    }

    public function functionExample2($id): JsonResponse
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running');

        try {
            $fetchExample = $this->exampleRepository->first(column: 'id', value: $id);

            if (!$fetchExample) {
                return $this->error(message: 'Não deu certo.', code: 400);
            }

            return $this->success(
                message: 'Tudo certo, vamos continuar.',
                payload: $fetchExample
            );

        } catch (\Exception $e) {
            return $this->error(message: $e->getMessage(), code: $e->getCode());
        }
    }
}

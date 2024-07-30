<?php

namespace App\Services\ExampleService;

use Illuminate\Http\JsonResponse;

interface ExampleServiceInterface
{
    public function functionExample(array $data): JsonResponse;

    public function functionExample2(?int $id): JsonResponse;
}

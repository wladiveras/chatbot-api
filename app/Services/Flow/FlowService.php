<?php

namespace App\Services\Flow;

use App\Repositories\Flow\FlowRepository;
use App\Services\BaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class FlowService extends BaseService implements FlowServiceInterface
{
    private $flowRepository;

    public function __construct()
    {

        $this->flowRepository = App::make(FlowRepository::class);
    }

    public function validate(array $data): JsonResponse
    {
        Log::debug(__CLASS__.'.'.__FUNCTION__.' => running');

        try {
            $createExample = $this->flowRepository->create($data);

            if (! $createExample) {
                return $this->error(
                    path: __CLASS__.'.'.__FUNCTION__,
                    message: 'Não deu certo.',
                    code: 400
                );
            }

            return $this->success(
                message: 'Tudo certo, vamos continuar.',
                payload: $createExample
            );

        } catch (\Exception $e) {
            return $this->error(
                path: __CLASS__.'.'.__FUNCTION__,
                message: $e->getMessage(),
                code: $e->getCode()
            );
        }
    }

    public function create(): JsonResponse
    {
        Log::debug(__CLASS__.'.'.__FUNCTION__.' => running');

        try {
            $flowFetch = $this->flowRepository->all();

            if (! $flowFetch) {
                return $this->error(
                    path: __CLASS__.'.'.__FUNCTION__,
                    message: 'Não deu certo.',
                    code: 400
                );
            }

            return $this->success(
                message: 'Tudo certo, vamos continuar.',
                payload: $flowFetch
            );

        } catch (\Exception $e) {
            return $this->error(
                path: __CLASS__.'.'.__FUNCTION__,
                message: $e->getMessage(),
                code: $e->getCode()
            );
        }

    }
}

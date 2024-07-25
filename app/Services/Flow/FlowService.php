<?php

namespace App\Services\Flow;

use App\Repositories\Flow\FlowRepository;
use App\Services\BaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use stdClass;

class FlowService extends BaseService implements FlowServiceInterface
{
    private $flowRepository;

    public function __construct()
    {

        $this->flowRepository = App::make(FlowRepository::class);
    }

    public function parse(array $data): JsonResponse
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running');

        try {
            $createFlow = $this->flowRepository->create($data);

            if (!$createFlow) {
                return $this->error(
                    path: __CLASS__ . '.' . __FUNCTION__,
                    message: 'Não deu certo.',
                    code: 400
                );
            }

            return $this->success(
                message: 'Tudo certo, vamos continuar.',
                payload: $createFlow
            );

        } catch (\Exception $e) {
            return $this->error(
                path: __CLASS__ . '.' . __FUNCTION__,
                message: $e->getMessage(),
                code: $e->getCode()
            );
        }
    }
    public function userFlows(): ?stdClass
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running');

        try {

            $flows = (object) $this->flowRepository->userFlows();

            if (!$flows) {
                return $this->error(
                    path: __CLASS__ . '.' . __FUNCTION__,
                    message: 'Não deu certo, não foi possivel trazer os fluxos.',
                    code: 400
                );
            }

            return $this->success(
                message: 'Tudo certo, os fluxos foram retornados.',
                payload: $flows
            );

        } catch (\Exception $e) {
            return $this->error(
                path: __CLASS__ . '.' . __FUNCTION__,
                message: $e->getMessage(),
                code: $e->getCode()
            );
        }
    }
    public function create(array $data): ?stdClass
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running');

        try {
            $user = Auth::user();

            $payload = [
                'user_id' => $user->id,
                'name' => $data['name'],
                'description' => $data['description'],
                'payload' => json_encode($data['payload']),
                'commands' => json_encode($data['commands']),
            ];

            $flow = $this->flowRepository->create($payload);

            if (!$flow) {
                return $this->error(
                    path: __CLASS__ . '.' . __FUNCTION__,
                    message: 'Não deu certo, não foi possível criar um fluxo.',
                    code: 400
                );
            }

            return $this->success(
                message: 'Tudo certo, seu fluxo foi criado.',
                payload: $flow
            );

        } catch (\Exception $e) {
            return $this->error(
                path: __CLASS__ . '.' . __FUNCTION__,
                message: $e->getMessage(),
                code: $e->getCode()
            );
        }
    }
}

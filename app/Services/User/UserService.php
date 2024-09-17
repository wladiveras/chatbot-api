<?php

namespace App\Services\User;

use App\Repositories\User\UserRepository;
use App\Services\BaseService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class UserService extends BaseService implements UserServiceInterface
{
    private $userRepository;

    public function __construct()
    {
        $this->userRepository = App::make(UserRepository::class);
    }

    public function findAllUsers(): object
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running');

        try {
            $repository = $this->userRepository->paginate(10);

            if (!$repository) {
                return $this->error(
                    path: __CLASS__ . '.' . __FUNCTION__,
                    message: 'Usuários não encontrados.',
                    code: 404
                );
            }

            return $this->success(message: 'Usuário retornado com sucesso.', payload: $repository);

        } catch (\Exception $e) {
            return $this->error(
                path: __CLASS__ . '.' . __FUNCTION__,
                message: $e->getMessage(),
                code: $e->getCode()
            );
        }
    }

    public function findUser(int $id): object
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running');

        try {
            $repository = $this->userRepository->find($id);

            if (!$repository) {
                return $this->error(
                    path: __CLASS__ . '.' . __FUNCTION__,
                    message: 'Usuário não encontrado.',
                    code: 404
                );
            }

            return $this->success(message: 'Usuário retornado com sucesso.', payload: $repository);

        } catch (\Exception $e) {
            return $this->error(
                path: __CLASS__ . '.' . __FUNCTION__,
                message: $e->getMessage(),
                code: $e->getCode()
            );
        }
    }

    public function createUser(array $data): object
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running');

        try {
            $repository = $this->userRepository->create($data);

            if (!$repository) {
                return $this->error(
                    path: __CLASS__ . '.' . __FUNCTION__,
                    message: 'Falha ao criar o usuário.',
                    code: 500
                );
            }

            return $this->success(message: 'Usuário criado com sucesso.', payload: $repository);

        } catch (\Exception $e) {
            return $this->error(
                path: __CLASS__ . '.' . __FUNCTION__,
                message: $e->getMessage(),
                code: $e->getCode()
            );
        }

    }

    public function updateUser(int $id, array $data): object
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running');

        try {
            $repository = $this->userRepository->update($id, $data);

            if (!$repository) {
                return $this->error(
                    path: __CLASS__ . '.' . __FUNCTION__,
                    message: 'Falha ao atualizar o usuário. Usuário não encontrado.',
                    code: 404
                );
            }

            return $this->success(message: 'Seus dados foram atualizados.', payload: $repository);

        } catch (\Exception $e) {
            return $this->error(
                path: __CLASS__ . '.' . __FUNCTION__,
                message: $e->getMessage(),
                code: $e->getCode()
            );
        }
    }

    public function deleteUser(int $id): object
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running');

        try {
            $repository = $this->userRepository->delete($id);

            if (!$repository) {
                return $this->error(
                    path: __CLASS__ . '.' . __FUNCTION__,
                    message: 'Falha ao deletar o usuário. Usuário não encontrado.',
                    code: 404
                );
            }

            return $this->success(message: 'Usuário deletado com sucesso.', payload: $repository);

        } catch (\Exception $e) {
            return $this->error(
                path: __CLASS__ . '.' . __FUNCTION__,
                message: $e->getMessage(),
                code: $e->getCode()
            );
        }
    }
}

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

    public function findAllUsers()
    {
        Log::debug(__CLASS__.'.'.__FUNCTION__.' => running');

        try {
            $users = $this->userRepository->paginate(10);

            if (! $users) {
                return $this->error(
                    path: __CLASS__.'.'.__FUNCTION__,
                    message: 'Usuários não identificados.',
                    code: 400
                );
            }

            return $this->success(message: 'Usuário retornado com sucesso.', payload: $users);

        } catch (\Exception $e) {
            return $this->error(
                path: __CLASS__.'.'.__FUNCTION__,
                message: $e->getMessage(),
                code: $e->getCode()
            );
        }
    }

    public function findUser(int|string $id)
    {
        Log::debug(__CLASS__.'.'.__FUNCTION__.' => running');

        try {
            $user = $this->userRepository->find($id);

            if (! $user) {
                return $this->error(
                    path: __CLASS__.'.'.__FUNCTION__,
                    message: 'Usuários não identificados..',
                    code: 400
                );
            }

            return $this->success(message: 'Usuário retornado com sucesso.');

        } catch (\Exception $e) {
            return $this->error(
                path: __CLASS__.'.'.__FUNCTION__,
                message: $e->getMessage(),
                code: $e->getCode()
            );
        }
    }

    public function createUser(array $data)
    {
        Log::debug(__CLASS__.'.'.__FUNCTION__.' => running');

        try {
            $newUser = $this->userRepository->create($data);

            if (! $newUser) {
                return $this->error(
                    path: __CLASS__.'.'.__FUNCTION__,
                    message: 'Não foi possivel criar o usuário.',
                    code: 400
                );
            }

            return $this->success(message: 'Usuário criado com sucesso.');

        } catch (\Exception $e) {
            return $this->error(
                path: __CLASS__.'.'.__FUNCTION__,
                message: $e->getMessage(),
                code: $e->getCode()
            );
        }

    }

    public function updateUser(int|string $id, array $data)
    {
        Log::debug(__CLASS__.'.'.__FUNCTION__.' => running');

        try {
            $updatedUser = $this->userRepository->update($id, $data);

            if (! $updatedUser) {
                return $this->error(
                    path: __CLASS__.'.'.__FUNCTION__,
                    message: 'Não foi possivel atualizar o usuário.',
                    code: 400
                );
            }

            return $this->success(message: 'Usuário atualizado com sucesso.');

        } catch (\Exception $e) {
            return $this->error(
                path: __CLASS__.'.'.__FUNCTION__,
                message: $e->getMessage(),
                code: $e->getCode()
            );
        }
    }

    public function deleteUser(int|string $id)
    {
        Log::debug(__CLASS__.'.'.__FUNCTION__.' => running');

        try {
            $deleteUser = $this->userRepository->delete($id);

            if (! $deleteUser) {
                return $this->error(
                    path: __CLASS__.'.'.__FUNCTION__,
                    message: 'Não foi possivel deletar o usuário.',
                    code: 400
                );
            }

            return $this->success(message: 'Usuário deletado com sucesso.');

        } catch (\Exception $e) {
            return $this->error(
                path: __CLASS__.'.'.__FUNCTION__,
                message: $e->getMessage(),
                code: $e->getCode()
            );
        }
    }
}

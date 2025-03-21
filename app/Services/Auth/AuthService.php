<?php

namespace App\Services\Auth;

use App\Mail\MagicLinkEmail;
use App\Repositories\User\magicLinkRepository;
use App\Repositories\User\UserRepository;
use App\Services\BaseService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use stdClass;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AuthService extends BaseService implements AuthServiceInterface
{
    private $userRepository;

    private $magicLinkRepository;

    public function __construct()
    {
        $this->userRepository = App::make(UserRepository::class);
        $this->magicLinkRepository = App::make(magicLinkRepository::class);
    }

    public function signIn(array $data): ?stdClass
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running');

        try {
            $user = $this->userRepository->signIn($data['email']);

            if (!$user) {
                $user = $this->userRepository->signUpWithEmail([
                    'name' => null,
                    'email' => $data['email'],
                    'password' => Hash::make(Str::random(30)),
                    'avatar' => 'avatar.png',
                ]);
            }

            if (!$user) {
                return $this->error(
                    path: __CLASS__ . '.' . __FUNCTION__,
                    message: 'Não foi possivel identificar o usuário.',
                    code: 400
                );
            }

            $token = Str::uuid()->toString();

            $this->magicLinkRepository->create([
                'user_id' => $user->id,
                'token' => $token,
                'active' => 0,
            ]);

            $magicLink = Config::get('app.front_url') . "/callback/$token";

            Mail::to($user->email)->send(new MagicLinkEmail($user->name, $magicLink));

            return $this->success(
                message: 'Um link magico foi enviado para seu email, verifique seu email e a caixa de spam.',
                payload: []
            );

        } catch (\Exception $e) {
            return $this->error(
                path: __CLASS__ . '.' . __FUNCTION__,
                message: $e->getMessage(),
                code: $e->getCode()
            );
        }
    }

    public function redirectToProvider($provider): RedirectResponse
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running');

        $this->availableProviders($provider);

        return Socialite::driver($provider)->stateless()->redirect();
    }

    public function availableProviders($provider): array|object|bool
    {
        if (!in_array($provider, ['apple', 'twitter', 'google'])) {
            return $this->error(
                path: __CLASS__ . '.' . __FUNCTION__,
                message: 'Não foi possivel identificar o provedor.',
                code: 400
            );
        }

        return true;
    }

    public function callbackWithProvider($provider, $data): array|object
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running');

        $this->availableProviders($provider);

        try {
            $user = Socialite::driver($provider)->stateless()->user();

            if ($user) {
                $user = $this->userRepository->signUpWithProvider($provider, $user);
            }

            if (!$user) {
                return $this->error(
                    path: __CLASS__ . '.' . __FUNCTION__,
                    message: 'Não foi possivel realizar o login.',
                    code: 400
                );
            }

            $token = $user->createToken(env('APP_NAME'))->accessToken;

            return $this->success(
                message: 'Bem vindo, Login realizado com sucesso.',
                payload: $token
            );

        } catch (\Exception $e) {
            return $this->error(
                path: __CLASS__ . '.' . __FUNCTION__,
                message: $e->getMessage(),
                code: $e->getCode()
            );
        }
    }

    public function magicLink($token): ?stdClass
    {
        try {

            $magicLink = $this->magicLinkRepository->find(column: 'token', value: $token);

            $user = $this->userRepository->find($magicLink->user_id);

            if (!$user || !$magicLink) {
                return $this->error(
                    path: __CLASS__ . '.' . __FUNCTION__,
                    message: 'Token inválido.',
                    code: 400
                );
            }

            $this->magicLinkRepository->update($magicLink->id, ['active' => 0]);
            $this->userRepository->update($magicLink->user_id, ['email_verified_at' => now()]);

            $token = $user->createToken(Str::uuid()->toString())->plainTextToken;

            if (!$token) {
                return $this->error(
                    path: __CLASS__ . '.' . __FUNCTION__,
                    message: 'Não foi possivel realizar a autenticação.',
                    code: 400
                );
            }

            return $this->success(
                message: 'Autenticado com sucesso, seja bem vindo. Você será redirecionado em breve.',
                payload: $token
            );

        } catch (\Exception $e) {
            return $this->error(
                path: __CLASS__ . '.' . __FUNCTION__,
                message: $e->getMessage(),
                code: $e->getCode()
            );
        }
    }

    public function auth(): ?stdClass
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running');

        try {
            $auth = $this->userRepository->auth();

            if (!$auth) {
                return $this->error(
                    path: __CLASS__ . '.' . __FUNCTION__,
                    message: 'Usuário não está autenticado.',
                    code: 401
                );
            }

            return $this->success(
                message: 'Usuário autenticado.',
                payload: $auth
            );

        } catch (\Exception $e) {
            return $this->error(
                path: __CLASS__ . '.' . __FUNCTION__,
                message: $e->getMessage(),
                code: $e->getCode()
            );
        }
    }

    public function refreshToken(): ?stdClass
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running');

        try {
            $auth = $this->userRepository->refreshToken();

            if (!$auth) {
                return $this->error(
                    path: __CLASS__ . '.' . __FUNCTION__,
                    message: 'Usuário não está autenticado.',
                    code: 400
                );
            }

            return $this->success(
                message: 'Usuário autenticado.',
                payload: $auth
            );

        } catch (\Exception $e) {
            return $this->error(
                path: __CLASS__ . '.' . __FUNCTION__,
                message: $e->getMessage(),
                code: $e->getCode()
            );
        }
    }

    public function logout(): ?stdClass
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running');

        try {
            $auth = $this->userRepository->logout();

            if (!$auth) {
                return $this->error(
                    path: __CLASS__ . '.' . __FUNCTION__,
                    message: 'Houve um problema na solicitação.',
                    code: 400
                );
            }

            return $this->success(
                message: 'Usuário deslogado com sucesso.',
                payload: $auth
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

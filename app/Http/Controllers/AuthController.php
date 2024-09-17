<?php

namespace App\Http\Controllers;

use App\Services\Auth\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Validator;

class AuthController extends BaseController
{
    private AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function login(Request $request): JsonResponse
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running', [
            'request' => $request,
        ]);

        $data = Validator::make($request->all(), [
            'email' => 'email|required',
        ]);

        if ($data->fails()) {
            return $this->error(
                path: __CLASS__ . '.' . __FUNCTION__,
                code: 422
            );
        }

        $service = $this->authService->signIn($data->validate());

        if ($service->success) {
            return $this->success(
                title: 'Bem vindo, falta pouco!',
                message: $service->message,
                payload: $service->payload
            );
        }

        return $this->error(
            path: __CLASS__ . '.' . __FUNCTION__,
            message: $service->message,
            code: $service->code
        );
    }

    public function magicLink(string $token, Request $request): JsonResponse
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running', [
            'request' => $request,
        ]);

        $service = $this->authService->magicLink($token);

        if ($service->success) {
            return $this->success(
                title: 'Autenticação!',
                message: $service->message,
                payload: $service->payload
            );
        }

        return $this->error(
            path: __CLASS__ . '.' . __FUNCTION__,
            message: $service->message,
            code: $service->code
        );
    }

    public function redirectToProvider(string $provider, Request $request): RedirectResponse
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running', [
            'request' => $request,
        ]);

        return $this->authService->redirectToProvider($provider);
    }

    public function callbackWithProvider(string $provider, Request $request): JsonResponse
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running', [
            'request' => $request,
        ]);

        $service = $this->authService->callbackWithProvider($provider, $request);

        if ($service->success) {
            return $this->success(
                title: 'Redirecionando...',
                message: $service->message,
                payload: $service->payload
            );
        }

        return $this->error(
            path: __CLASS__ . '.' . __FUNCTION__,
            message: $service->message,
            code: $service->code
        );
    }

    public function user(Request $request): JsonResponse
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running', [
            'request' => $request,
        ]);

        $service = $this->authService->auth();

        if ($service->success) {
            return $this->success(
                title: 'Bem vindo!',
                message: $service->message,
                payload: $service->payload
            );
        }

        return $this->error(
            path: __CLASS__ . '.' . __FUNCTION__,
            message: $service->message,
            code: $service->code
        );
    }

    public function logout(Request $request): JsonResponse
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running', [
            'request' => $request,
        ]);

        $service = $this->authService->logout();

        if ($service->success) {
            return $this->success(
                title: 'Até logo!',
                message: $service->message,
                payload: $service->payload
            );
        }

        return $this->error(
            path: __CLASS__ . '.' . __FUNCTION__,
            message: $service->message,
            code: $service->code
        );

    }

    public function refreshToken(Request $request): JsonResponse
    {
        Log::debug(__CLASS__ . '.' . __FUNCTION__ . ' => running', [
            'request' => $request,
        ]);

        $service = $this->authService->refreshToken();

        if ($service->success) {
            return $this->success(
                title: 'Token atualizado!',
                message: $service->message,
                payload: $service->payload
            );
        }

        return $this->error(
            path: __CLASS__ . '.' . __FUNCTION__,
            message: $service->message,
            code: $service->code
        );
    }
}

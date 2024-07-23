<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;


class AuthController extends BaseController
{

    public function __construct()
    {

    }

    public function auth(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        try {
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return $this->error(
                    message: 'Usuário não identificado.',
                    payload: $user,
                    code: 401
                );
            }

            return $this->success(
                message: 'Token gerado com sucesso para o usuário.',
                payload: $user->createToken($request->email)->plainTextToken
            );

        } catch (\Exception $exception) {
            return $this->error(
                message: $exception->getMessage(),
                payload: $request->all(),
                code: $exception->getCode()
            );
        }
    }

    public function user(Request $request): JsonResponse
    {
        try {
            $user = auth()->user();

            return $this->success(
                message: 'Usuário retornado com sucesso',
                payload: $user
            );

        } catch (\Exception $exception) {
            return $this->error(
                message: $exception->getMessage(),
                payload: $request->all(),
                code: $exception->getCode()
            );
        }
    }
}

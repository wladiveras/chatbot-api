<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Validator;

class AuthController extends BaseController
{

    public function __construct()
    {

    }

    public function auth(Request $request): JsonResponse
    {
        $data = Validator::make($request->all(), [
            'email' => 'email|required',
        ]);

        if ($data->fails()) {
            return $this->error(
                message: 'Error de validação de dados.',
                payload: $data->errors(),
                code: 400
            );
        }

        $data = $data->validated();

        try {
            $user = User::where('email', $data['email'])->first();

            if (!$user) {
                return $this->error(
                    message: 'Usuário não identificado.',
                    payload: $user,
                    code: 401
                );
            }

            return $this->success(
                message: 'Token gerado com sucesso para o usuário.',
                payload: $user->createToken(Str::uuid()->toString())->plainTextToken
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
    public function token(Request $request): JsonResponse
    {
        $user = $request->user();

        return $this->success(
            message: 'Token atualizado com sucesso.',
            payload: $user->createToken(Str::uuid()->toString())->plainTextToken
        );
    }

    public function logout(Request $request): JsonResponse
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return $this->success(
                message: 'Usuário desconectado com sucesso',
                payload: []
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

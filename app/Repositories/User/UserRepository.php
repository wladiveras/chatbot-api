<?php

namespace App\Repositories\User;

use App\Models\User;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function auth(): object|array
    {
        $user = auth()->user();

        return $user;
    }

    public function signIn(string $email): object|array|null
    {
        return $this->model->where('email', $email)->first();

    }

    public function refreshToken(): ?string
    {
        $user = auth()->user();

        return $user->createToken(Str::uuid()->toString())->plainTextToken;
    }

    public function sigUpWithEmail($data): object|array
    {
        return (object) $this->model->create($data)->toArray();
    }

    public function getToken($token): object|array
    {
        return (object) DB::table('personal_access_tokens')->where('token', $token)->first();
    }

    public function logout(): array|bool
    {
        $user = auth()->user();

        return $user->currentAccessToken()->delete();
    }
}

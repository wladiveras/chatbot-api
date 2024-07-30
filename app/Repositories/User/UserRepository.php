<?php

namespace App\Repositories\User;

use App\Models\User;
use App\Repositories\BaseRepository;
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

    public function signUpWithEmail($data): object|array
    {
        return (object) $this->model->create($data)->toArray();
    }

    public function signUpWithProvider($provider, $data): object|array
    {
        $provider = match ($provider) {
            'twitter' => 'x_id',
            'google' => 'google_id',
            'facebook' => 'facebook_id',
            default => null,
        };

        if ($provider === null) {
            return [];
        }

        $id = $data->getId() ?? null;
        $name = $data->getName() ?? $data->getNickname();
        $avatar = $data->getAvatar() ?? null;
        $email = $data->getEmail() ?? null;

        $user = $this->model->updateOrCreate([
            $provider => $id,
        ], [
            'name' => $name,
            'email' => $email,
            'avatar' => $avatar,
        ]);

        return $user;
    }

    public function logout(): array|bool
    {
        $user = auth()->user();

        return $user->currentAccessToken()->delete();
    }
}

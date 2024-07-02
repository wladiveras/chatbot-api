<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\User\UserServiceInterface;
use App\Services\User\UserService;


class ServicesProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->app->bind(UserServiceInterface::class, UserService::class);
    }
}

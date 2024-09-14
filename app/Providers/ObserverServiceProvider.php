<?php

namespace App\Providers;

use App\Models\User;
use App\Observers\UserObserver;
use App\Models\Connection;
use App\Observers\ConnectionObserver;
use App\Models\ConnectionProfile;
use App\Observers\ConnectionProfileObserver;
use App\Models\Flow;
use App\Observers\FlowObserver;
use App\Models\FlowSession;
use App\Observers\FlowSessionObserver;
use App\Models\Order;
use App\Observers\OrderObserver;


use Illuminate\Support\ServiceProvider;

class ObserverServiceProvider extends ServiceProvider
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
        User::observe(UserObserver::class);
        Connection::observe(ConnectionObserver::class);
        ConnectionProfile::observe(ConnectionProfileObserver::class);
        Flow::observe(FlowObserver::class);
        FlowSession::observe(FlowSessionObserver::class);
        Flow::observe(FlowObserver::class);
        Order::observe(OrderObserver::class);
    }
}

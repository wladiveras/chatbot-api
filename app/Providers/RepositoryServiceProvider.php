<?php

namespace App\Providers;


use Illuminate\Support\ServiceProvider;

use App\Repositories\User\UserRepository;
use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\Client\ClientRepository;
use App\Repositories\Client\ClientRepositoryInterface;
use App\Repositories\Connection\ConnectionRepository;
use App\Repositories\Connection\ConnectionRepositoryInterface;
use App\Repositories\ConnectionFlow\ConnectionFlowRepository;
use App\Repositories\ConnectionFlow\ConnectionFlowRepositoryInterface;
use App\Repositories\ConnectionSetting\ConnectionSettingRepository;
use App\Repositories\ConnectionSetting\ConnectionSettingRepositoryInterface;
use App\Repositories\Currency\CurrencyRepositoryInterface;
use App\Repositories\Currency\CurrencyRepository;
use App\Repositories\Flow\FlowRepositoryInterface;
use App\Repositories\Flow\FlowRepository;
use App\Repositories\Lead\LeadRepositoryInterface;
use App\Repositories\Lead\LeadRepository;
use App\Repositories\Message\MessageRepositoryInterface;
use App\Repositories\Message\MessageRepository;
use App\Repositories\Order\OrderRepositoryInterface;
use App\Repositories\Order\OrderRepository;
use App\Repositories\PaymentRequest\PaymentRequestRepositoryInterface;
use App\Repositories\PaymentRequest\PaymentRequestRepository;
use App\Repositories\Product\ProductRepositoryInterface;
use App\Repositories\Product\ProductRepository;
use App\Repositories\Setting\SettingRepositoryInterface;
use App\Repositories\Setting\SettingRepository;


class RepositoryServiceProvider extends ServiceProvider
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
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(ClientRepositoryInterface::class, ClientRepository::class);
        $this->app->bind(ConnectionRepositoryInterface::class, ConnectionRepository::class);
        $this->app->bind(ConnectionFlowRepositoryInterface::class, ConnectionFlowRepository::class);
        $this->app->bind(ConnectionSettingRepositoryInterface::class, ConnectionSettingRepository::class);
        $this->app->bind(CurrencyRepositoryInterface::class, CurrencyRepository::class);
        $this->app->bind(FlowRepositoryInterface::class, FlowRepository::class);
        $this->app->bind(LeadRepositoryInterface::class, LeadRepository::class);
        $this->app->bind(MessageRepositoryInterface::class, MessageRepository::class);
        $this->app->bind(OrderRepositoryInterface::class, OrderRepository::class);
        $this->app->bind(PaymentRequestRepositoryInterface::class, PaymentRequestRepository::class);
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->bind(SettingRepositoryInterface::class, SettingRepository::class);
    }
}

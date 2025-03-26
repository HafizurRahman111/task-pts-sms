<?php

namespace App\Providers;

use App\Interfaces\SmsGatewayInterface;
use App\Services\MockSmsGatewayService;
use App\Services\TwilioSmsGatewayService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(SmsGatewayInterface::class, TwilioSmsGatewayService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

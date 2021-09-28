<?php

namespace App\Providers;

use App\Services\Dalion\DalionApi;
use App\Services\Dalion\DalionApiMock;
use App\Services\Dalion\DalionSoapApi;
use App\Services\Sber\SberPayment;
use App\Services\Sber\SberPaymentApi;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider {
    public array $singletons = [
        SberPaymentApi::class => SberPayment::class
    ];

    public function register() {
    }

    public function boot() {
        $this->app->singleton(DalionApi::class, function ($app) {
            /** @var \Illuminate\Contracts\Foundation\Application $app */
            return $app->environment() === 'production' ? new DalionSoapApi() : new DalionApiMock();
        });
    }
}

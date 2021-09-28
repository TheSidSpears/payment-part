<?php

namespace App\Providers;

use App\Services\Sber\SberCallbackValidation;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;

class AuthServiceProvider extends ServiceProvider {

    public function boot(SberCallbackValidation $sberCallbackValidation) {
        $this->registerPolicies();

        Auth::viaRequest('sber-token', $sberCallbackValidation);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Merchant extends Model {
    protected $hidden = [
        'api_user_id', 'url', 'approve_uri',
        'sber_api_endpoint', 'sber_login', 'sber_password', 'sber_login_testing', 'sber_password_testing'];

    public function apiUser(): BelongsTo {
        return $this->belongsTo(ApiUser::class);
    }

    public function getNameAttribute(): string {
        return $this->apiUser->name;
    }

    public function getPaymentApproveUrlAttribute(): string {
        return $this->url . $this->approve_uri;
    }

    public function sberLogin(): string {
        return $this->isSberProduction() ? $this->sber_login : $this->sber_login_testing;
    }

    public function sberPassword(): string {
        return $this->isSberProduction() ? $this->sber_password : $this->sber_password_testing;
    }

    public function isSberProduction(): bool {
        return $this->sber_api_endpoint === 'production';
    }

    public static function current(): Merchant {
        return ApiUser::current()->merchant;
    }
}

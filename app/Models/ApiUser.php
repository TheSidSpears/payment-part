<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Auth;

class ApiUser extends Model {
    protected $fillable = ['name'];
    protected $hidden = ['token'];

    public function merchant(): HasOne {
        return $this->hasOne(Merchant::class);
    }

    public static function current(): ApiUser {
        /** @var ApiUser $authUser */
        $authUser = Auth::user();
        return $authUser;
    }
}

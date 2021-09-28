<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model {

    protected $fillable = ['name', 'second_name', 'patronymic', 'phone', 'email', 'passport_serial', 'passport_num', 'passport_date'];
    protected $hidden = ['id', 'created_at', 'updated_at'];

    protected $casts = [
        'passport_date' => 'date'
    ];

    public function transactions(): HasMany {
        return $this->hasMany(Transaction::class);
    }
}

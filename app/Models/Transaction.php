<?php

namespace App\Models;

use App\Casts\Json;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $fillable = [
        'customer_id',
        'product_uuid',
        'sber_order_id',
        'custom_data',
        'merchant_id',
        'status'
    ];

    protected $casts = [
        'custom_data' => Json::class,
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function merchant(): BelongsTo {
        return $this->belongsTo(Merchant::class);
    }
}

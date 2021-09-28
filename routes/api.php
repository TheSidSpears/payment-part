<?php

use App\Http\Controllers\{DalionCallback, OrderStatus, RegisterOrder, SberCallback};

Route::middleware('auth:api')->group(function () {

    Route::get('can-reserve-car/{uuid}', [RegisterOrder::class, 'canReserveCar'])
        ->name('can_reserve_car');

    Route::get('payment-form-url', [RegisterOrder::class, 'getPaymentFormUrl'])
        ->name('payment_form_url');

    Route::get('order/status/{transaction}', [OrderStatus::class, 'byTransaction'])
        ->name('order_status');

    Route::get('order/status-by-sber-id/{sberOrderId}', [OrderStatus::class, 'bySberOrderId'])
        ->name('order_status_by_sber_id');

    Route::prefix('dalion-callback/')
        ->group(function () {
            Route::get('deposit/{transaction}', [DalionCallback::class, 'deposit'])->name('deposit');
            Route::get('reverse/{transaction}', [DalionCallback::class, 'reverse'])->name('reverse');
            Route::get('refund/{transaction}', [DalionCallback::class, 'refund'])->name('refund');
            Route::get('receipt/{transaction}', [DalionCallback::class, 'receipt'])->name('receipt');
        });
});

Route::middleware('auth:sber')
    ->get('sber-callback', SberCallback::class)
    ->name('sber_callback');

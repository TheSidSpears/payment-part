<?php

use Illuminate\Support\Facades\Route;

foreach (config('sber.endpoints') as $env => $url){
    Route::domain($url)
        ->name("sber.$env.")
        ->group(function () {
            Route::post('register.do')->name('register');
            Route::post('registerPreAuth.do')->name('registerPreAuth');
            Route::post('getOrderStatusExtended.do')->name('getOrderStatusExtended');
            Route::post('deposit.do')->name('deposit');
            Route::post('reverse.do')->name('reverse');
            Route::post('refund.do')->name('refund');
            Route::post('getReceiptStatus.do')->name('getReceiptStatus');
        });
}

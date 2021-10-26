<?php

use Revosystems\RedsysPayment\Http\Controllers\CheckoutController;
use Revosystems\RedsysPayment\Http\Controllers\WebhookController;

Route::group(['prefix' => config('redsys-payment.routePrefix'), 'namespace' => 'Revosystems\RedsysPayment\Controllers', 'middleware' => config('redsys-payment.routeMiddleware', ['web','auth'])], function () {
    Route::post('redsys', [WebhookController::class, 'webhook'])->name('rv.webhooks.redsys');
    Route::post('payCheck', [CheckoutController::class, 'index'])->name('payCheck');
});


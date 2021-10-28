<?php

use Revosystems\Redsys\Http\Controllers\WebhookController;

Route::group(['prefix' => config('redsys.routePrefix'), 'namespace' => 'Revosystems\Redsys\Controllers', 'middleware' => config('redsys.routeMiddleware', ['web','auth'])], function () {
    Route::post('redsys', [WebhookController::class, 'webhook'])->name('rv.webhooks.redsys');
});


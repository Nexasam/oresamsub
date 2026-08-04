<?php

use App\Http\Controllers\Api\V2\BusinessApiController;
use Illuminate\Support\Facades\Route;

Route::get('/openapi.json', fn () => response()->json(
    json_decode(file_get_contents(resource_path('api/oresamsub-v2.openapi.json')), true, 512, JSON_THROW_ON_ERROR)
))->name('openapi');

Route::middleware(['business.api_token', 'throttle:business-api'])->group(function () {
    Route::get('/catalogue', [BusinessApiController::class, 'catalogue'])->name('catalogue');
    Route::get('/wallet', [BusinessApiController::class, 'wallet'])->name('wallet');
    Route::post('/validate-customer', [BusinessApiController::class, 'validateCustomer'])->name('validate-customer');
    Route::post('/buy-service', [BusinessApiController::class, 'buyService'])->name('buy-service');
    Route::get('/transactions/{reference}', [BusinessApiController::class, 'transaction'])->name('transactions.show');
});

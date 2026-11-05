<?php

use App\Http\Controllers\Api\UssdGateway\V1\UssdGatewayJobController;
use Illuminate\Support\Facades\Route;

Route::middleware(['ussd.gateway', 'throttle:ussd-gateway'])->group(function () {
    Route::post('/bind', [UssdGatewayJobController::class, 'bind']);
    Route::get('/status', [UssdGatewayJobController::class, 'status']);
    Route::post('/jobs/claim', [UssdGatewayJobController::class, 'claim']);
    Route::post('/jobs/{attempt}/started', [UssdGatewayJobController::class, 'started']);
    Route::post('/jobs/{attempt}/result', [UssdGatewayJobController::class, 'result']);
});

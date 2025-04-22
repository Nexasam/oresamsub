<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;

Route::get('api/user', function (Request $request) {
    return json_encode([
        'testing'
    ]);
})->middleware('sanctum');




// Route::middleware('auth:sanctum')->group( function () {
//     Route::resource('products', ProductController::class);
// });
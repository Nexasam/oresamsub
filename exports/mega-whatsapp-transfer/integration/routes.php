<?php

use App\Http\Controllers\Api\v1\VendorUsersApi\MegaWhatsappWebhookController;
use Illuminate\Support\Facades\Route;

// Add this to routes/web.php (or routes/api.php if the target app's webhook
// routes live there). Keep the final public URL consistent with Meta's setup.
Route::post('/whatsapp/webhook', [MegaWhatsappWebhookController::class, 'webhook']);

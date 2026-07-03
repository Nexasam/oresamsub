<?php
namespace App\Services\Whatsapp;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class WhatsappInteractiveRouter
{
    public function handle(
        string $phone,
        string $buttonId,
        User $user,
        ?array $session = null
    ): bool {

        return app(WhatsappInteractiveService::class)->handle(
             $phone,
            $buttonId,
             $user,
           
        );
    }
}
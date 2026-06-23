<?php
namespace App\Services\Whatsapp;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AccountHandler
{
    public function handle($phone): string
    {
        // TODO: fetch user + wallet
        $user = null; // replace with real lookup

        if (!$user) {
            return "⚠️ Your account is not linked yet.\n\nTap here to link your account:\nhttps://your-link.com/link";
        }

        return "💼 Account Details\nBalance: ₦0.00";
    }
}
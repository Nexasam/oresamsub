<?php
namespace App\Services\Whatsapp;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class IntentRouter
{
    public function resolve(string $text): array
    {
        $text = strtoupper(trim($text));

        // ACCOUNT / BALANCE / FUNDING
        if (preg_match('/(ACCOUNT|BALANCE|FUND|WALLET|DETAILS)/', $text)) {
            return ['intent' => 'account'];
        }

        // SERVICES
        if (preg_match('/(BUY DATA|DATA|AIRTIME|RECHARGE|SUBSCRIBE|MTN|GLO|AIRTEL|9MOBILE)/', $text)) {
            return ['intent' => 'services', 'raw' => $text];
        }

        // FAVORITES
        if (preg_match('/(FAV|FAVOURITE|FAVORITE|RECENT|REPEAT)/', $text)) {
            return ['intent' => 'favorites'];
        }

        // OFFERS
        if (preg_match('/(OFFERS|PROMO|DISCOUNT)/', $text)) {
            return ['intent' => 'offers'];
        }

        // ABOUT
        if (preg_match('/(ABOUT|WHO ARE YOU|INFO)/', $text)) {
            return ['intent' => 'about'];
        }

        return ['intent' => 'fallback', 'raw' => $text];
    }
}
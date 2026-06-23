<?php
namespace App\Services\Whatsapp;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FavouritesHandler
{
    public function handle($phone): string
    {
        return "⭐ Your recent successful transactions will appear here for quick repurchase.";
    }
}
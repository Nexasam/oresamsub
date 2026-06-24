<?php
namespace App\Services\Whatsapp;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AboutHandler
{
    public function handle(): string
    {
        return "ℹ️ OresamSub\nFast VTU services for data, airtime & bills.\n24/7 automated service2.";
    }
}
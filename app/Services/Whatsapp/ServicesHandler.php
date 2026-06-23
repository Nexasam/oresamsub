<?php
namespace App\Services\Whatsapp;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ServicesHandler
{
    public function handle($text): string
    {
        // Example intent: "MTN 1GB" or "BUY DATA"
        
        if (str_contains($text, 'DATA')) {
            return "📦 Data Plans:\n1. MTN 1GB - ₦XXX\n2. Airtel 2GB - ₦XXX\nReply with option number.";
        }

        return "📡 Services:\n- Buy Data\n- Buy Airtime\n- Cable TV\nReply 'DATA' to continue.";
    }
}
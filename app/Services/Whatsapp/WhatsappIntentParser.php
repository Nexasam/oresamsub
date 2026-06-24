<?php
namespace App\Services\Whatsapp;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;


class WhatsappIntentParser
{
    public function parse(string $message): array
    {
        $message = strtolower(trim($message));

        return [

            'raw_message' => $message,

            'type' => $this->detectType($message),

            'network' => $this->extractNetwork($message),

            'data_size_in_mb' => $this->extractDataSize($message),

            'validity_in_days' => $this->extractValidity($message),

            'amount' => $this->extractAmount($message),

            'phone' => $this->extractPhone($message),

            // 'unique_purchase_string' => $this->extractShortcut($message),
        ];
    }

   
    private function detectType(string $message): ?string
    {

        // Navigation intents (ecosystem routing)
        if (preg_match('/\b(app|mobile app|download app|android|ios)\b/i', $message)) {
            return 'navigation_app';
        }

        if (preg_match('/\b(telegram|tg channel|telegram bot)\b/i', $message)) {
            return 'navigation_telegram';
        }

        if (preg_match('/\b(support|help desk|agent|customer care)\b/i', $message)) {
            return 'navigation_support';
        }

        // Data

        // if (
        //     preg_match('/\b(gb|meg|megabyte|gigabyte|giga|mb|gig|gigs|data)\b/i', $message)
        // ) {
        //     return 'data';
        // }

        if (
            preg_match('/\d+(?:\.\d+)?\s*(gb|mb)/i', $message) ||
            preg_match('/\b(data|gig|gigs|gigabyte|megabyte|meg)\b/i', $message)
        ) {
            return 'data';
        }

        // Airtime

        if (
            preg_match('/\b(airtime|vtu|recharge|topup|top up)\b/i', $message)
        ) {
            return 'airtime';
        }

        // Favorites

        if (
            preg_match(
                '/\b(fav|favs|favorite|favorites|favourite|favourites|buy again|recent)\b/i',
                $message
            )
        ) {
            return 'favorites';
        }

        // Account

        if (
            preg_match(
                '/\b(balance|fund|wallet|account)\b/i',
                $message
            )
        ) {
            return 'account';
        }

        // Transactions

        if (
            preg_match(
                '/\b(txn|txns|transaction|transactions|history)\b/i',
                $message
            )
        ) {
            return 'transactions';
        }

        // About

        if (
            preg_match(
                '/\b(about|about us)\b/i',
                $message
            )
        ) {
            return 'about';
        }

        return null;
    }

    private function extractNetwork(string $message): ?string
    {
        if (str_contains($message, 'mtn')) {
            return 'mtn';
        }

        if (str_contains($message, 'airtel')) {
            return 'airtel';
        }

        if (str_contains($message, 'glo')) {
            return 'glo';
        }

        if (
            str_contains($message, '9mobile') ||
            str_contains($message, 'etisalat')
        ) {
            return '9mobile';
        }

        return null;
    }


    private function normalizeNumbers(string $message): string
{
    $map = [
        'one' => '1',
        'two' => '2',
        'three' => '3',
        'four' => '4',
        'five' => '5',
        'six' => '6',
        'seven' => '7',
        'eight' => '8',
        'nine' => '9',
        'ten' => '10',
        'fifty' => '50',
    ];

    return str_ireplace(
        array_keys($map),
        array_values($map),
        $message
    );
}

    
    
    public function extractDataSize(string $message): ?int
    {
        $message = $this->normalizeNumbers($message);

        $message = preg_replace(
            '/(\d+(?:\.\d+)?)\s*g\b/i',
            '$1gb',
            $message
        );

        $message = preg_replace(
            '/(\d+(?:\.\d+)?)\s*m\b/i',
            '$1mb',
            $message
        );

        $message = str_replace(
            ['gigabytes', 'gigabyte', 'gigs', 'gig'],
            'gb',
            $message
        );

        $message = str_replace(
            ['megabytes', 'megabyte', 'megs', 'meg'],
            'mb',
            $message
        );

        if (
            preg_match(
                '/(\d+(?:\.\d+)?)\s*gb/i',
                $message,
                $match
            )
        ) {
            return (int) ((float) $match[1] * 1000);
        }

        if (
            preg_match(
                '/(\d+(?:\.\d+)?)\s*mb/i',
                $message,
                $match
            )
        ) {
            return (int) $match[1];
        }

        return null;
    }

    
    // private function extractValidity(string $message): ?int
    // {
    //     if (
    //         str_contains($message, 'daily')
    //     ) {
    //         return 1;
    //     }

   

    //     if (
    //         str_contains($message, 'weekly')
    //     ) {
    //         return 7;
    //     }

    //     if (
    //         str_contains($message, 'week')
    //     ) {
    //         return 7;
    //     }

    //     if (
    //         str_contains($message, 'monthly')
    //     ) {
    //         return 30;
    //     }

    //     if (
    //         str_contains($message, 'month')
    //     ) {
    //         return 30;
    //     }

    //     preg_match(
    //         '/(\d+)\s*day/i',
    //         $message,
    //         $match
    //     );

    //     if (!empty($match[1])) {
    //         return (int) $match[1];
    //     }

    //     return null;
    // }

    
    private function extractValidityOption(string $message): ?int
    {
        $message = trim(strtolower($message));

        return match ($message) {
            '1' => 1,   // Daily
            '2' => 7,   // Weekly
            '3' => 30,  // Monthly
            default => null
        };
    }
    
    private function extractValidity(string $message): ?int
    {

     // 1. Handle menu selection first (WhatsApp UX flow)
     $option = $this->extractValidityOption($message);

     if ($option !== null) {
         return $option;
     }

    $message = $this->normalizeNumbers($message);

    if (
        str_contains($message, 'daily')
    ) {
        return 1;
    }

    if (
        str_contains($message, 'weekly') ||
        str_contains($message, 'week')
    ) {
        return 7;
    }

    if (
        str_contains($message, 'monthly') ||
        str_contains($message, 'month')
    ) {
        return 30;
    }

    if (
        preg_match(
            '/(\d+)\s*days?/i',
            $message,
            $match
        )
    ) {
        return (int) $match[1];
    }

    return null;
}

    private function extractPhone(string $message): ?string
    {
        preg_match(
            '/(\+234\d{10}|234\d{10}|0\d{10})/',
            $message,
            $match
        );

        return $match[1] ?? null;
    }

    private function extractAmount(string $message): ?int
    {
        if ($this->detectType($message) !== 'airtime') {
            return null;
        }
    
        preg_match(
            '/\b(\d{2,6})\b/',
            $message,
            $match
        );
    
        return isset($match[1])
            ? (int) $match[1]
            : null;
    }


   
}
<?php
namespace App\Services\Whatsapp;

use App\Models\User;

class WhatsappUserResolver
{
    public function resolve(string $phone): ?User
    {
        $phone = $this->normalizePhone($phone);
    
        return User::where('phone_number', $phone)
            ->orWhere('whatsapp_number', $phone)
            ->first();
    }

    private function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/\D/', '', $phone);

        if (str_starts_with($phone, '234')) {
            $phone = '0' . substr($phone, 3);
        }

        return $phone;
    }
}
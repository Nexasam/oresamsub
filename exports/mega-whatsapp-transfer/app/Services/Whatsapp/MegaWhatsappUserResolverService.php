<?php

namespace App\Services\Whatsapp;

use App\Models\User;

class MegaWhatsappUserResolverService
{
    public function resolve(
        string $phone
    ): ?User {

        $phone = $this->normalize(
            $phone
        );

        logger('normalized phone: '.$phone);

        return User::where('phone_number', $phone)
            ->orWhere('whatsapp_number', $phone)
            ->first();
    }

    public function normalize(
        string $phone
    ): string {

        $phone = preg_replace(
            '/[^0-9]/',
            '',
            $phone
        );

        if (
            str_starts_with(
                $phone,
                '234'
            )
        ) {
            $phone =
                '0' .
                substr(
                    $phone,
                    3
                );
        }

        return $phone;
    }
}

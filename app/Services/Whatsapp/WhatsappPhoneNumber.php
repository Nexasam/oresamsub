<?php

namespace App\Services\Whatsapp;

class WhatsappPhoneNumber
{
    public function toMeta(?string $phone): ?string
    {
        $digits = preg_replace('/\D/', '', (string) $phone);

        if ($digits === '') {
            return null;
        }

        if (str_starts_with($digits, '234') && strlen($digits) === 13) {
            return $digits;
        }

        if (str_starts_with($digits, '0') && strlen($digits) === 11) {
            return '234'.substr($digits, 1);
        }

        if (strlen($digits) === 10) {
            return '234'.$digits;
        }

        return null;
    }

    public function toLocal(?string $phone): ?string
    {
        $meta = $this->toMeta($phone);

        return $meta ? '0'.substr($meta, 3) : null;
    }

    public function lookupVariants(?string $phone): array
    {
        $meta = $this->toMeta($phone);
        $local = $this->toLocal($phone);

        return array_values(array_unique(array_filter([
            $meta,
            $meta ? '+'.$meta : null,
            $local,
        ])));
    }
}

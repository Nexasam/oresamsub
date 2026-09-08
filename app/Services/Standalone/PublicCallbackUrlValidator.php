<?php

namespace App\Services\Standalone;

class PublicCallbackUrlValidator
{
    public function isSafe(string $url): bool
    {
        $parts = parse_url($url);
        if (! is_array($parts) || ($parts['scheme'] ?? '') !== 'https' || empty($parts['host']) || isset($parts['user']) || isset($parts['pass'])) {
            return false;
        }
        $host = strtolower($parts['host']);
        if ($host === 'localhost' || str_ends_with($host, '.local')) {
            return false;
        }
        $records = filter_var($host, FILTER_VALIDATE_IP) ? [] : (dns_get_record($host, DNS_A | DNS_AAAA) ?: []);
        $addresses = filter_var($host, FILTER_VALIDATE_IP) ? [$host] : array_values(array_filter(array_map(fn ($record) => $record['ip'] ?? $record['ipv6'] ?? null, $records)));
        if ($addresses === []) {
            return false;
        }
        foreach ($addresses as $address) {
            if (! filter_var($address, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                return false;
            }
        }

        return true;
    }
}

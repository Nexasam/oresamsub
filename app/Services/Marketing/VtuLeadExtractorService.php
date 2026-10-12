<?php

namespace App\Services\Marketing;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class VtuLeadExtractorService
{
    private const DEFAULT_PATHS = [
        '',
        '/index.php',
        '/contact',
        '/contact-us',
        '/contact.php',
        '/about',
        '/about-us',
        '/pricing',
        '/pricing-plan',
        '/pricing.php',
        '/services',
    ];

    private const SIGNALS = [
        'msorg',
        'vtulink',
        'brendytech',
        'vtu',
        'buy data',
        'data subscription',
        'airtime recharge',
        'airtime to cash',
        'cable subscription',
        'electricity',
        'exam pin',
        'recharge pin',
        'wallet funding',
        'reseller',
        'automated platform',
        'telecom services',
        'mtn',
        'airtel',
        'glo',
        '9mobile',
    ];

    public function extract(string $domainOrUrl): array
    {
        $baseUrl = $this->normalizeUrl($domainOrUrl);
        $domain = $this->domainFromUrl($baseUrl);
        $pages = $this->candidatePages($baseUrl);
        $documents = [];
        $errors = [];

        foreach ($pages as $pageUrl) {
            $response = $this->fetch($pageUrl);

            if (! $response['ok']) {
                $errors[] = $response['error'];
                continue;
            }

            $documents[] = [
                'url' => $pageUrl,
                'html' => $response['body'],
                'text' => $this->plainText($response['body']),
            ];

            if (count($documents) === 1) {
                $pages = array_values(array_unique(array_merge(
                    $pages,
                    $this->discoverUsefulLinks($baseUrl, $response['body'])
                )));
            }

            if (count($documents) >= 8) {
                break;
            }
        }

        $combinedHtml = implode("\n", array_column($documents, 'html'));
        $combinedText = implode("\n", array_column($documents, 'text'));
        $title = $this->extractTitle($combinedHtml) ?: Str::headline(str_replace(['-', '.com', '.ng'], [' ', '', ''], $domain));
        $emails = $this->extractEmails($combinedHtml.' '.$combinedText);
        $phones = $this->extractPhones($combinedHtml.' '.$combinedText);
        $whatsapp = $this->extractWhatsappLinks($combinedHtml);
        $signals = $this->matchedSignals($combinedText.' '.$combinedHtml);
        $score = $this->score($emails, $phones, $whatsapp, $signals, $combinedHtml);

        return [
            'domain' => $domain,
            'url' => $baseUrl,
            'company_name' => $title,
            'emails' => $emails,
            'phones' => $phones,
            'whatsapp_links' => $whatsapp,
            'signals' => $signals,
            'confidence_score' => $score,
            'confidence' => $this->confidenceLabel($score),
            'pages_checked' => array_column($documents, 'url'),
            'errors' => array_values(array_unique($errors)),
        ];
    }

    private function fetch(string $url): array
    {
        try {
            $response = Http::withHeaders([
                'User-Agent' => 'OresamSub lead research bot (+https://oresamsub.com)',
                'Accept' => 'text/html,application/xhtml+xml',
            ])
                ->connectTimeout(8)
                ->timeout(15)
                ->get($url);

            if (! $response->successful()) {
                return [
                    'ok' => false,
                    'body' => '',
                    'error' => $url.' returned HTTP '.$response->status(),
                ];
            }

            return [
                'ok' => true,
                'body' => (string) $response->body(),
                'error' => null,
            ];
        } catch (\Throwable $e) {
            return [
                'ok' => false,
                'body' => '',
                'error' => $url.' failed: '.$e->getMessage(),
            ];
        }
    }

    private function normalizeUrl(string $domainOrUrl): string
    {
        $value = trim($domainOrUrl);

        if (! str_starts_with($value, 'http://') && ! str_starts_with($value, 'https://')) {
            $value = 'https://'.$value;
        }

        return rtrim($value, '/');
    }

    private function domainFromUrl(string $url): string
    {
        return strtolower((string) parse_url($url, PHP_URL_HOST));
    }

    private function candidatePages(string $baseUrl): array
    {
        return array_map(
            fn (string $path): string => $baseUrl.$path,
            self::DEFAULT_PATHS
        );
    }

    private function discoverUsefulLinks(string $baseUrl, string $html): array
    {
        $links = [];
        $baseHost = $this->domainFromUrl($baseUrl);

        preg_match_all('/href=["\']([^"\']+)["\']/i', $html, $matches);

        foreach ($matches[1] ?? [] as $href) {
            $href = html_entity_decode((string) $href);

            if (! preg_match('/(contact|about|partner|api|developer|pricing|plan|service|data)/i', $href)) {
                continue;
            }

            if (str_starts_with($href, '#') || str_starts_with($href, 'mailto:') || str_starts_with($href, 'tel:')) {
                continue;
            }

            $url = $this->absoluteUrl($baseUrl, $href);

            if ($this->domainFromUrl($url) === $baseHost) {
                $links[] = rtrim($url, '/');
            }
        }

        return array_slice(array_values(array_unique($links)), 0, 5);
    }

    private function absoluteUrl(string $baseUrl, string $href): string
    {
        if (str_starts_with($href, 'http://') || str_starts_with($href, 'https://')) {
            return $href;
        }

        if (str_starts_with($href, '/')) {
            return $baseUrl.$href;
        }

        return $baseUrl.'/'.$href;
    }

    private function extractTitle(string $html): ?string
    {
        if (preg_match('/<title[^>]*>(.*?)<\/title>/is', $html, $match)) {
            return trim(preg_replace('/\s+/', ' ', html_entity_decode(strip_tags($match[1]))));
        }

        return null;
    }

    private function extractEmails(string $text): array
    {
        preg_match_all('/[A-Z0-9._%+\-]+@[A-Z0-9.\-]+\.[A-Z]{2,}/i', $text, $matches);

        return array_values(array_unique(array_map('strtolower', $matches[0] ?? [])));
    }

    private function extractPhones(string $text): array
    {
        preg_match_all('/(?:\+?234|0)[789][01]\d[\s\-.]?\d{3}[\s\-.]?\d{4}/', $text, $matches);

        $phones = [];

        foreach ($matches[0] ?? [] as $phone) {
            $digits = preg_replace('/\D/', '', $phone);

            if (str_starts_with($digits, '0')) {
                $digits = '234'.substr($digits, 1);
            }

            if (strlen($digits) === 13) {
                $phones[] = '+'.$digits;
            }
        }

        return array_values(array_unique($phones));
    }

    private function extractWhatsappLinks(string $html): array
    {
        preg_match_all('/https?:\/\/(?:wa\.me|api\.whatsapp\.com|chat\.whatsapp\.com)\/[^"\'<\s]+/i', $html, $matches);

        return array_values(array_unique($matches[0] ?? []));
    }

    private function matchedSignals(string $text): array
    {
        $haystack = strtolower($text);
        $signals = [];

        foreach (self::SIGNALS as $signal) {
            if (str_contains($haystack, $signal)) {
                $signals[] = $signal;
            }
        }

        return $signals;
    }

    private function plainText(string $html): string
    {
        $html = preg_replace('/<script\b[^>]*>.*?<\/script>/is', ' ', $html);
        $html = preg_replace('/<style\b[^>]*>.*?<\/style>/is', ' ', $html);

        return trim(preg_replace('/\s+/', ' ', html_entity_decode(strip_tags($html))));
    }

    private function score(array $emails, array $phones, array $whatsapp, array $signals, string $html): int
    {
        $score = 0;
        $score += min(count($signals) * 5, 45);
        $score += $emails ? 15 : 0;
        $score += $phones ? 20 : 0;
        $score += $whatsapp ? 15 : 0;
        $score += preg_match('/\/(login|register|signup|dashboard)/i', $html) ? 10 : 0;
        $score += preg_match('/(api|developer|partner)/i', $html) ? 10 : 0;

        return min($score, 100);
    }

    private function confidenceLabel(int $score): string
    {
        return match (true) {
            $score >= 75 => 'high',
            $score >= 45 => 'medium',
            default => 'low',
        };
    }
}

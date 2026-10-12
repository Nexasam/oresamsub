<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class DiscoverMsorgPlayApps extends Command
{
    protected $signature = 'marketing:discover-msorg-play-apps
        {--developer-url= : Google Play developer page}
        {--output=storage/app/private/marketing/msorg-play-apps.csv : CSV output path}
        {--domains=storage/app/private/marketing/vtu-lead-domains.txt : Domain input file to append discovered websites}
        {--append-domains : Append discovered app support websites into the VTU lead domain list}
        {--follow-related : Also inspect app links discovered inside app pages. Off by default to avoid unrelated Play recommendations}
        {--trusted-developer : Treat direct apps from this developer page as valid target leads even if app text does not mention MSORG}
        {--developer-name=MSORG : Label for the developer source in the notes}
        {--required-developer= : Comma-separated text that must appear on app pages, e.g. "A D E Developers,ADE DEVELOPERS INTERNATIONAL LIMITED"}
        {--limit=80 : Maximum app pages to inspect}
        {--delay=400 : Delay between Play requests in milliseconds}';

    protected $description = 'Discover MSORG-built apps on Google Play and extract public app support contacts/websites for marketing';

    public function handle(): int
    {
        $developerUrl = (string) ($this->option('developer-url') ?: 'https://play.google.com/store/apps/developer?id=MSORG+DEVELOPERS+LIMITED');
        $output = base_path((string) $this->option('output'));
        $domainFile = base_path((string) $this->option('domains'));
        $limit = max(1, (int) $this->option('limit'));
        $delay = max(0, (int) $this->option('delay'));
        $appendDomains = (bool) $this->option('append-domains');
        $followRelated = (bool) $this->option('follow-related');
        $trustedDeveloper = (bool) $this->option('trusted-developer');
        $developerName = (string) $this->option('developer-name');
        $requiredDeveloperText = $this->requiredDeveloperText();

        File::ensureDirectoryExists(dirname($output));
        File::ensureDirectoryExists(dirname($domainFile));

        $queue = $this->appLinksFromPage($developerUrl);
        $seen = [];
        $rows = [];

        $this->components->info('Discovered '.count($queue).' initial app link(s) from developer page.');

        while ($queue !== [] && count($rows) < $limit) {
            $appUrl = array_shift($queue);

            if (isset($seen[$appUrl])) {
                continue;
            }

            $seen[$appUrl] = true;
            $this->line((count($rows) + 1).'. '.$appUrl);

            $html = $this->fetch($appUrl);

            if ($html === null) {
                $rows[] = [
                    'app_name' => '',
                    'app_url' => $appUrl,
                    'package' => $this->packageFromUrl($appUrl),
                    'downloads' => '',
                    'website' => '',
                    'website_domain' => '',
                    'support_email' => '',
                    'phones' => '',
                    'developer_email' => '',
                    'developer_phone' => '',
                    'notes' => 'fetch_failed',
                ];

                continue;
            }

            if ($requiredDeveloperText !== [] && ! $this->containsAny($html, $requiredDeveloperText)) {
                $this->line('   skipped: developer text did not match');

                continue;
            }

            if ($followRelated) {
                foreach ($this->appLinksFromHtml($html) as $link) {
                    if (! isset($seen[$link]) && count($queue) + count($rows) < $limit * 3) {
                        $queue[] = $link;
                    }
                }
            }

            $row = $this->extractApp($appUrl, $html, $trustedDeveloper, $developerName);
            $rows[] = $row;

            $this->line('   '.($row['app_name'] ?: $row['package']).' · '
                .($row['downloads'] ?: 'downloads?').' · '
                .($row['website_domain'] ?: 'no website').' · '
                .($row['support_email'] ?: 'no email'));

            if ($delay > 0 && count($rows) < $limit) {
                usleep($delay * 1000);
            }
        }

        $this->writeCsv($output, $rows);

        if ($appendDomains) {
            $added = $this->appendDomains($domainFile, $rows);
            $this->components->info("Added {$added} new domain(s) to {$domainFile}");
        }

        $this->components->info('Play app CSV saved to: '.$output);

        $this->table([
            'App',
            'Downloads',
            'Domain',
            'Email',
            'Phone',
        ], array_map(fn (array $row): array => [
            Str::limit($row['app_name'] ?: $row['package'], 32),
            $row['downloads'],
            $row['website_domain'],
            $row['support_email'],
            $row['phones'],
        ], array_slice($rows, 0, 30)));

        return self::SUCCESS;
    }

    private function appLinksFromPage(string $url): array
    {
        $html = $this->fetch($url);

        if ($html === null) {
            return [];
        }

        return $this->appLinksFromHtml($html);
    }

    private function requiredDeveloperText(): array
    {
        $value = trim((string) $this->option('required-developer'));

        if ($value === '') {
            return [];
        }

        return array_values(array_filter(array_map(
            fn (string $item): string => strtolower(trim($item)),
            explode(',', $value)
        )));
    }

    private function containsAny(string $html, array $needles): bool
    {
        $haystack = strtolower(html_entity_decode(strip_tags($html)));

        foreach ($needles as $needle) {
            if ($needle !== '' && str_contains($haystack, $needle)) {
                return true;
            }
        }

        return false;
    }

    private function fetch(string $url): ?string
    {
        try {
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 OresamSub lead research',
                'Accept' => 'text/html,application/xhtml+xml',
            ])
                ->connectTimeout(10)
                ->timeout(20)
                ->get($url);

            if (! $response->successful()) {
                $this->components->warn($url.' returned HTTP '.$response->status());

                return null;
            }

            return (string) $response->body();
        } catch (\Throwable $e) {
            $this->components->warn($url.' failed: '.$e->getMessage());

            return null;
        }
    }

    private function appLinksFromHtml(string $html): array
    {
        preg_match_all('/(?:https:\/\/play\.google\.com)?\/store\/apps\/details\?id=([a-zA-Z0-9._]+)/', $html, $matches);
        $links = [];

        foreach ($matches[1] ?? [] as $package) {
            $links[] = 'https://play.google.com/store/apps/details?id='.$package;
        }

        return array_values(array_unique($links));
    }

    private function extractApp(string $appUrl, string $html, bool $trustedDeveloper, string $developerName): array
    {
        $text = $this->plainText($html);
        $externalUrls = $this->externalUrls($html);
        $website = $this->bestWebsite($externalUrls);
        $emails = $this->emails($text.' '.$html);
        $phones = $this->phones($text);

        return [
            'app_name' => $this->appName($html),
            'app_url' => $appUrl,
            'package' => $this->packageFromUrl($appUrl),
            'downloads' => $this->downloads($text),
            'website' => $website,
            'website_domain' => $website ? strtolower((string) parse_url($website, PHP_URL_HOST)) : '',
            'support_email' => $emails[0] ?? '',
            'phones' => implode(' | ', $phones),
            'developer_email' => $this->firstDeveloperEmail($emails),
            'developer_phone' => $this->firstDeveloperPhone($phones),
            'notes' => $this->notes($text, $appUrl, $trustedDeveloper, $developerName),
        ];
    }

    private function notes(string $text, string $appUrl, bool $trustedDeveloper, string $developerName): string
    {
        $notes = [];
        $lower = strtolower($text.' '.$appUrl);

        if ($trustedDeveloper) {
            $notes[] = 'trusted_developer:'.Str::slug($developerName ?: 'developer');
        }

        if (str_contains($lower, 'msorg developers') || str_contains($lower, 'msorgdevelopers') || str_contains($lower, 'msorg.')) {
            $notes[] = 'msorg_app';
        }

        if (str_contains($lower, 'vtu')) {
            $notes[] = 'vtu_app';
        }

        return implode('|', $notes);
    }

    private function appName(string $html): string
    {
        if (preg_match('/<h1[^>]*>\s*<span[^>]*>(.*?)<\/span>\s*<\/h1>/is', $html, $match)) {
            return $this->clean($match[1]);
        }

        if (preg_match('/<h1[^>]*>(.*?)<\/h1>/is', $html, $match)) {
            return $this->clean($match[1]);
        }

        if (preg_match('/<title[^>]*>(.*?)<\/title>/is', $html, $match)) {
            return Str::before($this->clean($match[1]), ' - ');
        }

        return '';
    }

    private function downloads(string $text): string
    {
        if (preg_match('/\b(\d+(?:\.\d+)?[KMB]?\+)\s+Downloads\b/i', $text, $match)) {
            return strtoupper($match[1]);
        }

        return '';
    }

    private function externalUrls(string $html): array
    {
        preg_match_all('/href=["\']([^"\']+)["\']/i', $html, $matches);
        $urls = [];

        foreach ($matches[1] ?? [] as $href) {
            $url = html_entity_decode((string) $href);

            if (! str_starts_with($url, 'http')) {
                continue;
            }

            $host = strtolower((string) parse_url($url, PHP_URL_HOST));

            if ($host === '' || str_contains($host, 'google.') || str_contains($host, 'gstatic.') || str_contains($host, 'play.google.')) {
                continue;
            }

            $urls[] = strtok($url, '?') ?: $url;
        }

        return array_values(array_unique($urls));
    }

    private function bestWebsite(array $urls): string
    {
        foreach ($urls as $url) {
            if (! preg_match('/privacy|policy|terms|support/i', $url)) {
                return $url;
            }
        }

        return $urls[0] ?? '';
    }

    private function emails(string $text): array
    {
        preg_match_all('/[A-Z0-9._%+\-]+@[A-Z0-9.\-]+\.[A-Z]{2,}/i', $text, $matches);

        return array_values(array_unique(array_map('strtolower', $matches[0] ?? [])));
    }

    private function phones(string $text): array
    {
        preg_match_all('/(?:\+?234|0)[\s\-]?[789][01]\d[\s\-]?\d{3}[\s\-]?\d{4}/', $text, $matches);
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

    private function firstDeveloperEmail(array $emails): string
    {
        foreach ($emails as $email) {
            if (str_contains($email, 'msorgdevelopers.com')) {
                return $email;
            }
        }

        return '';
    }

    private function firstDeveloperPhone(array $phones): string
    {
        return $phones[1] ?? '';
    }

    private function packageFromUrl(string $url): string
    {
        parse_str((string) parse_url($url, PHP_URL_QUERY), $query);

        return (string) ($query['id'] ?? '');
    }

    private function plainText(string $html): string
    {
        return trim(preg_replace('/\s+/', ' ', html_entity_decode(strip_tags($html))));
    }

    private function clean(string $value): string
    {
        return trim(preg_replace('/\s+/', ' ', html_entity_decode(strip_tags($value))));
    }

    private function appendDomains(string $path, array $rows): int
    {
        $existing = File::exists($path)
            ? file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES)
            : [];

        $existing = array_map(fn (string $line): string => strtolower(trim($line)), $existing ?: []);
        $existingSet = array_fill_keys($existing, true);
        $toAdd = [];

        foreach ($rows as $row) {
            $domain = strtolower(trim((string) $row['website_domain']));

            if (
                $domain === ''
                || isset($existingSet[$domain])
                || (
                    ! str_contains((string) $row['notes'], 'msorg_app')
                    && ! str_contains((string) $row['notes'], 'trusted_developer:')
                )
            ) {
                continue;
            }

            $existingSet[$domain] = true;
            $toAdd[] = $domain;
        }

        if ($toAdd !== []) {
            File::append($path, PHP_EOL.implode(PHP_EOL, $toAdd).PHP_EOL);
        }

        return count($toAdd);
    }

    private function writeCsv(string $path, array $rows): void
    {
        $handle = fopen($path, 'w');

        fputcsv($handle, [
            'app_name',
            'app_url',
            'package',
            'downloads',
            'website',
            'website_domain',
            'support_email',
            'phones',
            'developer_email',
            'developer_phone',
            'notes',
        ]);

        foreach ($rows as $row) {
            fputcsv($handle, $row);
        }

        fclose($handle);
    }
}

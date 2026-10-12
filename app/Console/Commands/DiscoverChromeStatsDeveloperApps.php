<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class DiscoverChromeStatsDeveloperApps extends Command
{
    protected $signature = 'marketing:discover-chrome-stats-apps
        {--developer=A D E Developers : Developer name to match}
        {--output=storage/app/private/marketing/adex-chrome-stats-apps.csv : CSV output path}
        {--domains=storage/app/private/marketing/vtu-lead-domains.txt : Domain input file to append discovered websites}
        {--append-domains : Append discovered app websites into the VTU lead domain list}
        {--limit=140 : Maximum matching apps to keep}
        {--max-pages=260 : Maximum Chrome-Stats pages to inspect}
        {--delay=150 : Delay between requests in milliseconds}';

    protected $description = 'Discover developer apps from Chrome-Stats public pages and extract public website/domain leads';

    private const ADEX_SEEDS = [
        'https://chrome-stats.com/d/com.erecharge.ng',
        'https://chrome-stats.com/d/com.advancesub',
        'https://chrome-stats.com/d/com.mydatajoy',
        'https://chrome-stats.com/d/com.dataxpress',
        'https://chrome-stats.com/d/com.a2zdataplug',
        'https://chrome-stats.com/d/com.thesubexpert',
        'https://chrome-stats.com/d/com.isahmobiledata',
        'https://chrome-stats.com/d/com.slick_g',
        'https://chrome-stats.com/d/com.inyassdata',
        'https://chrome-stats.com/d/com.subflex_ade',
        'https://chrome-stats.com/d/com.easysub_g',
        'https://chrome-stats.com/d/com.egobelldata_ade',
        'https://chrome-stats.com/d/com.alruhsadata',
        'https://chrome-stats.com/d/com.assudanglobalnetworks',
        'https://chrome-stats.com/d/com.Pamcitydata_tm',
        'https://chrome-stats.com/d/com.mydatajet',
        'https://chrome-stats.com/d/com.subvas.com',
    ];

    public function handle(): int
    {
        $developer = strtolower(trim((string) $this->option('developer')));
        $output = base_path((string) $this->option('output'));
        $domainFile = base_path((string) $this->option('domains'));
        $limit = max(1, (int) $this->option('limit'));
        $maxPages = max($limit, (int) $this->option('max-pages'));
        $delay = max(0, (int) $this->option('delay'));

        $queue = self::ADEX_SEEDS;
        $seen = [];
        $rows = [];

        File::ensureDirectoryExists(dirname($output));
        File::ensureDirectoryExists(dirname($domainFile));

        while ($queue !== [] && count($seen) < $maxPages && count($rows) < $limit) {
            $url = array_shift($queue);

            if (isset($seen[$url])) {
                continue;
            }

            $seen[$url] = true;
            $html = $this->fetch($url);

            if ($html === null) {
                continue;
            }

            foreach ($this->chromeStatsLinks($html) as $link) {
                if (! isset($seen[$link]) && count($queue) < $maxPages * 2) {
                    $queue[] = $link;
                }
            }

            $text = $this->plainText($html);

            if (! $this->matchesDeveloper($text, $developer)) {
                continue;
            }

            $row = $this->extractRow($url, $html, $text);
            $rows[$row['package'] ?: $url] = $row;

            $this->line(count($rows).'. '.$row['app_name'].' · '.$row['website_domain']);

            if ($delay > 0) {
                usleep($delay * 1000);
            }
        }

        $rows = array_values($rows);
        $this->writeCsv($output, $rows);

        if ((bool) $this->option('append-domains')) {
            $added = $this->appendDomains($domainFile, $rows);
            $this->components->info("Added {$added} domain(s) to {$domainFile}");
        }

        $this->components->info('Chrome-Stats CSV saved to: '.$output);
        $this->table([
            'App',
            'Package',
            'Domain',
            'Website',
        ], array_map(fn (array $row): array => [
            Str::limit($row['app_name'], 28),
            Str::limit($row['package'], 30),
            $row['website_domain'],
            Str::limit($row['website'], 34),
        ], array_slice($rows, 0, 40)));

        return self::SUCCESS;
    }

    private function fetch(string $url): ?string
    {
        try {
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 OresamSub lead research',
                'Accept' => 'text/html,application/xhtml+xml',
            ])->connectTimeout(8)->timeout(15)->get($url);

            return $response->successful() ? (string) $response->body() : null;
        } catch (\Throwable) {
            return null;
        }
    }

    private function chromeStatsLinks(string $html): array
    {
        preg_match_all('/href=["\'](\/d\/[a-zA-Z0-9._\-]+(?:\?[^"\']*)?)["\']/i', $html, $matches);
        $links = [];

        foreach ($matches[1] ?? [] as $path) {
            $links[] = 'https://chrome-stats.com'.strtok($path, '?');
        }

        return array_values(array_unique($links));
    }

    private function matchesDeveloper(string $text, string $developer): bool
    {
        $lower = strtolower($text);

        return str_contains($lower, 'by: '.$developer)
            || str_contains($lower, 'developed by: '.$developer)
            || preg_match('/developed by:\s*'.preg_quote($developer, '/').'/i', $text);
    }

    private function extractRow(string $url, string $html, string $text): array
    {
        $appName = $this->title($html) ?: Str::headline($this->packageFromChromeStatsUrl($url));
        $package = $this->package($text) ?: $this->packageFromChromeStatsUrl($url);
        $website = $this->website($text, $html);
        $domain = $website ? strtolower((string) parse_url($website, PHP_URL_HOST)) : '';

        return [
            'app_name' => $appName,
            'chrome_stats_url' => $url,
            'app_url' => $package ? 'https://play.google.com/store/apps/details?id='.$package : '',
            'package' => $package,
            'downloads' => $this->downloads($text),
            'website' => $website,
            'website_domain' => $domain,
            'support_email' => $this->email($text),
            'phones' => '',
            'developer_email' => '',
            'developer_phone' => '',
            'notes' => 'trusted_developer:adex|chrome_stats',
        ];
    }

    private function title(string $html): string
    {
        if (preg_match('/<h1[^>]*>(.*?)<\/h1>/is', $html, $match)) {
            return $this->clean($match[1]);
        }

        if (preg_match('/<title[^>]*>(.*?)<\/title>/is', $html, $match)) {
            return Str::before($this->clean($match[1]), ' Stats');
        }

        return '';
    }

    private function package(string $text): string
    {
        if (preg_match('/play\.google\.com\/store\/apps\/details\?id=([a-zA-Z0-9._]+)/', $text, $match)) {
            return $match[1];
        }

        return '';
    }

    private function packageFromChromeStatsUrl(string $url): string
    {
        return basename((string) parse_url($url, PHP_URL_PATH));
    }

    private function downloads(string $text): string
    {
        if (preg_match('/Downloads:\s*([0-9,.KMBkmb+\s]+)/', $text, $match)) {
            return trim($match[1]);
        }

        return '';
    }

    private function website(string $text, string $html): string
    {
        if (preg_match('/Website:\s*(https?:\/\/[^\s]+)/i', $text, $match)) {
            return rtrim($match[1], '.,)');
        }

        preg_match_all('/https?:\/\/[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}(?:\/[^\s"\']*)?/i', $html.' '.$text, $matches);

        foreach ($matches[0] ?? [] as $url) {
            $host = strtolower((string) parse_url($url, PHP_URL_HOST));

            if ($host !== '' && ! str_contains($host, 'google.') && ! str_contains($host, 'chrome-stats.com')) {
                return strtok($url, '?') ?: $url;
            }
        }

        return '';
    }

    private function email(string $text): string
    {
        preg_match_all('/[A-Z0-9._%+\-*]+@[A-Z0-9.\-]+\.[A-Z]{2,}/i', $text, $matches);

        foreach ($matches[0] ?? [] as $email) {
            if (! str_contains($email, '*')) {
                return strtolower($email);
            }
        }

        return strtolower($matches[0][0] ?? '');
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
        $set = array_fill_keys(array_map(fn ($line) => strtolower(trim((string) $line)), $existing ?: []), true);
        $new = [];

        foreach ($rows as $row) {
            $domain = strtolower(trim((string) $row['website_domain']));

            if ($domain !== '' && ! isset($set[$domain])) {
                $set[$domain] = true;
                $new[] = $domain;
            }
        }

        if ($new !== []) {
            File::append($path, PHP_EOL.implode(PHP_EOL, $new).PHP_EOL);
        }

        return count($new);
    }

    private function writeCsv(string $path, array $rows): void
    {
        $handle = fopen($path, 'w');
        $headers = [
            'app_name',
            'chrome_stats_url',
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
        ];

        fputcsv($handle, $headers);

        foreach ($rows as $row) {
            fputcsv($handle, array_map(fn ($header) => $row[$header] ?? '', $headers));
        }

        fclose($handle);
    }
}

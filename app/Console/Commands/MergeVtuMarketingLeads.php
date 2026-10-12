<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MergeVtuMarketingLeads extends Command
{
    protected $signature = 'marketing:merge-vtu-leads
        {--website=storage/app/private/marketing/vtu-leads.csv : Website extractor CSV}
        {--play=storage/app/private/marketing/msorg-play-apps.csv : Play Store extractor CSV}
        {--extra-play=* : Extra Play Store extractor CSV path(s)}
        {--output=storage/app/private/marketing/vtu-master-outreach-leads.csv : Merged CSV output}';

    protected $description = 'Merge website-extracted VTU leads and MSORG Play Store app leads into one outreach CSV';

    public function handle(): int
    {
        $websitePath = base_path((string) $this->option('website'));
        $playPath = base_path((string) $this->option('play'));
        $output = base_path((string) $this->option('output'));

        if (! File::exists($websitePath) && ! File::exists($playPath)) {
            $this->components->error('No source CSV files found.');

            return self::FAILURE;
        }

        $leads = [];

        foreach ($this->readCsv($websitePath) as $row) {
            $domain = strtolower(trim((string) ($row['domain'] ?? '')));

            if ($domain === '') {
                continue;
            }

            $leads[$domain] = [
                'source' => 'website',
                'domain' => $domain,
                'company_name' => $row['company_name'] ?? '',
                'app_name' => '',
                'website' => $row['url'] ?? '',
                'phones' => $row['phones'] ?? '',
                'emails' => $row['emails'] ?? '',
                'whatsapp_links' => $row['whatsapp_links'] ?? '',
                'confidence' => $row['confidence'] ?? '',
                'confidence_score' => $row['confidence_score'] ?? '',
                'signals' => $row['signals'] ?? '',
                'play_app_url' => '',
                'package' => '',
                'notes' => '',
            ];
        }

        $playPaths = array_merge([$playPath], array_map(
            fn (string $path): string => base_path($path),
            (array) $this->option('extra-play')
        ));

        foreach ($playPaths as $currentPlayPath) {
            foreach ($this->readCsv($currentPlayPath) as $row) {
            $domain = strtolower(trim((string) ($row['website_domain'] ?? $row['domain'] ?? '')));

            if ($domain === '') {
                continue;
            }

            $existing = $leads[$domain] ?? null;

            if (
                ! $existing
                && ! str_contains((string) ($row['notes'] ?? ''), 'msorg_app')
                && ! str_contains((string) ($row['notes'] ?? ''), 'trusted_developer:')
            ) {
                continue;
            }

            $leads[$domain] = [
                'source' => $existing ? $this->mergeList($existing['source'] ?? '', 'play') : ($row['source'] ?? 'play'),
                'domain' => $domain,
                'company_name' => $existing['company_name'] ?? ($row['app_name'] ?? ''),
                'app_name' => $row['app_name'] ?? '',
                'website' => $existing['website'] ?? ($row['website'] ?? ''),
                'phones' => $this->mergeList($existing['phones'] ?? '', $row['phones'] ?? ''),
                'emails' => $this->mergeList($existing['emails'] ?? '', $row['support_email'] ?? ''),
                'whatsapp_links' => $existing['whatsapp_links'] ?? '',
                'confidence' => $existing['confidence'] ?? 'play',
                'confidence_score' => $existing['confidence_score'] ?? '',
                'signals' => $existing['signals'] ?? '',
                'play_app_url' => $row['app_url'] ?? '',
                'package' => $row['package'] ?? '',
                'notes' => trim(($existing['notes'] ?? '').' '.($row['notes'] ?? '')),
            ];
        }
        }

        File::ensureDirectoryExists(dirname($output));
        $this->writeCsv($output, array_values($leads));

        $this->components->info('Merged '.count($leads).' lead(s) into: '.$output);
        $this->table([
            'Domain',
            'Source',
            'Name',
            'Phones',
            'Emails',
        ], array_map(fn (array $lead): array => [
            $lead['domain'],
            $lead['source'],
            Str::limit($lead['company_name'] ?: $lead['app_name'], 30),
            Str::limit($lead['phones'], 34),
            Str::limit($lead['emails'], 34),
        ], array_slice(array_values($leads), 0, 30)));

        return self::SUCCESS;
    }

    private function readCsv(string $path): array
    {
        if (! File::exists($path)) {
            return [];
        }

        $handle = fopen($path, 'r');
        $headers = fgetcsv($handle);
        $rows = [];

        if (! is_array($headers)) {
            fclose($handle);

            return [];
        }

        while (($data = fgetcsv($handle)) !== false) {
            $rows[] = array_combine($headers, array_pad($data, count($headers), ''));
        }

        fclose($handle);

        return $rows;
    }

    private function mergeList(string $left, string $right): string
    {
        $items = [];

        foreach ([$left, $right] as $value) {
            foreach (preg_split('/\s*\|\s*/', $value) ?: [] as $item) {
                $item = trim($item);

                if ($item !== '') {
                    $items[strtolower($item)] = $item;
                }
            }
        }

        return implode(' | ', array_values($items));
    }

    private function writeCsv(string $path, array $rows): void
    {
        $handle = fopen($path, 'w');
        $headers = [
            'source',
            'domain',
            'company_name',
            'app_name',
            'website',
            'phones',
            'emails',
            'whatsapp_links',
            'confidence',
            'confidence_score',
            'signals',
            'play_app_url',
            'package',
            'notes',
        ];

        fputcsv($handle, $headers);

        foreach ($rows as $row) {
            fputcsv($handle, array_map(fn (string $header) => $row[$header] ?? '', $headers));
        }

        fclose($handle);
    }
}

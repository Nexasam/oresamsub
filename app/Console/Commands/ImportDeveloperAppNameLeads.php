<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ImportDeveloperAppNameLeads extends Command
{
    protected $signature = 'marketing:import-developer-app-names
        {input : Text file copied from Google Play "More by developer"}
        {--developer=ADEX : Developer/source label}
        {--output=storage/app/private/marketing/adex-app-name-leads.csv : CSV output path}
        {--domains=storage/app/private/marketing/vtu-lead-domains.txt : Domain input file to append guessed domains}
        {--append-domains : Append guessed domains into the VTU lead domain list}';

    protected $description = 'Import app names copied from a Play developer page and turn them into marketing seed leads';

    public function handle(): int
    {
        $input = (string) $this->argument('input');
        $developer = strtoupper(trim((string) $this->option('developer')));
        $output = base_path((string) $this->option('output'));
        $domainFile = base_path((string) $this->option('domains'));

        if (! File::exists($input)) {
            $this->components->error('Input file not found: '.$input);

            return self::FAILURE;
        }

        $apps = $this->parseAppNames(File::get($input), $developer);
        $rows = array_map(fn (string $app): array => $this->row($app, $developer), $apps);

        File::ensureDirectoryExists(dirname($output));
        $this->writeCsv($output, $rows);

        if ((bool) $this->option('append-domains')) {
            $added = $this->appendDomains($domainFile, $rows);
            $this->components->info("Added {$added} guessed domain(s) to {$domainFile}");
        }

        $this->components->info('Imported '.count($rows).' app-name lead seed(s) into: '.$output);
        $this->table([
            'App',
            'Guessed domain',
        ], array_map(fn (array $row): array => [
            Str::limit($row['app_name'], 40),
            $row['domain'],
        ], array_slice($rows, 0, 50)));

        return self::SUCCESS;
    }

    private function parseAppNames(string $text, string $developer): array
    {
        $lines = preg_split('/\R/', $text) ?: [];
        $apps = [];

        foreach ($lines as $line) {
            $line = trim($line);
            $lowerLine = strtolower($line);
            $developerName = strtolower(str_replace('-', ' ', $developer));

            if ($line === ''
                || $line === 'More by A D E Developers'
                || $lowerLine === 'more by a d e developers'
                || $lowerLine === 'cdlp hub ltd'
                || $lowerLine === $developerName
                || $lowerLine === 'star'
                || $lowerLine === 'screenshot image'
                || $lowerLine === 'thumbnail image'
                || preg_match('/^\d+(?:\.\d+)?$/', $line)
            ) {
                continue;
            }

            if (str_starts_with($line, 'Icon image ')) {
                $line = trim(substr($line, strlen('Icon image ')));
            }

            $line = trim(str_replace(['Screenshot image', 'Thumbnail image'], '', $line));

            if ($line === '' || preg_match('/^\d+(?:\.\d+)?$/', $line)) {
                continue;
            }

            $apps[$this->normalizeAppKey($line)] = $line;
        }

        return array_values($apps);
    }

    private function row(string $appName, string $developer): array
    {
        $domain = $this->guessDomain($appName);

        return [
            'source' => 'app_name_seed',
            'domain' => $domain,
            'company_name' => $appName,
            'app_name' => $appName,
            'website' => $domain ? 'https://'.$domain : '',
            'phones' => '',
            'emails' => '',
            'whatsapp_links' => '',
            'confidence' => 'seed',
            'confidence_score' => '',
            'signals' => 'play app name',
            'play_app_url' => '',
            'package' => '',
            'notes' => 'trusted_developer:'.Str::slug($developer).'|app_name_seed',
        ];
    }

    private function guessDomain(string $appName): string
    {
        $name = strtolower($appName);
        $name = preg_replace('/[:|].*$/', '', $name);
        $name = preg_replace('/\b(cheap|data|airtime|bills?|bill|payment|wallet|sub|vtu|and|ng|pro|hub)\b/i', '', $name);
        $name = preg_replace('/[^a-z0-9]+/', '', (string) $name);

        if ($name === '') {
            return '';
        }

        return $name.'.com';
    }

    private function normalizeAppKey(string $appName): string
    {
        return strtolower(preg_replace('/\s+/', ' ', trim($appName)));
    }

    private function appendDomains(string $path, array $rows): int
    {
        File::ensureDirectoryExists(dirname($path));
        $existing = File::exists($path)
            ? file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES)
            : [];
        $set = array_fill_keys(array_map(fn ($line) => strtolower(trim((string) $line)), $existing ?: []), true);
        $new = [];

        foreach ($rows as $row) {
            $domain = strtolower(trim((string) $row['domain']));

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

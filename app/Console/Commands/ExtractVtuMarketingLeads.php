<?php

namespace App\Console\Commands;

use App\Services\Marketing\VtuLeadExtractorService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ExtractVtuMarketingLeads extends Command
{
    protected $signature = 'marketing:extract-vtu-leads
        {input? : Newline-separated file of domains or URLs}
        {--output=storage/app/private/marketing/vtu-leads.csv : CSV output path}
        {--json=storage/app/private/marketing/vtu-leads.json : JSON output path}
        {--limit=0 : Maximum domains to process. 0 means all}
        {--delay=500 : Delay between sites in milliseconds}';

    protected $description = 'Extract public contact and confidence details from VTU/MSORG-like websites for API outreach';

    public function handle(VtuLeadExtractorService $extractor): int
    {
        $input = (string) ($this->argument('input') ?: storage_path('app/private/marketing/vtu-lead-domains.txt'));
        $output = base_path((string) $this->option('output'));
        $jsonOutput = base_path((string) $this->option('json'));
        $limit = max(0, (int) $this->option('limit'));
        $delay = max(0, (int) $this->option('delay'));

        if (! File::exists($input)) {
            $this->createTemplateInput($input);
            $this->components->warn("Input file was created at {$input}");
            $this->line('Add one domain/URL per line, then run this command again.');

            return self::SUCCESS;
        }

        $domains = $this->readDomains($input);

        if ($limit > 0) {
            $domains = array_slice($domains, 0, $limit);
        }

        if ($domains === []) {
            $this->components->error('No domains found in input file.');

            return self::FAILURE;
        }

        File::ensureDirectoryExists(dirname($output));
        File::ensureDirectoryExists(dirname($jsonOutput));

        $rows = [];
        $this->components->info('Extracting '.count($domains).' lead(s)...');

        foreach ($domains as $index => $domain) {
            $this->line(($index + 1).'. '.$domain);

            $lead = $extractor->extract($domain);
            $rows[] = $lead;

            $this->line('   '.$lead['confidence'].' · '.$lead['confidence_score'].'/100 · '
                .count($lead['phones']).' phone(s), '.count($lead['emails']).' email(s)');

            if ($delay > 0 && $index < count($domains) - 1) {
                usleep($delay * 1000);
            }
        }

        $this->writeCsv($output, $rows);
        File::put($jsonOutput, json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        $this->newLine();
        $this->components->info('CSV saved to: '.$output);
        $this->components->info('JSON saved to: '.$jsonOutput);

        $this->table([
            'Domain',
            'Name',
            'Confidence',
            'Phones',
            'Emails',
            'Signals',
        ], array_map(fn (array $row): array => [
            $row['domain'],
            Str::limit($row['company_name'], 34),
            $row['confidence'].' '.$row['confidence_score'],
            implode(', ', array_slice($row['phones'], 0, 2)),
            implode(', ', array_slice($row['emails'], 0, 2)),
            implode(', ', array_slice($row['signals'], 0, 4)),
        ], $rows));

        return self::SUCCESS;
    }

    private function createTemplateInput(string $path): void
    {
        File::ensureDirectoryExists(dirname($path));

        File::put($path, implode(PHP_EOL, [
            '# Add one VTU/MSORG-like website per line.',
            '# Examples:',
            'datanova.com.ng',
            '# examplevtu.com',
            '',
        ]));
    }

    private function readDomains(string $input): array
    {
        $lines = file($input, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
        $domains = [];

        foreach ($lines as $line) {
            $line = trim($line);

            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            $domains[] = $line;
        }

        return array_values(array_unique($domains));
    }

    private function writeCsv(string $path, array $rows): void
    {
        $handle = fopen($path, 'w');

        fputcsv($handle, [
            'domain',
            'url',
            'company_name',
            'phones',
            'emails',
            'whatsapp_links',
            'confidence',
            'confidence_score',
            'signals',
            'pages_checked',
            'errors',
        ]);

        foreach ($rows as $row) {
            fputcsv($handle, [
                $row['domain'],
                $row['url'],
                $row['company_name'],
                implode(' | ', $row['phones']),
                implode(' | ', $row['emails']),
                implode(' | ', $row['whatsapp_links']),
                $row['confidence'],
                $row['confidence_score'],
                implode(' | ', $row['signals']),
                implode(' | ', $row['pages_checked']),
                implode(' | ', $row['errors']),
            ]);
        }

        fclose($handle);
    }
}

<?php

namespace App\Console\Commands;

use App\Services\ProductPlans\AirtelGiftingPlanImportService;
use Illuminate\Console\Command;

class ImportAirtelGiftingPlans extends Command
{
    protected $signature = 'plans:import-airtel-gifting
        {--execute : Write the import to the database. Omit for preview only}
        {--update-existing : Update matching existing plans and provider rows}
        {--category=Airtel Gifting : Product plan category name to use/create}
        {--source-slug=oresamplug : Automation/source slug to resolve}
        {--source-name=ORESAMPLUG AUTOMATION : Automation/source name fallback}';

    protected $description = 'Preview or import Airtel giftable data plans for the Oresamplug automation source';

    public function handle(AirtelGiftingPlanImportService $importer): int
    {
        $execute = (bool) $this->option('execute');
        $options = [
            'update_existing' => (bool) $this->option('update-existing'),
            'category' => (string) $this->option('category'),
            'source_slug' => (string) $this->option('source-slug'),
            'source_name' => (string) $this->option('source-name'),
        ];

        $result = $execute
            ? $importer->execute($importer->defaultText(), $options)
            : $importer->preview($importer->defaultText(), $options);

        foreach ($result['errors'] as $error) {
            $this->components->error($error);
        }

        foreach ($result['warnings'] as $warning) {
            $this->components->warn($warning);
        }

        foreach ($result['parse_errors'] as $error) {
            $this->components->error($error);
        }

        if ($result['errors'] || $result['parse_errors']) {
            return self::FAILURE;
        }

        $this->line($execute ? 'Airtel gifting import execution' : 'Airtel gifting import preview');
        $this->line('Network: '.($result['dependencies']['network'] ?? 'missing')
            .' | Product: '.($result['dependencies']['product'] ?? 'missing')
            .' | Category: '.($result['dependencies']['category'] ?? 'missing')
            .' | Source: '.($result['dependencies']['automation'] ?? 'missing'));
        $this->newLine();

        $this->table([
            'Plan',
            'Public ID',
            'Airtel Code',
            'Original',
            'L1',
            'L2',
            'L3',
            'L4',
            'L5',
            'L6',
            'Status',
            'Reason',
        ], array_map(fn (array $row): array => [
            $row['name'],
            $row['public_id'],
            $row['code'],
            $this->money($row['price']),
            $this->money($row['level_prices'][1]),
            $this->money($row['level_prices'][2]),
            $this->money($row['level_prices'][3]),
            $this->money($row['level_prices'][4]),
            $this->money($row['level_prices'][5]),
            $this->money($row['level_prices'][6]),
            $row['status'],
            $row['reason'],
        ], $result['rows']));

        if (! $execute) {
            $this->newLine();
            $this->components->info('Preview only. Re-run with --execute to import. Add --update-existing only if existing matching plans should be overwritten.');

            return self::SUCCESS;
        }

        $summary = $result['summary'];
        $this->newLine();
        $this->components->info("Created: {$summary['created']}; updated: {$summary['updated']}; skipped existing: {$summary['skipped_existing']}; invalid/skipped: {$summary['invalid']}.");

        return $summary['invalid'] > 0 ? self::FAILURE : self::SUCCESS;
    }

    private function money(float|int|string $value): string
    {
        return number_format((float) $value, 2, '.', '');
    }
}

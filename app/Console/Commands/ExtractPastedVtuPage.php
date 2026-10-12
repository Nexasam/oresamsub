<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ExtractPastedVtuPage extends Command
{
    protected $signature = 'marketing:extract-pasted-vtu-page
        {input : Text file pasted/copied from a VTU website page}
        {--lead-output=storage/app/private/marketing/pasted-vtu-leads.csv : Contact lead CSV output path}
        {--pricing-output=storage/app/private/marketing/pasted-vtu-pricing.csv : Pricing CSV output path}';

    protected $description = 'Extract contacts and visible data pricing from pasted VTU website page text';

    public function handle(): int
    {
        $input = (string) $this->argument('input');
        $leadOutput = base_path((string) $this->option('lead-output'));
        $pricingOutput = base_path((string) $this->option('pricing-output'));

        if (! File::exists($input)) {
            $this->components->error('Input file not found: '.$input);

            return self::FAILURE;
        }

        $text = File::get($input);
        $lead = $this->extractLead($text);
        $plans = $this->extractPlans($text);

        File::ensureDirectoryExists(dirname($leadOutput));
        File::ensureDirectoryExists(dirname($pricingOutput));

        $this->writeLeadCsv($leadOutput, $lead);
        $this->writePricingCsv($pricingOutput, $plans);

        $this->components->info('Lead contact CSV saved to: '.$leadOutput);
        $this->components->info('Pricing CSV saved to: '.$pricingOutput);

        $this->table(['Name', 'Domain', 'Phones', 'Emails'], [[
            $lead['company_name'],
            $lead['domain'],
            $lead['phones'],
            $lead['emails'],
        ]]);

        $this->table(['Network', 'Size', 'Price', 'Validity'], array_map(fn (array $plan): array => [
            $plan['network'],
            $plan['size'],
            '₦'.$plan['price'],
            Str::limit($plan['validity'], 40),
        ], array_slice($plans, 0, 30)));

        $this->components->info('Extracted '.count($plans).' visible data plan price(s).');

        return self::SUCCESS;
    }

    private function extractLead(string $text): array
    {
        $emails = $this->extractEmails($text);
        $phones = $this->extractPhones($text);
        $company = $this->extractCompanyName($text);
        $domain = $this->domainFromEmails($emails) ?: Str::slug($company).'.com';

        return [
            'domain' => $domain,
            'url' => 'https://'.$domain,
            'company_name' => $company,
            'phones' => implode(' | ', $phones),
            'emails' => implode(' | ', $emails),
            'whatsapp_links' => '',
            'confidence' => ($phones || $emails) ? 'high' : 'medium',
            'confidence_score' => ($phones && $emails) ? 100 : 80,
            'signals' => 'pasted page | vtu | pricing | data plans',
            'pages_checked' => 'pasted text',
            'errors' => '',
        ];
    }

    private function extractCompanyName(string $text): string
    {
        foreach (preg_split('/\R/', $text) ?: [] as $line) {
            $line = trim($line);

            if (preg_match('/^Welcome to\s+(.+)$/i', $line, $match)) {
                return trim($match[1]);
            }

            if (preg_match('/^ABOUT\s+(.+)$/i', $line, $match) && strtolower(trim($match[1])) !== 'us') {
                return trim($match[1]);
            }
        }

        return 'Pasted VTU Website';
    }

    private function extractPlans(string $text): array
    {
        $plans = [];
        $network = null;

        foreach (preg_split('/\R/', $text) ?: [] as $line) {
            $line = trim(preg_replace('/\s+/', ' ', $line));

            if ($line === '') {
                continue;
            }

            if (preg_match('/^(MTN|AIRTEL|GLO|9MOBILE)\s+DATA$/i', $line, $match)) {
                $network = strtoupper($match[1]);
                continue;
            }

            if (! $network) {
                continue;
            }

            if (preg_match('/^(TESTIMONIALS|EXPLORE|Contact Us|FAQ)/i', $line)) {
                $network = null;
                continue;
            }

            if (! preg_match('/^([0-9]+(?:\.[0-9]+)?\s*(?:MB|GB|TB)|[0-9]+(?:\.[0-9]+)?(?:MB|GB|TB))\s+₦\s*([0-9,]+)\s*(.*)$/i', $line, $match)) {
                continue;
            }

            $plans[] = [
                'network' => $network,
                'size' => strtoupper(str_replace(' ', '', $match[1])),
                'price' => (string) (int) str_replace(',', '', $match[2]),
                'validity' => trim($match[3]),
                'raw_line' => $line,
            ];
        }

        return $plans;
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

    private function domainFromEmails(array $emails): ?string
    {
        foreach ($emails as $email) {
            $domain = strtolower((string) Str::after($email, '@'));

            if ($domain !== '' && ! in_array($domain, ['gmail.com', 'yahoo.com', 'outlook.com', 'hotmail.com'], true)) {
                return $domain;
            }
        }

        return null;
    }

    private function writeLeadCsv(string $path, array $lead): void
    {
        $handle = fopen($path, 'w');
        $headers = [
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
        ];

        fputcsv($handle, $headers);
        fputcsv($handle, array_map(fn (string $header) => $lead[$header] ?? '', $headers));
        fclose($handle);
    }

    private function writePricingCsv(string $path, array $plans): void
    {
        $handle = fopen($path, 'w');
        $headers = ['network', 'size', 'price', 'validity', 'raw_line'];

        fputcsv($handle, $headers);

        foreach ($plans as $plan) {
            fputcsv($handle, array_map(fn (string $header) => $plan[$header] ?? '', $headers));
        }

        fclose($handle);
    }
}

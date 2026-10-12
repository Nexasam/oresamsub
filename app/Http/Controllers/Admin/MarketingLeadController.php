<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MarketingLead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MarketingLeadController extends Controller
{
    public function index(Request $request)
    {
        $query = MarketingLead::query()->latest('updated_at');

        if ($search = trim((string) $request->query('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('domain', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%")
                    ->orWhere('app_name', 'like', "%{$search}%")
                    ->orWhere('emails', 'like', "%{$search}%")
                    ->orWhere('phones', 'like', "%{$search}%");
            });
        }

        if ($confidence = $request->query('confidence')) {
            $query->where('confidence', $confidence);
        }

        if ($source = $request->query('source')) {
            $query->where('source', 'like', "%{$source}%");
        }

        return view('admin.marketing_leads.index', [
            'leads' => $query->paginate(50)->withQueryString(),
            'stats' => [
                'total' => MarketingLead::count(),
                'with_phone' => MarketingLead::whereNotNull('phones')->where('phones', '<>', '')->count(),
                'with_email' => MarketingLead::whereNotNull('emails')->where('emails', '<>', '')->count(),
                'high' => MarketingLead::where('confidence', 'high')->count(),
            ],
            'defaultFiles' => [
                'master' => 'storage/app/private/marketing/vtu-master-outreach-leads.csv',
                'website' => 'storage/app/private/marketing/vtu-leads.csv',
                'msorg_play' => 'storage/app/private/marketing/msorg-play-apps.csv',
                'adex_play' => 'storage/app/private/marketing/adex-play-apps.csv',
                'autobiz_play' => 'storage/app/private/marketing/autobiz-cdlp-play-apps.csv',
                'autobiz_seed' => 'storage/app/private/marketing/autobiz-cdlp-app-name-leads.csv',
            ],
        ]);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'leads_csv' => ['required', 'file', 'mimes:csv,txt', 'max:10240'],
            'developer_source' => ['nullable', 'string', 'max:100'],
        ]);

        $result = $this->importCsv(
            $request->file('leads_csv')->getRealPath(),
            $request->input('developer_source')
        );

        return back()->with('success', "Imported {$result['imported']} lead(s), updated {$result['updated']} lead(s).");
    }

    public function importDefault(Request $request)
    {
        $request->validate([
            'path' => ['required', 'string'],
            'developer_source' => ['nullable', 'string', 'max:100'],
        ]);

        $path = base_path($request->input('path'));

        if (! File::exists($path)) {
            return back()->with('failure', 'CSV file not found: '.$request->input('path'));
        }

        $result = $this->importCsv($path, $request->input('developer_source'));

        return back()->with('success', "Imported {$result['imported']} lead(s), updated {$result['updated']} lead(s) from {$request->input('path')}.");
    }

    public function runCommand(Request $request)
    {
        $request->validate([
            'command' => ['required', 'in:discover_msorg,discover_adex,discover_autobiz,extract_websites,merge'],
        ]);

        $command = $request->input('command');
        $exitCode = 0;

        if ($command === 'discover_msorg') {
            $exitCode = Artisan::call('marketing:discover-msorg-play-apps', [
                '--append-domains' => true,
                '--trusted-developer' => true,
                '--developer-name' => 'MSORG',
                '--required-developer' => 'MSORG DEVELOPERS LIMITED,MSORG DEVELOPERS',
                '--follow-related' => true,
                '--output' => 'storage/app/private/marketing/msorg-play-apps.csv',
                '--limit' => 80,
                '--delay' => 250,
            ]);
        }

        if ($command === 'discover_adex') {
            $exitCode = Artisan::call('marketing:discover-msorg-play-apps', [
                '--developer-url' => 'https://play.google.com/store/apps/dev?id=5920068084377496312&hl=en',
                '--append-domains' => true,
                '--trusted-developer' => true,
                '--developer-name' => 'ADEX',
                '--required-developer' => 'A D E Developers,ADE DEVELOPERS',
                '--follow-related' => true,
                '--output' => 'storage/app/private/marketing/adex-play-apps.csv',
                '--limit' => 120,
                '--delay' => 250,
            ]);
        }

        if ($command === 'discover_autobiz') {
            $exitCode = Artisan::call('marketing:discover-msorg-play-apps', [
                '--developer-url' => 'https://play.google.com/store/apps/developer?id=CDLP+HUB+LTD&hl=en',
                '--append-domains' => true,
                '--trusted-developer' => true,
                '--developer-name' => 'AUTOBIZ-CDLP',
                '--required-developer' => 'CDLP HUB LTD',
                '--follow-related' => true,
                '--output' => 'storage/app/private/marketing/autobiz-cdlp-play-apps.csv',
                '--limit' => 120,
                '--delay' => 250,
            ]);
        }

        if ($command === 'extract_websites') {
            $exitCode = Artisan::call('marketing:extract-vtu-leads', [
                '--delay' => 250,
            ]);
        }

        if ($command === 'merge') {
            $exitCode = Artisan::call('marketing:merge-vtu-leads', [
                '--extra-play' => [
                    'storage/app/private/marketing/adex-play-apps.csv',
                    'storage/app/private/marketing/adex-chrome-stats-apps.csv',
                    'storage/app/private/marketing/adex-app-name-leads.csv',
                    'storage/app/private/marketing/autobiz-cdlp-play-apps.csv',
                    'storage/app/private/marketing/autobiz-cdlp-app-name-leads.csv',
                ],
            ]);
        }

        $output = trim(Artisan::output());

        if ($exitCode !== 0) {
            return back()->with('failure', "Command failed.\n".$output);
        }

        return back()->with('success', "Command completed.\n".$output);
    }

    private function importCsv(string $path, ?string $developerSource = null): array
    {
        $handle = fopen($path, 'r');
        $headers = fgetcsv($handle);
        $imported = 0;
        $updated = 0;

        if (! is_array($headers)) {
            fclose($handle);

            return compact('imported', 'updated');
        }

        $headers = array_map(fn ($header) => Str::snake(trim((string) $header)), $headers);

        while (($data = fgetcsv($handle)) !== false) {
            $row = array_combine($headers, array_pad($data, count($headers), ''));
            $payload = $this->payloadFromRow($row, $developerSource);

            if (empty($payload['domain'])) {
                continue;
            }

            $lead = MarketingLead::updateOrCreate(
                ['domain' => $payload['domain']],
                $payload
            );

            $lead->wasRecentlyCreated ? $imported++ : $updated++;
        }

        fclose($handle);

        return compact('imported', 'updated');
    }

    private function payloadFromRow(array $row, ?string $developerSource): array
    {
        $domain = strtolower(trim((string) ($row['domain'] ?? $row['website_domain'] ?? '')));

        if ($domain === '' && ! empty($row['website'])) {
            $domain = strtolower((string) parse_url((string) $row['website'], PHP_URL_HOST));
        }

        $confidenceScore = $row['confidence_score'] ?? null;

        return [
            'source' => $row['source'] ?? ($row['app_url'] ?? null ? 'play' : 'csv'),
            'developer_source' => $developerSource ?: $this->developerSourceFromNotes((string) ($row['notes'] ?? '')),
            'domain' => $domain,
            'company_name' => $row['company_name'] ?? '',
            'app_name' => $row['app_name'] ?? '',
            'website' => $row['website'] ?? $row['url'] ?? '',
            'phones' => $row['phones'] ?? '',
            'emails' => $row['emails'] ?? $row['support_email'] ?? '',
            'whatsapp_links' => $row['whatsapp_links'] ?? '',
            'confidence' => $row['confidence'] ?? '',
            'confidence_score' => is_numeric($confidenceScore) ? (int) $confidenceScore : null,
            'signals' => $row['signals'] ?? '',
            'play_app_url' => $row['play_app_url'] ?? $row['app_url'] ?? '',
            'package' => $row['package'] ?? '',
            'notes' => $row['notes'] ?? '',
            'last_imported_at' => now(),
        ];
    }

    private function developerSourceFromNotes(string $notes): ?string
    {
        if (preg_match('/trusted_developer:([a-z0-9\-]+)/', $notes, $match)) {
            return strtoupper(str_replace('-', ' ', $match[1]));
        }

        if (str_contains($notes, 'msorg_app')) {
            return 'MSORG';
        }

        return null;
    }
}

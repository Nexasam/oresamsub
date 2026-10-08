<?php

namespace App\Services\ProductPlans;

use App\Models\Automation;
use App\Models\AutomationProductPlan;
use App\Models\Network;
use App\Models\Product;
use App\Models\ProductPlan;
use App\Models\ProductPlanCategory;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AirtelGiftingPlanImportService
{
    /**
     * @var array<int, array<string, mixed>>
     */
    private const DEFAULT_PLANS = [
        ['public_id' => '223', 'name' => 'Special data 500', 'code' => 'Data CVM2_500', 'size' => '3.20 GB', 'validity' => 7, 'price' => 500],
        ['public_id' => '224', 'name' => 'Night Plan 50', 'code' => 'Night_Plan_50', 'size' => '250 MB', 'validity' => 1, 'price' => 50],
        ['public_id' => '225', 'name' => 'Daily Plan 75', 'code' => 'Daily_Plan_75', 'size' => '75 MB', 'validity' => 1, 'price' => 75],
        ['public_id' => '226', 'name' => 'Daily Plan 100', 'code' => 'Daily_Plan_100', 'size' => '110 MB', 'validity' => 1, 'price' => 100],
        ['public_id' => '227', 'name' => 'Social Plan 100', 'code' => 'Social_Plan_100', 'size' => '200 MB', 'validity' => 2, 'price' => 100],
        ['public_id' => '228', 'name' => 'Daily Plan 200', 'code' => 'Daily_Plan_200', 'size' => '230 MB', 'validity' => 2, 'price' => 200],
        ['public_id' => '229', 'name' => 'Daily Plan 300', 'code' => 'Daily_Plan_300', 'size' => '300 MB', 'validity' => 2, 'price' => 300],
        ['public_id' => '230', 'name' => 'Social Plan 300', 'code' => 'Social_Plan_300', 'size' => '1 GB', 'validity' => 3, 'price' => 300],
        ['public_id' => '231', 'name' => 'Daily Plan 350', 'code' => 'Daily_Plan_350', 'size' => '500 MB', 'validity' => 1, 'price' => 350],
        ['public_id' => '232', 'name' => 'Binge 500', 'code' => 'Binge_500', 'size' => '1 GB', 'validity' => 1, 'price' => 500],
        ['public_id' => '233', 'name' => 'Binge 600', 'code' => 'Binge_600', 'size' => '2 GB', 'validity' => 2, 'price' => 600],
        ['public_id' => '234', 'name' => 'Binge 750', 'code' => 'Binge_750', 'size' => '3 GB', 'validity' => 2, 'price' => 750],
        ['public_id' => '235', 'name' => 'Binge 1000', 'code' => 'Binge_1000', 'size' => '4 GB', 'validity' => 2, 'price' => 1000],
        ['public_id' => '236', 'name' => 'Binge 1500', 'code' => 'Bingee_1500', 'size' => '6 GB', 'validity' => 2, 'price' => 1500],
        ['public_id' => '237', 'name' => 'Weekly Plan 500', 'code' => 'Weekly_Plan_500', 'size' => '500 MB', 'validity' => 7, 'price' => 500],
        ['public_id' => '238', 'name' => 'Social Plan 500', 'code' => 'Social_Plan_500', 'size' => '1.50 GB', 'validity' => 7, 'price' => 500],
        ['public_id' => '239', 'name' => 'Weekly Plan 800', 'code' => 'Weekly_Plan_800', 'size' => '1 GB', 'validity' => 7, 'price' => 800],
        ['public_id' => '240', 'name' => 'Weekly Plan 1000', 'code' => 'Weekly_Plan_1000', 'size' => '1.50 GB', 'validity' => 7, 'price' => 1000],
        ['public_id' => '241', 'name' => 'Weekly Plan 1500', 'code' => 'Weekly_Plan_1500', 'size' => '4 GB', 'validity' => 7, 'price' => 1500],
        ['public_id' => '242', 'name' => 'Weekly Plan 2000', 'code' => 'Weekly_Plan_2000', 'size' => '6 GB', 'validity' => 7, 'price' => 2000],
        ['public_id' => '243', 'name' => 'Weekly Plan 2500', 'code' => 'Weekly_Plan_2500', 'size' => '8 GB', 'validity' => 7, 'price' => 2500],
        ['public_id' => '244', 'name' => 'Weekly Plan 3000', 'code' => 'Weekly_Plan_3000', 'size' => '10 GB', 'validity' => 7, 'price' => 3000],
        ['public_id' => '245', 'name' => 'Weekly Plan 5000', 'code' => 'Weekly_Plan_5000', 'size' => '20 GB', 'validity' => 7, 'price' => 5000],
        ['public_id' => '246', 'name' => 'Monthly Plan 1500', 'code' => 'Monthly_Plan_1500', 'size' => '2 GB', 'validity' => 30, 'price' => 1500],
        ['public_id' => '247', 'name' => 'Monthly Plan 2000', 'code' => 'Monthly_Plan_2000', 'size' => '3 GB', 'validity' => 30, 'price' => 2000],
        ['public_id' => '248', 'name' => 'Monthly Plan 2500', 'code' => 'Monthly_Plan_2500', 'size' => '4 GB', 'validity' => 30, 'price' => 2500],
        ['public_id' => '249', 'name' => 'Monthly Plan 3000', 'code' => 'Monthly_Plan_3000', 'size' => '8 GB', 'validity' => 30, 'price' => 3000],
        ['public_id' => '250', 'name' => 'Monthly Plan 4000', 'code' => 'Monthly_Plan_4000', 'size' => '10 GB', 'validity' => 30, 'price' => 4000],
        ['public_id' => '251', 'name' => 'Monthly Plan 5000', 'code' => 'Monthly_Plan_5000', 'size' => '13 GB', 'validity' => 30, 'price' => 5000],
        ['public_id' => '252', 'name' => 'Monthly Plan 6000', 'code' => 'Monthly_Plan_6000', 'size' => '18 GB', 'validity' => 30, 'price' => 6000],
        ['public_id' => '253', 'name' => 'Monthly Plan 8000', 'code' => 'Monthly_Plan_8000', 'size' => '25 GB', 'validity' => 30, 'price' => 8000],
        ['public_id' => '254', 'name' => 'Monthly Plan 10000', 'code' => 'Monthly_Plan_10000', 'size' => '35 GB', 'validity' => 30, 'price' => 10000],
        ['public_id' => '255', 'name' => 'Monthly Plan 15000', 'code' => 'Monthly_Plan_15000', 'size' => '60 GB', 'validity' => 30, 'price' => 15000],
        ['public_id' => '256', 'name' => 'Monthly Plan 20000', 'code' => 'Monthly_Plan_20000', 'size' => '100 GB', 'validity' => 30, 'price' => 20000],
        ['public_id' => '257', 'name' => 'Monthly Plan 30000', 'code' => 'Monthly_Plan_30000', 'size' => '160 GB', 'validity' => 30, 'price' => 30000],
        ['public_id' => '258', 'name' => 'Monthly Plan 40000', 'code' => 'Monthly_Plan_40000', 'size' => '210 GB', 'validity' => 30, 'price' => 40000],
        ['public_id' => '261', 'name' => 'Monthly Plan 50000', 'code' => 'Monthly_Plan_50000', 'size' => '300 GB', 'validity' => 90, 'price' => 50000],
        ['public_id' => '262', 'name' => 'Monthly Plan 60000', 'code' => 'Monthly_Plan_60000', 'size' => '350 GB', 'validity' => 120, 'price' => 60000],
        ['public_id' => '263', 'name' => 'Monthly plan 100000', 'code' => '3-Months_Plan_100000', 'size' => '685 GB', 'validity' => 365, 'price' => 100000],
    ];

    public function defaultText(): string
    {
        $lines = ['Public ID | Plan Name | Airtel API Code | Data Size | Validity | Original Price'];

        foreach ($this->defaultImportPlans() as $plan) {
            $lines[] = implode(' | ', [
                $plan['public_id'],
                $plan['name'],
                $plan['code'],
                $plan['size'],
                $plan['validity'].' days',
                $plan['price'],
            ]);
        }

        return implode(PHP_EOL, $lines);
    }

    /**
     * @return array<int, string>
     */
    public function defaultPublicIds(): array
    {
        return collect($this->defaultImportPlans())
            ->pluck('public_id')
            ->map(fn ($value): string => (string) $value)
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function defaultImportPlans(): array
    {
        return collect(self::DEFAULT_PLANS)
            ->reject(fn (array $plan): bool => $this->sizeToMb((string) $plan['size']) === 230)
            ->unique(fn (array $plan): string => (string) $plan['public_id'])
            ->values()
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    public function preview(string $text, array $options = []): array
    {
        $context = $this->resolveContext($options, false);
        $plans = $this->parseText($text);
        $rows = $this->buildRows($plans, $context, $options, (bool) ($options['update_existing'] ?? false));

        return [
            'dependencies' => $context['dependencies'],
            'errors' => $context['errors'],
            'warnings' => $context['warnings'],
            'parse_errors' => $this->parseErrors($text, $plans),
            'rows' => $rows,
            'summary' => $this->summary($rows),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function execute(string $text, array $options = []): array
    {
        $updateExisting = (bool) ($options['update_existing'] ?? false);
        $context = $this->resolveContext($options, true);
        $plans = $this->parseText($text);
        $rows = $this->buildRows($plans, $context, $options, $updateExisting);

        if ($context['errors']) {
            return [
                'dependencies' => $context['dependencies'],
                'errors' => $context['errors'],
                'warnings' => $context['warnings'],
                'parse_errors' => $this->parseErrors($text, $plans),
                'rows' => $rows,
                'summary' => $this->summary($rows),
            ];
        }

        $resultRows = DB::transaction(function () use ($rows, $context, $updateExisting): array {
            return array_map(
                fn (array $row): array => $this->executeRow($row, $context['category'], $context['automation'], $updateExisting),
                $rows
            );
        });

        return [
            'dependencies' => $context['dependencies'],
            'errors' => [],
            'warnings' => $context['warnings'],
            'parse_errors' => $this->parseErrors($text, $plans),
            'rows' => $resultRows,
            'summary' => $this->summary($resultRows),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function staleAirtelPlans(array $options = []): array
    {
        $context = $this->resolveContext($options, false);

        if ($context['errors']) {
            return [
                'errors' => $context['errors'],
                'rows' => [],
                'summary' => ['total' => 0],
            ];
        }

        $cutoff = now()->subMonths(3);
        $newPublicIds = $this->defaultPublicIds();

        $query = ProductPlan::query()
            ->with(['product_plan_category.product', 'product_plan_category.network', 'automationProductPlans'])
            ->whereHas('product_plan_category', function ($categoryQuery) use ($context, $options): void {
                if ($context['network']) {
                    $categoryQuery->where('network_id', $context['network']->id);
                }

                if ($context['product']) {
                    $categoryQuery->where('product_id', $context['product']->id);
                }

                if (filled($options['category_id'] ?? null)) {
                    $categoryQuery->where('id', $options['category_id']);
                }
            })
            ->whereNotIn('automation_product_plan_id', $newPublicIds)
            ->whereDoesntHave('automationProductPlans', fn ($providerQuery) => $providerQuery->whereIn('provider_plan_id', $newPublicIds))
            ->whereNotExists(function ($transactions) use ($cutoff): void {
                $transactions->selectRaw('1')
                    ->from('transactions')
                    ->whereColumn('transactions.product_plan_id', 'product_plans.id')
                    ->where('transactions.status', '1')
                    ->where('transactions.created_at', '>=', $cutoff);
            })
            ->select('product_plans.*')
            ->selectSub(function ($transactions): void {
                $transactions->from('transactions')
                    ->selectRaw('MAX(created_at)')
                    ->whereColumn('transactions.product_plan_id', 'product_plans.id')
                    ->where('transactions.status', '1');
            }, 'last_successful_purchase_at')
            ->selectSub(function ($transactions): void {
                $transactions->from('transactions')
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('transactions.product_plan_id', 'product_plans.id')
                    ->where('transactions.status', '1');
            }, 'successful_purchase_count')
            ->orderBy('product_plan_name');

        $rows = $query->get()->map(fn (ProductPlan $plan): array => [
            'id' => $plan->id,
            'name' => $plan->product_plan_name,
            'category' => $plan->product_plan_category?->product_plan_category_name,
            'network' => $plan->product_plan_category?->network?->network_name,
            'product' => $plan->product_plan_category?->product?->product_name,
            'automation_product_plan_id' => $plan->automation_product_plan_id,
            'size_mb' => $plan->data_size_in_mb,
            'validity_days' => $plan->validity_in_days,
            'visibility' => $plan->visibility,
            'last_successful_purchase_at' => $plan->last_successful_purchase_at,
            'successful_purchase_count' => (int) $plan->successful_purchase_count,
        ])->values()->all();

        return [
            'errors' => [],
            'rows' => $rows,
            'summary' => [
                'total' => count($rows),
                'cutoff' => $cutoff->toDateString(),
            ],
        ];
    }

    /**
     * @param array<int, string> $planIds
     * @return array<string, mixed>
     */
    public function deleteStaleAirtelPlans(array $planIds, array $options = []): array
    {
        $safeIds = collect($this->staleAirtelPlans($options)['rows'] ?? [])
            ->pluck('id')
            ->intersect($planIds)
            ->values();

        $deleted = ProductPlan::query()
            ->whereIn('id', $safeIds)
            ->delete();

        return [
            'deleted' => $deleted,
            'eligible_ids' => $safeIds->all(),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function parseText(string $text): array
    {
        $plans = [];

        foreach (preg_split('/\R/', $text) ?: [] as $line) {
            $line = trim($line);

            if ($line === '' || ! str_contains($line, '|')) {
                continue;
            }

            $parts = array_values(array_filter(array_map('trim', explode('|', $line)), fn (string $value): bool => $value !== ''));

            if (count($parts) < 6 || ! ctype_digit($parts[0])) {
                continue;
            }

            $plan = [
                'public_id' => $parts[0],
                'name' => $parts[1],
                'code' => $parts[2],
                'size' => $parts[3],
                'validity' => $this->parseValidity($parts[4]),
                'price' => $this->parseMoney($parts[5]),
            ];

            foreach (range(1, 7) as $level) {
                $partIndex = 5 + $level;
                if (isset($parts[$partIndex]) && $parts[$partIndex] !== '') {
                    $plan["level_price_{$level}"] = $this->parseMoney($parts[$partIndex]);
                }
            }

            $plans[] = $plan;
        }

        return $plans;
    }

    /**
     * @param array<int, array<string, mixed>> $rows
     */
    public function rowsToText(array $rows): string
    {
        $lines = ['Public ID | Plan Name | Airtel API Code | Data Size MB | Validity | Original Price | L1 | L2 | L3 | L4 | L5 | L6 | L7'];

        foreach ($rows as $row) {
            $lines[] = implode(' | ', [
                trim((string) ($row['public_id'] ?? '')),
                trim((string) ($row['name'] ?? '')),
                trim((string) ($row['code'] ?? '')),
                trim((string) ($row['size'] ?? '')),
                trim((string) ($row['validity'] ?? '')).' days',
                trim((string) ($row['price'] ?? '')),
                trim((string) ($row['level_price_1'] ?? $row['level_prices'][1] ?? '')),
                trim((string) ($row['level_price_2'] ?? $row['level_prices'][2] ?? '')),
                trim((string) ($row['level_price_3'] ?? $row['level_prices'][3] ?? '')),
                trim((string) ($row['level_price_4'] ?? $row['level_prices'][4] ?? '')),
                trim((string) ($row['level_price_5'] ?? $row['level_prices'][5] ?? '')),
                trim((string) ($row['level_price_6'] ?? $row['level_prices'][6] ?? '')),
                trim((string) ($row['level_price_7'] ?? $row['level_prices'][7] ?? '')),
            ]);
        }

        return implode(PHP_EOL, $lines);
    }

    /**
     * @return array<string, mixed>
     */
    private function resolveContext(array $options, bool $execute): array
    {
        $categoryName = trim((string) ($options['category'] ?? 'Airtel Gifting')) ?: 'Airtel Gifting';
        $sourceSlug = Str::lower(trim((string) ($options['source_slug'] ?? 'oresamplug')));
        $sourceName = Str::lower(trim((string) ($options['source_name'] ?? 'ORESAMPLUG AUTOMATION')));

        $network = filled($options['network_id'] ?? null)
            ? Network::query()->find($options['network_id'])
            : Network::query()->whereRaw('LOWER(network_name) = ?', ['airtel'])->first();
        $product = filled($options['product_id'] ?? null)
            ? Product::query()->find($options['product_id'])
            : Product::query()->whereRaw('LOWER(slug) = ?', ['data'])->orWhereRaw('LOWER(product_name) = ?', ['data'])->first();
        $automation = filled($options['automation_id'] ?? null)
            ? Automation::query()->find($options['automation_id'])
            : Automation::query()
                ->whereRaw('LOWER(slug) = ?', [$sourceSlug])
                ->orWhereRaw('LOWER(automation_name) = ?', [$sourceName])
                ->orWhere(function ($query) {
                    $query->where('slug', 'like', '%oresamplug%')
                        ->orWhere('automation_name', 'like', '%oresamplug%');
                })
                ->first();

        $errors = [];
        $warnings = [];

        if (! $network) {
            $errors[] = filled($options['network_id'] ?? null) ? 'Selected network was not found.' : 'Missing Network: Airtel.';
        }

        if (! $product) {
            $errors[] = filled($options['product_id'] ?? null) ? 'Selected product was not found.' : 'Missing Product: Data.';
        }

        if (! $automation) {
            $errors[] = filled($options['automation_id'] ?? null) ? 'Selected automation/source was not found.' : 'Missing Automation/source: ORESAMPLUG AUTOMATION / oresamplug.';
        }

        $category = null;
        if (filled($options['category_id'] ?? null)) {
            $category = ProductPlanCategory::query()->find($options['category_id']);

            if (! $category) {
                $errors[] = 'Selected product plan category was not found.';
            } else {
                $categoryName = $category->product_plan_category_name;
                $product = $category->product ?: $product;
                $network = $category->network ?: $network;
                $automation = $automation ?: $category->automation;
            }
        }

        if ($network && $product) {
            $category ??= ProductPlanCategory::query()
                    ->whereRaw('LOWER(product_plan_category_name) = ?', [Str::lower($categoryName)])
                    ->where('product_id', $product->id)
                    ->where('network_id', $network->id)
                    ->first();

            if (! $category && $execute && $automation) {
                $category = ProductPlanCategory::create([
                    'product_plan_category_name' => $categoryName,
                    'automation_id' => $automation->id,
                    'product_id' => $product->id,
                    'network_id' => $network->id,
                    'visibility' => '1',
                    'is_hot_sales' => '0',
                ]);
            } elseif (! $category) {
                $warnings[] = "Category '{$categoryName}' does not exist yet; Apply Import will create it.";
            }
        }

        return [
            'network' => $network,
            'product' => $product,
            'automation' => $automation,
            'category' => $category,
            'category_name' => $categoryName,
            'dependencies' => [
                'network' => $network?->network_name,
                'product' => $product?->product_name,
                'automation' => $automation?->automation_name,
                'category' => $category?->product_plan_category_name ?? $categoryName,
                'category_exists' => (bool) $category,
            ],
            'errors' => $errors,
            'warnings' => $warnings,
        ];
    }

    /**
     * @param array<int, array<string, mixed>> $plans
     * @param array<string, mixed> $context
     * @return array<int, array<string, mixed>>
     */
    private function buildRows(array $plans, array $context, array $options, bool $updateExisting): array
    {
        $airtimePurchaseRate = $this->boundedPercent($options['airtime_purchase_rate_per_100'] ?? 94, 0, 100);

        return array_map(function (array $plan) use ($context, $options, $updateExisting, $airtimePurchaseRate): array {
            $sizeMb = $this->sizeToMb((string) $plan['size']);
            $levelPrices = $this->levelPrices((float) $plan['price'], $options, $plan);
            $effectiveAirtimeCost = round((float) $plan['price'] * ($airtimePurchaseRate / 100), 2);
            $existing = $context['category'] && $context['automation']
                ? $this->matchingPlan($context['category'], $context['automation'], (string) $plan['public_id'])
                : null;
            $invalidReason = $this->invalidReason($plan);

            return array_merge($plan, [
                'name' => $this->formattedPlanName($sizeMb, (int) $plan['validity']),
                'size' => (string) $sizeMb,
                'size_mb' => $sizeMb,
                'is_editable' => (string) $plan['public_id'] !== '239',
                'level_prices' => $levelPrices,
                'airtime_purchase_rate_per_100' => $airtimePurchaseRate,
                'effective_airtime_cost' => $effectiveAirtimeCost,
                'profit_by_level' => array_map(
                    fn (float $sellingPrice): float => round($sellingPrice - $effectiveAirtimeCost, 2),
                    $levelPrices
                ),
                'existing_plan_id' => $existing?->id,
                'status' => $invalidReason ? 'invalid' : ($existing ? 'existing' : 'new'),
                'reason' => $invalidReason ?: ($existing ? ($updateExisting ? 'matches existing; will update on apply' : 'matching Airtel code already exists') : ''),
            ]);
        }, $plans);
    }

    private function executeRow(array $row, ProductPlanCategory $category, Automation $automation, bool $updateExisting): array
    {
        if ($row['status'] === 'invalid') {
            return $row;
        }

        $existing = $this->matchingPlan($category, $automation, (string) $row['public_id']);

        if ($existing && ! $updateExisting) {
            $row['status'] = 'skipped_existing';
            $row['reason'] = 'matching Airtel code already exists';

            return $row;
        }

        $payload = $this->productPlanPayload($row, $category, $automation);

        if ($existing) {
            $existing->update($payload);
            $plan = $existing;
            $row['status'] = 'updated_existing';
            $row['reason'] = 'existing plan updated';
        } else {
            $plan = ProductPlan::create($payload);
            $row['status'] = 'created';
            $row['reason'] = '';
        }

        AutomationProductPlan::updateOrCreate([
            'product_plan_id' => $plan->id,
            'automation_id' => $automation->id,
        ], [
            'priority' => 1,
            'cost_price' => $this->money($row['effective_airtime_cost']),
            'selling_price' => null,
            'provider_plan_id' => (string) $row['public_id'],
            'is_active' => true,
        ]);

        return $row;
    }

    private function matchingPlan(ProductPlanCategory $category, Automation $automation, string $publicPlanId): ?ProductPlan
    {
        if (! $category->exists) {
            return null;
        }

        return ProductPlan::query()
            ->where('product_plan_category_id', $category->id)
            ->where(function ($query) use ($automation, $publicPlanId) {
                $query->where('automation_product_plan_id', $publicPlanId)
                    ->orWhereHas('automationProductPlans', function ($providerQuery) use ($automation, $publicPlanId) {
                        $providerQuery
                            ->where('automation_id', $automation->id)
                            ->where('provider_plan_id', $publicPlanId);
                    });
            })
            ->first();
    }

    /**
     * @param array<string, mixed> $row
     * @return array<string, mixed>
     */
    private function productPlanPayload(array $row, ProductPlanCategory $category, Automation $automation): array
    {
        $levelPrices = $row['level_prices'];
        $payload = [
            'product_plan_name' => (string) $row['name'],
            'product_plan_category_id' => $category->id,
            'automation_id' => $automation->id,
            'automation_product_plan_id' => (string) $row['public_id'],
            'api_id' => null,
            'cost_price' => $this->money($row['effective_airtime_cost']),
            'data_size_in_mb' => (string) $row['size_mb'],
            'validity_in_days' => (string) $row['validity'],
            'default_selling_price' => $this->money($levelPrices[4]),
            'user_level_1_selling_price' => $this->money($levelPrices[1]),
            'user_level_2_selling_price' => $this->money($levelPrices[2]),
            'user_level_3_selling_price' => $this->money($levelPrices[3]),
            'user_level_4_selling_price' => $this->money($levelPrices[4]),
            'user_level_5_selling_price' => $this->money($levelPrices[5]),
            'user_level_6_selling_price' => $this->money($levelPrices[6]),
            'visibility' => '1',
            'public_visibility' => '1',
            'active_status' => '1',
        ];

        if (Schema::hasColumn('product_plans', 'user_level_7_selling_price')) {
            $payload['user_level_7_selling_price'] = $this->money($levelPrices[7]);
        }

        if (Schema::hasColumn('product_plans', 'is_social')) {
            $payload['is_social'] = Str::contains(Str::lower((string) $row['name']), 'social') ? '1' : '0';
        }

        if (Schema::hasColumn('product_plans', 'network')) {
            $payload['network'] = Str::lower((string) ($category->network?->network_name ?? 'airtel'));
        }

        foreach (range(1, 7) as $level) {
            $column = "user_level_{$level}_commission";
            if (Schema::hasColumn('product_plans', $column)) {
                $payload[$column] = '0';
            }
        }

        if (Schema::hasColumn('product_plans', 'commission_feature')) {
            $payload['commission_feature'] = '1';
        }

        return $payload;
    }

    /**
     * @return array<int, float>
     */
    private function levelPrices(float $price, array $options = [], array $plan = []): array
    {
        $prices = [];

        foreach (range(1, 7) as $level) {
            if (isset($plan["level_price_{$level}"]) && $plan["level_price_{$level}"] !== '') {
                $prices[$level] = round((float) $plan["level_price_{$level}"], 2);
            } else {
                $discount = $this->boundedPercent(
                    $options["level_discount_percent_{$level}"]
                        ?? $options['all_levels_discount_percent']
                        ?? $options['level_1_to_3_discount_percent']
                        ?? 0.5,
                    0,
                    100
                );
                $prices[$level] = round($price * ((100 - $discount) / 100), 0);
            }
        }

        return $prices;
    }

    private function boundedPercent(mixed $value, float $min, float $max): float
    {
        $numeric = (float) preg_replace('/[^\d.]/', '', (string) $value);

        return max($min, min($max, $numeric));
    }

    /**
     * @param array<int, array<string, mixed>> $rows
     * @return array<string, int>
     */
    private function summary(array $rows): array
    {
        return [
            'total' => count($rows),
            'new' => collect($rows)->where('status', 'new')->count(),
            'existing' => collect($rows)->where('status', 'existing')->count(),
            'created' => collect($rows)->where('status', 'created')->count(),
            'updated' => collect($rows)->where('status', 'updated_existing')->count(),
            'skipped_existing' => collect($rows)->where('status', 'skipped_existing')->count(),
            'invalid' => collect($rows)->where('status', 'invalid')->count(),
        ];
    }

    /**
     * @return array<int, string>
     */
    private function parseErrors(string $text, array $plans): array
    {
        if (trim($text) !== '' && count($plans) === 0) {
            return ['No valid rows were found. Use: Public ID | Plan Name | Airtel API Code | Data Size | Validity | Original Price'];
        }

        return [];
    }

    private function parseValidity(string $value): int
    {
        preg_match('/\d+/', $value, $matches);

        return (int) ($matches[0] ?? 0);
    }

    private function parseMoney(string $value): float
    {
        return (float) preg_replace('/[^\d.]/', '', $value);
    }

    private function sizeToMb(string $size): int
    {
        if (preg_match('/^\s*\d+\s*$/', $size)) {
            return (int) trim($size);
        }

        if (! preg_match('/([\d.]+)\s*(GB|MB)/i', $size, $matches)) {
            return 0;
        }

        $amount = (float) $matches[1];
        $unit = Str::upper($matches[2]);

        return (int) round($unit === 'GB' ? $amount * 1000 : $amount);
    }

    private function formattedPlanName(int $sizeMb, int $validityDays): string
    {
        $sizeLabel = $sizeMb >= 1000
            ? rtrim(rtrim(number_format($sizeMb / 1000, 2, '.', ''), '0'), '.').'GB'
            : $sizeMb.'MB';
        $dayLabel = $validityDays === 1 ? '1 DAY' : $validityDays.' DAYS';

        return "{$sizeLabel} AIRTEL CG ({$dayLabel})";
    }

    /**
     * @param array<string, mixed> $plan
     */
    private function invalidReason(array $plan): string
    {
        foreach (['public_id', 'name', 'code', 'size', 'validity', 'price'] as $key) {
            if (! isset($plan[$key]) || $plan[$key] === '') {
                return "missing {$key}";
            }
        }

        if ($this->sizeToMb((string) $plan['size']) <= 0) {
            return 'invalid data size';
        }

        if ($this->sizeToMb((string) $plan['size']) === 230) {
            return 'excluded size: 230MB';
        }

        if ((int) $plan['validity'] <= 0) {
            return 'invalid validity';
        }

        if ((float) $plan['price'] <= 0) {
            return 'invalid price';
        }

        $excluded = ['talk more', 'talkmore', 'flexi', 'roam the world', 'ebonylife'];
        $haystack = Str::lower((string) $plan['name'].' '.(string) $plan['code']);
        foreach ($excluded as $needle) {
            if (Str::contains($haystack, $needle)) {
                return "excluded family: {$needle}";
            }
        }

        return '';
    }

    private function money(float|int|string $value): string
    {
        return number_format((float) $value, 2, '.', '');
    }
}

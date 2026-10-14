<?php

namespace App\Services\Reports;

use Carbon\CarbonPeriod;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SalesReportService
{
    private const SALE_CATEGORIES = [
        'data',
        'airtime',
        'cable_subscription',
        'utility_bills',
    ];

    public function generate(array $filters): array
    {
        $baseQuery = $this->baseQuery($filters);
        $actualPaid = $this->actualPaidExpression();
        $volumeExpression = $this->volumeGbExpression();

        $summaryRow = (clone $baseQuery)
            ->selectRaw("COUNT(*) as quantity_sold")
            ->selectRaw("COALESCE(SUM({$actualPaid}), 0) as total_sales")
            ->selectRaw("COALESCE(SUM({$volumeExpression}), 0) as total_volume_gb")
            ->selectRaw('COUNT(DISTINCT t.user_id) as customer_count')
            ->selectRaw('COUNT(DISTINCT t.product_plan_id) as product_count')
            ->first();

        $days = max(1, $filters['date_from']->startOfDay()->diffInDays($filters['date_to']->startOfDay()) + 1);
        $quantitySold = (int) ($summaryRow->quantity_sold ?? 0);
        $totalSales = (float) ($summaryRow->total_sales ?? 0);

        $products = $this->products($baseQuery, $actualPaid, $volumeExpression);
        $customers = $this->customers($baseQuery, $actualPaid);
        $dailyTrend = $this->dailyTrend($baseQuery, $actualPaid, $volumeExpression, $filters);

        return [
            'summary' => [
                'quantity_sold' => $quantitySold,
                'total_sales' => $totalSales,
                'total_volume_gb' => (float) ($summaryRow->total_volume_gb ?? 0),
                'customer_count' => (int) ($summaryRow->customer_count ?? 0),
                'product_count' => (int) ($summaryRow->product_count ?? 0),
                'days' => $days,
                'average_count_per_day' => $quantitySold / $days,
                'average_sales_per_day' => $totalSales / $days,
                'top_customer' => $customers->first(),
                'top_product' => $products->first(),
            ],
            'products' => $products,
            'customers' => $customers,
            'daily_trend' => $dailyTrend,
        ];
    }

    private function baseQuery(array $filters): Builder
    {
        $query = DB::table('transactions as t')
            ->leftJoin('product_plans as pp', 'pp.id', '=', 't.product_plan_id')
            ->leftJoin('product_plan_categories as ppc', 'ppc.id', '=', 'pp.product_plan_category_id')
            ->leftJoin('products as p', 'p.id', '=', 'ppc.product_id')
            ->leftJoin('networks as n', 'n.id', '=', 'ppc.network_id')
            ->leftJoin('users as u', 'u.id', '=', 't.user_id')
            ->where('t.status', 1)
            ->whereNotNull('pp.id')
            ->whereIn('t.transaction_category', self::SALE_CATEGORIES)
            ->whereBetween('t.created_at', [
                $filters['date_from']->format('Y-m-d H:i:s'),
                $filters['date_to']->format('Y-m-d H:i:s'),
            ]);

        if (($filters['customer_mode'] ?? 'all') === 'individual' && ! empty($filters['user_id'])) {
            $query->where('t.user_id', $filters['user_id']);
        }

        if (! empty($filters['product_type'])) {
            $query->where('t.transaction_category', $filters['product_type']);
        }

        if (! empty($filters['network_id'])) {
            $query->where('ppc.network_id', $filters['network_id']);
        }

        if (! empty($filters['category_id'])) {
            $query->where('ppc.id', $filters['category_id']);
        }

        if (! empty($filters['plan_ids'])) {
            $query->whereIn('t.product_plan_id', $filters['plan_ids']);
        }

        return $query;
    }

    private function products(Builder $baseQuery, string $actualPaid, string $volumeExpression): Collection
    {
        $topBuyers = (clone $baseQuery)
            ->select([
                't.product_plan_id',
                'u.id as user_id',
                'u.username',
                'u.first_name',
                'u.last_name',
                'u.email',
            ])
            ->selectRaw('COUNT(*) as quantity_sold')
            ->selectRaw("COALESCE(SUM({$actualPaid}), 0) as total_sales")
            ->groupBy(['t.product_plan_id', 'u.id', 'u.username', 'u.first_name', 'u.last_name', 'u.email'])
            ->orderByDesc('quantity_sold')
            ->orderByDesc('total_sales')
            ->get()
            ->groupBy('product_plan_id')
            ->map(fn (Collection $buyers): array => $this->mapCustomerRow($buyers->first()));

        return (clone $baseQuery)
            ->select([
                't.product_plan_id',
                't.transaction_category as product_type',
                'n.network_name',
                'pp.product_plan_name as plan_name',
                'pp.data_size_in_mb as volume_mb',
                'pp.validity_in_days',
                'ppc.product_plan_category_name as category_name',
            ])
            ->selectRaw('COUNT(*) as quantity_sold')
            ->selectRaw("COALESCE(SUM({$actualPaid}), 0) as total_sales")
            ->selectRaw("COALESCE(SUM({$volumeExpression}), 0) as total_volume_gb")
            ->groupBy([
                't.product_plan_id',
                't.transaction_category',
                'n.network_name',
                'pp.product_plan_name',
                'pp.data_size_in_mb',
                'pp.validity_in_days',
                'ppc.product_plan_category_name',
            ])
            ->orderByDesc('quantity_sold')
            ->orderByDesc('total_sales')
            ->get()
            ->map(fn (object $row): array => [
                'product_plan_id' => $row->product_plan_id,
                'product_type' => $row->product_type,
                'network_name' => $row->network_name,
                'category_name' => $row->category_name,
                'plan_name' => $row->plan_name ?: 'Unknown plan',
                'volume_mb' => $row->volume_mb !== null && $row->volume_mb !== '' ? (float) $row->volume_mb : null,
                'validity_days' => $row->validity_in_days !== null && $row->validity_in_days !== '' ? (int) $row->validity_in_days : null,
                'quantity_sold' => (int) $row->quantity_sold,
                'total_sales' => (float) $row->total_sales,
                'total_volume_gb' => (float) $row->total_volume_gb,
                'top_buyer' => $topBuyers->get($row->product_plan_id),
            ]);
    }

    private function customers(Builder $baseQuery, string $actualPaid): Collection
    {
        return (clone $baseQuery)
            ->select([
                'u.id as user_id',
                'u.username',
                'u.first_name',
                'u.last_name',
                'u.email',
                'u.phone_number',
            ])
            ->selectRaw('COUNT(*) as quantity_sold')
            ->selectRaw("COALESCE(SUM({$actualPaid}), 0) as total_sales")
            ->selectRaw('COUNT(DISTINCT t.product_plan_id) as distinct_plans')
            ->groupBy(['u.id', 'u.username', 'u.first_name', 'u.last_name', 'u.email', 'u.phone_number'])
            ->orderByDesc('quantity_sold')
            ->orderByDesc('total_sales')
            ->limit(50)
            ->get()
            ->map(fn (object $row): array => $this->mapCustomerRow($row));
    }

    private function dailyTrend(Builder $baseQuery, string $actualPaid, string $volumeExpression, array $filters): Collection
    {
        $rows = (clone $baseQuery)
            ->selectRaw('DATE(t.created_at) as sale_date')
            ->selectRaw('COUNT(*) as quantity_sold')
            ->selectRaw("COALESCE(SUM({$actualPaid}), 0) as total_sales")
            ->selectRaw("COALESCE(SUM({$volumeExpression}), 0) as total_volume_gb")
            ->groupByRaw('DATE(t.created_at)')
            ->orderBy('sale_date')
            ->get()
            ->keyBy('sale_date');

        return collect(CarbonPeriod::create(
            $filters['date_from']->startOfDay(),
            $filters['date_to']->startOfDay()
        ))->map(function ($date) use ($rows): array {
            $key = $date->format('Y-m-d');
            $row = $rows->get($key);

            return [
                'date' => $key,
                'label' => $date->format('D, M j'),
                'quantity_sold' => (int) ($row->quantity_sold ?? 0),
                'total_sales' => (float) ($row->total_sales ?? 0),
                'total_volume_gb' => (float) ($row->total_volume_gb ?? 0),
            ];
        });
    }

    private function actualPaidExpression(): string
    {
        return "CASE WHEN CAST(COALESCE(NULLIF(TRIM(t.service_charge), ''), '0') AS DECIMAL(18,2)) > 0 THEN CAST(COALESCE(NULLIF(TRIM(t.service_charge), ''), '0') AS DECIMAL(18,2)) ELSE CAST(COALESCE(NULLIF(TRIM(t.discounted_amount), ''), NULLIF(TRIM(t.amount), ''), '0') AS DECIMAL(18,2)) END";
    }

    private function volumeGbExpression(): string
    {
        return "CASE WHEN p.slug = 'data' THEN (CAST(COALESCE(NULLIF(TRIM(pp.data_size_in_mb), ''), '0') AS DECIMAL(18,2)) / 1000) ELSE 0 END";
    }

    private function mapCustomerRow(?object $row): ?array
    {
        if (! $row) {
            return null;
        }

        return [
            'user_id' => $row->user_id,
            'username' => $row->username ?: trim(($row->first_name ?? '').' '.($row->last_name ?? '')) ?: 'Unknown customer',
            'name' => trim(($row->first_name ?? '').' '.($row->last_name ?? '')) ?: null,
            'email' => $row->email,
            'phone_number' => $row->phone_number ?? null,
            'quantity_sold' => (int) $row->quantity_sold,
            'total_sales' => (float) $row->total_sales,
            'distinct_plans' => (int) ($row->distinct_plans ?? 0),
        ];
    }
}

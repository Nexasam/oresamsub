<?php

namespace App\Http\Requests\Admin;

use Carbon\CarbonImmutable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SalesReportRequest extends FormRequest
{
    private const TIMEZONE = 'Africa/Lagos';

    private const PRODUCT_TYPES = [
        'data',
        'airtime',
        'cable_subscription',
        'utility_bills',
    ];

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $period = $this->input('period', 'today');
        $customerMode = $this->filled('user_id')
            ? 'individual'
            : $this->input('customer_mode', 'all');

        $customDefaults = [];
        if ($period === 'custom') {
            $today = CarbonImmutable::now(self::TIMEZONE)->format('Y-m-d');
            $customDefaults = [
                'date_from' => $this->has('date_from') ? $this->input('date_from') : $today,
                'date_to' => $this->has('date_to') ? $this->input('date_to') : $today,
            ];
        }

        $this->merge($customDefaults + [
            'period' => $period,
            'customer_mode' => $customerMode,
            'plan_ids' => $this->input('plan_ids', []),
        ]);
    }

    public function rules(): array
    {
        return [
            'period' => ['required', Rule::in(['today', 'yesterday', 'week', 'month', 'custom'])],
            'date_from' => ['nullable', 'required_if:period,custom', 'date_format:Y-m-d'],
            'date_to' => ['nullable', 'required_if:period,custom', 'date_format:Y-m-d', 'after_or_equal:date_from'],
            'customer_mode' => ['required', Rule::in(['all', 'individual'])],
            'user_id' => ['nullable', 'required_if:customer_mode,individual', 'uuid', 'exists:users,id'],
            'product_type' => ['nullable', Rule::in(self::PRODUCT_TYPES)],
            'network_id' => ['nullable', 'uuid', 'exists:networks,id'],
            'category_id' => ['nullable', 'uuid', 'exists:product_plan_categories,id'],
            'plan_ids' => ['array'],
            'plan_ids.*' => ['uuid', 'distinct', 'exists:product_plans,id'],
        ];
    }

    public function filters(): array
    {
        $validated = $this->validated();
        $period = $validated['period'];
        $now = CarbonImmutable::now(self::TIMEZONE);

        [$dateFrom, $dateTo] = match ($period) {
            'yesterday' => [$now->subDay()->startOfDay(), $now->subDay()->endOfDay()],
            'week' => [$now->subDays(6)->startOfDay(), $now->endOfDay()],
            'month' => [$now->subDays(29)->startOfDay(), $now->endOfDay()],
            'custom' => [
                CarbonImmutable::createFromFormat('Y-m-d', $validated['date_from'], self::TIMEZONE)->startOfDay(),
                CarbonImmutable::createFromFormat('Y-m-d', $validated['date_to'], self::TIMEZONE)->endOfDay(),
            ],
            default => [$now->startOfDay(), $now->endOfDay()],
        };

        return [
            'period' => $period,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'customer_mode' => $validated['customer_mode'],
            'user_id' => $validated['customer_mode'] === 'individual' ? ($validated['user_id'] ?? null) : null,
            'product_type' => $validated['product_type'] ?? null,
            'network_id' => $validated['network_id'] ?? null,
            'category_id' => $validated['category_id'] ?? null,
            'plan_ids' => array_values($validated['plan_ids'] ?? []),
        ];
    }
}

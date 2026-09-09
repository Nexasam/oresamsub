<?php

namespace App\Models;

use App\Models\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StandaloneFeature extends Model
{
    use HasUuids;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'default_price' => 'decimal:2',
            'level_1_price' => 'decimal:2',
            'level_2_price' => 'decimal:2',
            'level_3_price' => 'decimal:2',
            'level_4_price' => 'decimal:2',
            'default_monthly_price' => 'decimal:2',
            'level_1_monthly_price' => 'decimal:2',
            'level_2_monthly_price' => 'decimal:2',
            'level_3_monthly_price' => 'decimal:2',
            'level_4_monthly_price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function priceFor(?int $level): string
    {
        return $level && in_array($level, [1, 2, 3, 4], true)
            ? $this->{"level_{$level}_price"}
            : $this->default_price;
    }

    public function monthlyPriceFor(?int $level): string
    {
        $monthly = $level && in_array($level, [1, 2, 3, 4], true)
            ? $this->{"level_{$level}_monthly_price"}
            : $this->default_monthly_price;

        return $monthly ?? $this->priceFor($level);
    }

    public function purchasePriceFor(?int $level): string
    {
        if ($this->billing_type === 'free') {
            return '0.00';
        }

        return $this->billing_type === 'monthly' ? $this->monthlyPriceFor($level) : $this->priceFor($level);
    }

    public function isRecurring(): bool
    {
        return in_array($this->billing_type, ['monthly', 'one_time_plus_monthly'], true);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(StandaloneFeatureSubscription::class);
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(StandaloneFeaturePurchase::class);
    }
}

<?php

namespace App\Models;

use App\Models\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bonus extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    public const GROUP_NEW_REGISTRATION = 'new_registration';

    public const GROUP_DORMANT_CUSTOMER = 'dormant_customer';

    public const ENJOYMENT_WALLET = 'bonus_wallet';

    public const ENJOYMENT_FUNDING = 'funding_bonus';

    public const ENJOYMENT_FEE_WAIVER = 'funding_fee_waiver';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
            'enjoyment' => 'array',
            'conditions' => 'array',
            'funding_whitelist' => 'array',
            'funding_value' => 'decimal:4',
            'funding_cap' => 'decimal:2',
            'bonus_wallet_amount' => 'decimal:2',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query
            ->where('status', true)
            ->where(fn (Builder $builder) => $builder
                ->whereNull('starts_at')
                ->orWhere('starts_at', '<=', now()))
            ->where('ends_at', '>', now());
    }

    public function entitlements(): HasMany
    {
        return $this->hasMany(BonusEntitlement::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(BonusLog::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function includes(string $enjoyment): bool
    {
        return in_array($enjoyment, $this->enjoyment ?? [], true);
    }
}

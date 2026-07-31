<?php

namespace App\Models;

use App\Models\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UplineFundingBonusSetting extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
            'reward_value' => 'decimal:4',
            'reward_cap' => 'decimal:2',
            'frequency_per_downline' => 'integer',
            'funding_whitelist' => 'array',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query
            ->where('enabled', true)
            ->where(fn (Builder $builder) => $builder->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->where(fn (Builder $builder) => $builder->whereNull('ends_at')->orWhere('ends_at', '>', now()));
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(UplineFundingBonusLog::class);
    }
}

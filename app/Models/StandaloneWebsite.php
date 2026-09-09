<?php

namespace App\Models;

use App\Models\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class StandaloneWebsite extends Model
{
    use HasUuids;

    protected $guarded = [];

    protected $hidden = [
        'api_token_digest',
        'bvn',
        'webhook_signing_secret',
    ];

    protected function casts(): array
    {
        return [
            'bvn' => 'encrypted',
            'webhook_signing_secret' => 'encrypted',
            'master_wallet' => 'decimal:2',
            'api_token_must_rotate' => 'boolean',
            'api_token_expires_at' => 'datetime',
            'api_token_rotated_at' => 'datetime',
            'webhook_secret_rotated_at' => 'datetime',
        ];
    }

    public static function findByApiToken(string $token): ?self
    {
        if ($token === '') {
            return null;
        }

        return static::query()
            ->where('api_token_digest', hash('sha256', $token))
            ->first();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function virtualAccount(): HasOne
    {
        return $this->hasOne(StandaloneVirtualAccount::class);
    }

    public function fundingEvents(): HasMany
    {
        return $this->hasMany(StandaloneFundingEvent::class);
    }

    public function walletEntries(): HasMany
    {
        return $this->hasMany(StandaloneWalletEntry::class);
    }
}

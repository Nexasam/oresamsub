<?php

namespace App\Models;

use App\Models\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StandaloneFeaturePurchase extends Model
{
    use HasUuids;

    protected $guarded = [];

    protected function casts(): array
    {
        return ['amount' => 'decimal:2', 'period_starts_at' => 'datetime', 'period_ends_at' => 'datetime'];
    }

    public function standaloneWebsite(): BelongsTo
    {
        return $this->belongsTo(StandaloneWebsite::class);
    }

    public function feature(): BelongsTo
    {
        return $this->belongsTo(StandaloneFeature::class, 'standalone_feature_id');
    }

    public function walletEntry(): BelongsTo
    {
        return $this->belongsTo(StandaloneWalletEntry::class, 'standalone_wallet_entry_id');
    }
}

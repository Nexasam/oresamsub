<?php

namespace App\Models;

use App\Models\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AffiliateFundingAttempt extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'wallet_balance' => 'decimal:2',
            'funding_threshold' => 'decimal:2',
            'requested_amount' => 'decimal:2',
            'metadata' => 'array',
            'triggered_at' => 'datetime',
        ];
    }

    public function setting(): BelongsTo
    {
        return $this->belongsTo(AffiliateWalletSetting::class, 'affiliate_wallet_setting_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

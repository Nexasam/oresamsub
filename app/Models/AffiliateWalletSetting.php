<?php

namespace App\Models;

use App\Models\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AffiliateWalletSetting extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

    protected $hidden = [
        'funding_bank_name',
        'funding_bank_code',
        'funding_account_name',
        'funding_account_number',
    ];

    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
            'funding_threshold' => 'decimal:2',
            'funding_amount' => 'decimal:2',
            'funding_bank_name' => 'encrypted',
            'funding_bank_code' => 'encrypted',
            'funding_account_name' => 'encrypted',
            'funding_account_number' => 'encrypted',
            'automatic_transfer_enabled' => 'boolean',
            'last_notified_on' => 'date',
            'last_checked_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(AffiliateFundingAttempt::class);
    }
}

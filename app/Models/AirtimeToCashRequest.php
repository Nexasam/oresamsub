<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AirtimeToCashRequest extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_PAID = 'paid';
    public const STATUS_REJECTED = 'rejected';

    protected $guarded = [];

    protected $hidden = [
        'payout_account_number',
    ];

    protected $casts = [
        'airtime_amount' => 'decimal:2',
        'cash_amount' => 'decimal:2',
        'rate_per_100' => 'decimal:2',
        'payout_account_number' => 'encrypted',
        'fraud_disclaimer_accepted_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_PROCESSING,
            self::STATUS_PAID,
            self::STATUS_REJECTED,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function network(): BelongsTo
    {
        return $this->belongsTo(Network::class);
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function maskedAccountNumber(): string
    {
        $account = (string) $this->payout_account_number;

        if ($account === '') {
            return '';
        }

        return str_repeat('*', max(strlen($account) - 4, 0)).substr($account, -4);
    }
}

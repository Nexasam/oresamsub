<?php

namespace App\Models;

use App\Models\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UplineFundingBonusLog extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'funded_amount' => 'decimal:2',
            'bonus_amount' => 'decimal:2',
            'bonus_balance_before' => 'decimal:2',
            'bonus_balance_after' => 'decimal:2',
            'sequence' => 'integer',
            'metadata' => 'array',
        ];
    }

    public function setting(): BelongsTo
    {
        return $this->belongsTo(UplineFundingBonusSetting::class, 'upline_funding_bonus_setting_id');
    }

    public function upline(): BelongsTo
    {
        return $this->belongsTo(User::class, 'upline_id');
    }

    public function downline(): BelongsTo
    {
        return $this->belongsTo(User::class, 'downline_id');
    }
}

<?php

namespace App\Models;

use App\Models\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BonusEntitlement extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'bonus_wallet_awarded' => 'decimal:2',
            'bonus_wallet_remaining' => 'decimal:2',
            'funding_uses' => 'integer',
            'awarded_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function bonus(): BelongsTo
    {
        return $this->belongsTo(Bonus::class)->withTrashed();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(BonusLog::class);
    }
}

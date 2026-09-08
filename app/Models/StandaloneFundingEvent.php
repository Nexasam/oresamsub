<?php

namespace App\Models;

use App\Models\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StandaloneFundingEvent extends Model
{
    use HasUuids;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'amount_gross' => 'decimal:2',
            'fees' => 'decimal:2',
            'amount_settled' => 'decimal:2',
            'paid_at' => 'datetime',
            'provider_payload' => 'array',
            'callback_payload' => 'array',
            'first_attempted_at' => 'datetime',
            'last_attempted_at' => 'datetime',
            'delivered_at' => 'datetime',
        ];
    }

    public function standaloneWebsite(): BelongsTo
    {
        return $this->belongsTo(StandaloneWebsite::class);
    }
}

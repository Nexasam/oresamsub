<?php

namespace App\Models;

use App\Models\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UssdRechargeAttempt extends Model
{
    use HasUuids;

    public const TERMINAL_STATUSES = ['confirmed_success', 'confirmed_failed', 'cancelled'];

    protected $guarded = [];

    protected $hidden = ['dispatch_token_digest'];

    protected function casts(): array
    {
        return [
            'response_text' => 'encrypted',
            'picked_at' => 'datetime',
            'dial_started_at' => 'datetime',
            'result_received_at' => 'datetime',
            'reviewed_at' => 'datetime',
        ];
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(UssdRechargeBatch::class, 'batch_id');
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(UssdGatewayDevice::class, 'device_id');
    }
}

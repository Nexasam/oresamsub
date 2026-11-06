<?php

namespace App\Models;

use App\Models\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UssdGatewayDevice extends Model
{
    use HasUuids;

    protected $guarded = [];

    protected $hidden = ['token_digest'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'processing_mode' => 'string',
            'sim_slot' => 'integer',
            'subscription_id' => 'integer',
            'bound_at' => 'datetime',
            'last_seen_at' => 'datetime',
        ];
    }

    public function batches(): HasMany
    {
        return $this->hasMany(UssdRechargeBatch::class, 'device_id');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(UssdRechargeAttempt::class, 'device_id');
    }
}

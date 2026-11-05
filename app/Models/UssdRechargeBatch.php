<?php

namespace App\Models;

use App\Models\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UssdRechargeBatch extends Model
{
    use HasUuids;

    protected $guarded = [];

    public function device(): BelongsTo
    {
        return $this->belongsTo(UssdGatewayDevice::class, 'device_id');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(UssdRechargeAttempt::class, 'batch_id')->orderBy('sequence');
    }
}

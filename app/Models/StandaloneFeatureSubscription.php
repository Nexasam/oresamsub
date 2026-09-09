<?php

namespace App\Models;

use App\Models\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StandaloneFeatureSubscription extends Model
{
    use HasUuids;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'current_period_starts_at' => 'datetime',
            'current_period_ends_at' => 'datetime',
            'grace_ends_at' => 'datetime',
            'cancel_at_period_end' => 'boolean',
            'cancelled_at' => 'datetime',
        ];
    }

    public function standaloneWebsite(): BelongsTo
    {
        return $this->belongsTo(StandaloneWebsite::class);
    }

    public function feature(): BelongsTo
    {
        return $this->belongsTo(StandaloneFeature::class, 'standalone_feature_id');
    }
}

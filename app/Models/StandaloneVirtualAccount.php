<?php

namespace App\Models;

use App\Models\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StandaloneVirtualAccount extends Model
{
    use HasUuids;

    protected $guarded = [];

    protected function casts(): array
    {
        return ['provider_metadata' => 'array'];
    }

    public function standaloneWebsite(): BelongsTo
    {
        return $this->belongsTo(StandaloneWebsite::class);
    }

    public function fundingOption(): BelongsTo
    {
        return $this->belongsTo(FundingOption::class);
    }
}

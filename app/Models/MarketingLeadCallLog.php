<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketingLeadCallLog extends Model
{
    protected $fillable = [
        'marketing_lead_id',
        'user_id',
        'phone',
        'note',
        'called_at',
    ];

    protected $casts = [
        'called_at' => 'datetime',
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(MarketingLead::class, 'marketing_lead_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

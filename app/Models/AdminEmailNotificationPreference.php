<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminEmailNotificationPreference extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'failed_transactions' => 'boolean',
            'pending_transactions' => 'boolean',
            'automation_low_balance' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

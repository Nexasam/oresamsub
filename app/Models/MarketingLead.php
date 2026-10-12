<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MarketingLead extends Model
{
    protected $fillable = [
        'source',
        'developer_source',
        'domain',
        'company_name',
        'app_name',
        'website',
        'phones',
        'emails',
        'whatsapp_links',
        'confidence',
        'confidence_score',
        'signals',
        'play_app_url',
        'package',
        'notes',
        'status',
        'last_imported_at',
    ];

    protected $casts = [
        'confidence_score' => 'integer',
        'last_imported_at' => 'datetime',
    ];

    public function callLogs(): HasMany
    {
        return $this->hasMany(MarketingLeadCallLog::class)->latest('called_at');
    }
}

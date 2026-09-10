<?php

namespace App\Models;

use App\Models\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Model;

class AffiliateDataPurchaseRequest extends Model
{
    use HasUuids;

    protected $guarded = [];

    protected $casts = [
        'response_body' => 'array',
        'provider_response' => 'array',
        'response_status' => 'integer',
    ];
}

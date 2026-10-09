<?php

namespace App\Models;

use App\Models\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NetworkIssueAlert extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
        'priority' => 'integer',
    ];

    public function network()
    {
        return $this->belongsTo(Network::class);
    }

    public function displayTitle(): string
    {
        $network = $this->network?->network_name ?? 'Network';

        return $this->title ?: "{$network} service update";
    }

    public function displayMessage(): string
    {
        $network = $this->network?->network_name ?? 'this network';

        return $this->message ?: "{$network} is currently experiencing service issues from the telco. Some transactions may delay or fail. Please try again shortly while the provider resolves it.";
    }
}

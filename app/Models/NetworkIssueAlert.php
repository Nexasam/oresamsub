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
        return $this->title ?: self::defaultTitleFor($this->network?->network_name);
    }

    public function displayMessage(): string
    {
        return $this->message ?: self::defaultMessageFor($this->network?->network_name);
    }

    public static function defaultTitleFor(?string $networkName): string
    {
        $network = self::normalizedNetworkName($networkName);

        return "{$network} service update";
    }

    public static function defaultMessageFor(?string $networkName): string
    {
        $network = self::normalizedNetworkName($networkName);

        return "{$network} is currently experiencing a temporary service issue from the telco. Some transactions may delay or fail. Please hold on or try again shortly while the provider resolves it.";
    }

    private static function normalizedNetworkName(?string $networkName): string
    {
        $network = trim((string) $networkName);

        return $network !== '' ? $network : 'Network';
    }
}

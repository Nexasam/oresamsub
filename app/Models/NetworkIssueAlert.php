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
        'last_restored_at' => 'datetime',
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

    public function displayRestoredTitle(): string
    {
        return $this->restored_title ?: self::defaultRestoredTitleFor($this->network?->network_name);
    }

    public function displayRestoredMessage(): string
    {
        return $this->restored_message ?: self::defaultRestoredMessageFor($this->network?->network_name);
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

    public static function defaultRestoredTitleFor(?string $networkName): string
    {
        $network = self::normalizedNetworkName($networkName);

        return "{$network} service restored";
    }

    public static function defaultRestoredMessageFor(?string $networkName): string
    {
        $network = self::normalizedNetworkName($networkName);

        return "{$network} service has been restored by the telco. Transactions should now process normally. Thank you for your patience.";
    }

    public function activePayload(): array
    {
        return [
            'id' => $this->id,
            'type' => 'network_issue',
            'network' => $this->network?->network_name ?? 'Network',
            'title' => $this->displayTitle(),
            'message' => $this->displayMessage(),
            'priority' => $this->priority,
            'updated_at' => optional($this->updated_at)->toIso8601String(),
        ];
    }

    public function restoredPayload(): array
    {
        return [
            'id' => $this->id,
            'type' => 'network_restored',
            'network' => $this->network?->network_name ?? 'Network',
            'title' => $this->displayRestoredTitle(),
            'message' => $this->displayRestoredMessage(),
            'priority' => $this->priority,
            'restored_at' => optional($this->last_restored_at)->toIso8601String(),
            'once_key' => 'network-restored:'.$this->id.':'.optional($this->last_restored_at)->timestamp,
        ];
    }

    private static function normalizedNetworkName(?string $networkName): string
    {
        $network = trim((string) $networkName);

        return $network !== '' ? $network : 'Network';
    }
}

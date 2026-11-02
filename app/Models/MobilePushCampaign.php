<?php

namespace App\Models;

use App\Models\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MobilePushCampaign extends Model
{
    use HasFactory, HasUuids;

    public const DESTINATIONS = [
        'dashboard' => 'Dashboard',
        'services' => 'Services',
        'data' => 'Buy Data',
        'airtime' => 'Buy Airtime',
        'cable' => 'Cable TV',
        'electricity' => 'Electricity',
        'wallet' => 'Wallet',
        'transactions' => 'Transactions',
        'profile' => 'Profile',
    ];

    public const AUDIENCES = [
        'all' => 'All app customers',
        'user_plan' => 'Customers on a selected plan',
        'salespersons' => 'Salesperson accounts',
    ];

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'queued_at' => 'datetime',
            'dispatched_at' => 'datetime',
        ];
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sent_by');
    }

    public function eventKey(): string
    {
        return 'campaign:'.$this->id;
    }

    public function audienceLabel(): string
    {
        if ($this->audience_type === 'user_plan') {
            return 'Plan: '.($this->audiencePlan?->user_plan_name ?? 'Deleted plan');
        }

        return self::AUDIENCES[$this->audience_type] ?? ucfirst((string) $this->audience_type);
    }

    public function audiencePlan()
    {
        return $this->belongsTo(UserPlan::class, 'audience_value');
    }
}

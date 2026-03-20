<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AutomationWalletFunding extends Model
{
    protected $guarded = [];

    protected $casts = [
        'threshold' => 'decimal:2',
        'amount_to_fund' => 'decimal:2',
        'send_failed_notification' => 'boolean',
        'automatic_funding' => 'boolean',
        'last_balance' => 'decimal:2'
    ];

    public function automation()
    {
        return $this->belongsTo(Automation::class);
    }
 
}

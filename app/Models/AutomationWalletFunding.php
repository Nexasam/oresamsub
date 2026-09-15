<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AutomationWalletFunding extends Model
{
    protected $guarded = [];

    protected $hidden = ['provider_account_number'];

    protected $casts = [
        'threshold' => 'decimal:2',
        'amount_to_fund' => 'decimal:2',
        'default_balance' => 'decimal:2',
        'send_failed_notification' => 'boolean',
        'automatic_funding' => 'boolean',
        'last_balance' => 'decimal:2',
        'securewave_customer_created_at' => 'datetime',
        'last_balance_synced_at' => 'datetime',
        'last_funded_at' => 'datetime',
        'provider_account_number' => 'encrypted',
        'securewave_bank_info_saved_at' => 'datetime',
    ];

    public function automation()
    {
        return $this->belongsTo(Automation::class);
    }
 
}

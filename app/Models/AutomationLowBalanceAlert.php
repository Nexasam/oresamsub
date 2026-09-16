<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AutomationLowBalanceAlert extends Model
{
    protected $guarded = [];

    protected $casts = [
        'sent_at' => 'datetime',
    ];
}

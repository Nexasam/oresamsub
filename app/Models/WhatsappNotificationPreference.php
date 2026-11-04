<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappNotificationPreference extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'transactional_enabled' => 'boolean',
            'consented_at' => 'datetime',
            'opted_out_at' => 'datetime',
            'last_inbound_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OreWhatsappConversation extends Model
{
    protected $fillable = [
        'phone',
        'user_id',
        'current_state',
        'payload',
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(
            User::class
        );
    }
}

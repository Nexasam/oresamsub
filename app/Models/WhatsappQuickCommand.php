<?php

namespace App\Models;

use App\Models\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsappQuickCommand extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

    protected $casts = [
        'last_used_at' => 'datetime',
        'usage_count' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

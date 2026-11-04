<?php

namespace App\Models;

use App\Models\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Model;

class WhatsappNotificationDelivery extends Model
{
    use HasUuids;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'attempted_at' => 'datetime',
            'delivered_at' => 'datetime',
            'read_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sent_by');
    }
}

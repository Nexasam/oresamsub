<?php

namespace App\Models;

use App\Models\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MobilePushDelivery extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'attempted_at' => 'datetime',
            'receipt_checked_at' => 'datetime',
            'delivered_at' => 'datetime',
        ];
    }

    public function device()
    {
        return $this->belongsTo(MobileDeviceInstallation::class, 'mobile_device_installation_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AutomationProductPlan extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
    ];
    
    public function productPlan()
    {
        return $this->belongsTo(ProductPlan::class);
    }

    public function automation()
    {
        return $this->belongsTo(Automation::class);
    }
}

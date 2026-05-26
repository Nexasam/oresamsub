<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAutomation extends Model
{
    use HasUuids;

    protected $guarded = [
        // 'user_id',
        // 'automation_id',
        // 'automation_pricing_type',
        // 'pricing_amount',
        // 'first_payment',
        // 'product',
    ];

    protected $casts = [
        'pricing_amount' => 'decimal:2',
        'first_payment' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function automation(): BelongsTo
    {
        return $this->belongsTo(Automation::class);
    }
}
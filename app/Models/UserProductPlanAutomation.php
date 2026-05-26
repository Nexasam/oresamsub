<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProductPlanAutomation extends Model
{
    use HasUuids;

    protected $guarded = [
        // 'user_automation_id',
        // 'product_plan_id',
        // 'automation_product_plan_id',
        // 'priority',
        // 'status',
    ];

    protected $casts = [
        'priority' => 'integer',
        'status' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function productPlan(): BelongsTo
    {
        return $this->belongsTo(ProductPlan::class);
    }

    public function userAutomation(): BelongsTo
    {
        return $this->belongsTo(UserAutomation::class);
    }
}
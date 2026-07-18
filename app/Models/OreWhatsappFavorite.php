<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OreWhatsappFavorite extends Model
{
    protected $fillable = [
        'user_id',
        'shortcut',
        'product_type',
        'product_plan_id',
        'beneficiary_phone',
        'amount',
    ];

    public function productPlan()
    {
        return $this->belongsTo(ProductPlan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
        ];
    }
}

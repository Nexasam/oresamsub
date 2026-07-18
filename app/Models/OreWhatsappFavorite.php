<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OreWhatsappFavorite extends Model
{
    protected $fillable = [
        'user_id',
        'shortcut',
        'product_plan_id',
        'beneficiary_phone',
    ];

    public function productPlan()
    {
        return $this->belongsTo(ProductPlan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

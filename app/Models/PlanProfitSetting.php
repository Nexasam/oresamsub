<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PlanProfitSetting extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

     /**
     * each product belongs to product category 
    **/
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}

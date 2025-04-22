<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BulkDataProductPlans extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

     /**
     * each bulk data plan belongs to a product_plan_category === nullable 
    **/
    public function product_plan_category()
    {
        return $this->belongsTo(ProductPlanCategory::class, 'product_plan_category_id', 'id');
        // return $this->belongsTo(ProductPlanCategory::class, 'product_plan_category_id', 'id')->where('active_status',1);
    }
}

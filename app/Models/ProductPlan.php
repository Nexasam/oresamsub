<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductPlan extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = ['id'];

    
     /**
     * each product plan belongs to a product 
    **/
    public function product()
    {
        // return $this->belongsTo(Product::class, 'product_id', 'id');
        return $this->belongsTo(Product::class, 'product_id', 'id')->where('active_status',1);
    }

     /**
     * each product plan belongs to a product_plan_category === nullable 
    **/
    public function product_plan_category()
    {
        return $this->belongsTo(ProductPlanCategory::class, 'product_plan_category_id', 'id');
        // return $this->belongsTo(ProductPlanCategory::class, 'product_plan_category_id', 'id')->where('active_status',1);
    }

    
}

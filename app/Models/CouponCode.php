<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CouponCode extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

    /**
    * each product_plan_category plan belongs to a product_plan_category 
    **/
    public function product_plan_category()
    {
        return $this->belongsTo(ProductPlanCategory::class, 'product_plan_category_id', 'id');
    }

    // /**
    // * each used_user_coupon_code plan belongs to a used_user_coupon_code 
    // **/
    // public function used_user_coupon_code()
    // {
    //     return $this->belongsTo(UsedUserCouponCode::class, 'user_id', 'id');
    // }
    
}

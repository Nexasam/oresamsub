<?php

namespace App\Models;

use App\Models\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class ProductPlan extends Model
{
    use HasFactory, HasUuids;

    //TODO: revamp productplan with global scope for visibility in all its instance in the code

    protected $guarded = [];

    protected static function booted(): void
    {
        static::saving(function (ProductPlan $productPlan): void {
            if ($productPlan->api_id === null || $productPlan->api_id === '') {
                return;
            }

            $duplicateExists = static::query()
                ->where('api_id', (string) $productPlan->api_id)
                ->when(
                    $productPlan->exists,
                    fn ($query) => $query->whereKeyNot($productPlan->getKey())
                )
                ->exists();

            if ($duplicateExists) {
                throw ValidationException::withMessages([
                    'api_id' => "Product plan API ID '{$productPlan->api_id}' is already in use.",
                ]);
            }
        });
    }

    
     /**
     * each product plan belongs to a product 
    **/
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
        // return $this->belongsTo(Product::class, 'product_id', 'id')->where('active_status',1);
    }

     /**
     * each product plan belongs to a product_plan_category === nullable 
    **/
    public function product_plan_category()
    {
        return $this->belongsTo(ProductPlanCategory::class, 'product_plan_category_id', 'id');
        // return $this->belongsTo(ProductPlanCategory::class, 'product_plan_category_id', 'id')->where('active_status',1);
    }

    public function automation()
    {
        return $this->belongsTo(Automation::class, 'automation_id', 'id');
        // return $this->belongsTo(ProductPlanCategory::class, 'product_plan_category_id', 'id')->where('active_status',1);
    }



    public function reprocess_automation()
    {
        return $this->belongsTo(Automation::class, 'reprocess_automation_id', 'id');
        // return $this->belongsTo(ProductPlanCategory::class, 'product_plan_category_id', 'id')->where('active_status',1);
    }

    public function automationProductPlans()
    {
        return $this->hasMany(AutomationProductPlan::class);
    }
    

    
}

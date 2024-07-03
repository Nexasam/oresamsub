<?php

namespace App\Observers;

use App\Models\ProductPlanCategory;
use App\Models\User;
use App\Models\UserBulkDataWallet;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

class UserObserver implements ShouldHandleEventsAfterCommit
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        //check atleast one bulk data wallet of the user:
        // $check_existence = UserBulkDataWallet::where('user_id',$user->id)->first();
        // if(! $check_existence){
            // Create bulk wallet accounts based on product plan categories
            $product_plan_categories = ProductPlanCategory::with(['product' => function($query){
                $query->where('slug','data');
            }])->pluck('id');
            // return $product_plan_categories;
            // dd($product_plan_categories);
            foreach($product_plan_categories as $product_plan_category){
                UserBulkDataWallet::updateOrCreate([
                    'user_id' => $user->id,
                    'product_plan_category_id' => $product_plan_category,
                ],[]);
            }    
        // }


    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        //
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}

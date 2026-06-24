<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ProductPlan;

class BackfillProductPlanNetworks extends Command
{
    protected $signature = 'plans:backfill-networks';
    protected $description = 'Backfill network field in product_plans from product_plan_categories';

    public function handle()
    {
        $this->info("Starting network backfill...");


        logger('this actually ran');
        ProductPlan::with([
            'product_plan_category.network'
        ])->chunk(200, function ($plans) {

            foreach ($plans as $plan) {

                $network = $plan->product_plan_category?->network;

                if (!$network) {
                    continue;
                }

                $plan->network = strtolower($network->network_name);
                $plan->save();
            }

        });

        $this->info("Network backfill completed successfully.");

        return 0;
    }
}
<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ComputeReferralCommission extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'compute-referral-commission';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Compute Referral Commission';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //start adding commission from this date:
        // $co
    }
}

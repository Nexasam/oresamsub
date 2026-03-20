<?php

namespace App\Console\Commands;

use App\Services\Automation\WalletAutoFundingService;
use Illuminate\Console\Command;

class RunWalletAutoFunding extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'automation:wallet-funding';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run wallet auto funding automation';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            app(WalletAutoFundingService::class)->run();

            $this->info('Wallet auto funding executed successfully');

        } catch (\Throwable $e) {
            \Log::error('Wallet automation cron failed', [
                'error' => $e->getMessage()
            ]);
        }
    }
}

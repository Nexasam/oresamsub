<?php
namespace App\Console\Commands;

use App\Models\Transaction;
use App\Models\UserContact;
use Illuminate\Console\Command;

class SyncUserContactsFromTransactions extends Command
{
 
     /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-contacts-from-transactions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync user contacts from recent data transactions';


    public function handle(): void
    {
        Transaction::query()
            ->where('transaction_category', 'data')
            ->where('created_at', '>=', now()->subDays(60))
            ->select('user_id', 'phone_number','product_plan_id', 'created_at')
            ->distinct()
            ->chunkById(500, function ($transactions) {

                foreach ($transactions as $tx) {

                    UserContact::updateOrCreate(
                        [
                            'user_id'      => $tx->user_id,
                            'phone_number' => $tx->phone_number,
                        ],
                        [
                            // only set network if we have one
                            'product_plan_id'   => $tx->product_plan_id,
                            'last_used_at' => $tx->created_at,
                        ]
                    );
                }
            });
    }
}

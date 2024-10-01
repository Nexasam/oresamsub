<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ZerorizeNegativeBalances extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:zerorize-negative-balances';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Zerorize Negative Wallets';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::select('main_wallet','id')->get();
        foreach($users as $user){
            if($user->main_wallet < 0){
                User::where('id',$user->id)->update([
                    'main_wallet' => 0
                ]);
            }
        }
        
    }
}

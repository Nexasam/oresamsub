<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ClearErrorLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear-error-logs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear Error Logs';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        Artisan::call('clear-error-logs');
        
        // $users = User::select('main_wallet','id','email')->get();
        // foreach($users as $user){
        //     if($user->main_wallet < 0){
        //         User::where('id',$user->id)->update([
        //             'main_wallet' => 0
        //         ]);
        //         logger('Wallet of '.$user->email.'  with value of '.$user->main_wallet. ' has been zerorized');
        //     }
        // }

        logger('sss');
        
    }
}

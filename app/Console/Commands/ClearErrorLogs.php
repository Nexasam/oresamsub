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
    
        //add some stuffs hhere

        // logger('sss');
        
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

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
    public function handle(Filesystem $files): int
    {
        $logDirectory = storage_path('logs');
        $deleted = 0;

        if ($files->isDirectory($logDirectory)) {
            foreach ($files->glob($logDirectory.'/*.log') as $logFile) {
                if ($files->isFile($logFile) && $files->delete($logFile)) {
                    $deleted++;
                }
            }
        }

        $this->components->info("Cleared {$deleted} error log file(s).");

        return self::SUCCESS;
    }
}

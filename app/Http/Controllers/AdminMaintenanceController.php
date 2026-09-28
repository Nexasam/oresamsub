<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminMaintenanceController extends Controller
{
    private const ACTIONS = [
        'migrate' => [
            'label' => 'Run pending database migrations',
            'description' => 'Creates missing tables/columns after a deployment. This fixes errors like "table does not exist".',
            'command' => 'migrate',
            'parameters' => ['--force' => true],
            'warning' => 'Changes database structure. Only run after deploying trusted code.',
        ],
        'optimize_clear' => [
            'label' => 'Clear all Laravel optimization caches',
            'description' => 'Runs optimize:clear to clear config, route, event, compiled and view caches.',
            'command' => 'optimize:clear',
            'parameters' => [],
            'warning' => null,
        ],
        'config_clear' => [
            'label' => 'Clear config cache',
            'description' => 'Refreshes environment/config values without touching the database.',
            'command' => 'config:clear',
            'parameters' => [],
            'warning' => null,
        ],
        'route_clear' => [
            'label' => 'Clear route cache',
            'description' => 'Refreshes route definitions after a deployment.',
            'command' => 'route:clear',
            'parameters' => [],
            'warning' => null,
        ],
        'view_clear' => [
            'label' => 'Clear compiled views',
            'description' => 'Refreshes Blade templates when old UI is still showing.',
            'command' => 'view:clear',
            'parameters' => [],
            'warning' => null,
        ],
        'clean_laravel_logs' => [
            'label' => 'Clean Laravel logs',
            'description' => 'Deletes only .log files inside storage/logs. Useful when logs become too large.',
            'command' => null,
            'parameters' => [],
            'warning' => 'This removes local Laravel log file contents/history. Download any logs you need before running.',
        ],
        'schedule_run' => [
            'label' => 'Run scheduler once',
            'description' => 'Manually runs due scheduled tasks once. Use this only when you understand the scheduled jobs.',
            'command' => 'schedule:run',
            'parameters' => [],
            'warning' => 'May execute due scheduled jobs such as notifications, retries, imports or cleanup tasks.',
        ],
    ];

    public function index(): View
    {
        return view('admin.maintenance.index', [
            'actions' => self::ACTIONS,
            'migrationStatus' => $this->commandPreview('migrate:status', ['--no-interaction' => true]),
        ]);
    }

    public function run(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'action' => ['required', Rule::in(array_keys(self::ACTIONS))],
            'confirm' => ['accepted'],
        ]);

        $actionKey = $validated['action'];
        $action = self::ACTIONS[$actionKey];

        if (function_exists('set_time_limit')) {
            set_time_limit(180);
        }

        try {
            if ($actionKey === 'clean_laravel_logs') {
                [$exitCode, $output] = $this->cleanLaravelLogs();
            } else {
                $exitCode = Artisan::call($action['command'], $action['parameters'] + ['--no-interaction' => true]);
                $output = trim(Artisan::output());
            }

            Log::notice('Admin maintenance command executed.', [
                'admin_id' => auth()->id(),
                'action' => $actionKey,
                'command' => $action['command'],
                'exit_code' => $exitCode,
            ]);

            Session::flash($exitCode === 0 ? 'success' : 'failure', $action['label'].' finished with exit code '.$exitCode.'.');
            Session::flash('maintenance_output', $output !== '' ? $output : 'Command completed with no output.');
        } catch (\Throwable $exception) {
            Log::error('Admin maintenance command failed.', [
                'admin_id' => auth()->id(),
                'action' => $actionKey,
                'command' => $action['command'],
                'message' => $exception->getMessage(),
            ]);

            Session::flash('failure', $action['label'].' failed: '.$exception->getMessage());
            Session::flash('maintenance_output', $exception->getMessage());
        }

        return back();
    }

    private function commandPreview(string $command, array $parameters = []): string
    {
        try {
            Artisan::call($command, $parameters);

            return trim(Artisan::output()) ?: 'No output.';
        } catch (\Throwable $exception) {
            return 'Unable to read status: '.$exception->getMessage();
        }
    }

    private function cleanLaravelLogs(): array
    {
        $logDirectory = storage_path('logs');
        $deleted = 0;
        $freedBytes = 0;
        $failures = [];

        foreach (glob($logDirectory.DIRECTORY_SEPARATOR.'*.log') ?: [] as $path) {
            if (! is_file($path) || pathinfo($path, PATHINFO_EXTENSION) !== 'log') {
                continue;
            }

            $freedBytes += filesize($path) ?: 0;

            if (@unlink($path)) {
                $deleted++;
            } else {
                $failures[] = basename($path);
            }
        }

        $freedMegabytes = number_format($freedBytes / 1024 / 1024, 2);
        $output = "Deleted {$deleted} Laravel log file(s). Freed approximately {$freedMegabytes} MB.";

        if ($failures !== []) {
            $output .= "\nFailed to delete: ".implode(', ', $failures);
        }

        return [$failures === [] ? 0 : 1, $output];
    }
}

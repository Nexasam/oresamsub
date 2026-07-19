<?php

use Illuminate\Support\Facades\File;

it('clears log files without invoking itself recursively', function () {
    $originalStoragePath = storage_path();
    $temporaryStoragePath = sys_get_temp_dir().'/oresamsub-clear-logs-'.uniqid();

    File::ensureDirectoryExists($temporaryStoragePath.'/logs');
    File::put($temporaryStoragePath.'/logs/laravel.log', 'error');
    File::put($temporaryStoragePath.'/logs/laravel-2026-07-19.log', 'error');
    File::put($temporaryStoragePath.'/logs/.gitignore', "*\n!.gitignore\n");

    app()->useStoragePath($temporaryStoragePath);

    try {
        $this->artisan('clear-error-logs')
            ->expectsOutputToContain('Cleared 2 error log file(s).')
            ->assertSuccessful();

        expect(File::exists($temporaryStoragePath.'/logs/laravel.log'))->toBeFalse()
            ->and(File::exists($temporaryStoragePath.'/logs/laravel-2026-07-19.log'))->toBeFalse()
            ->and(File::exists($temporaryStoragePath.'/logs/.gitignore'))->toBeTrue();
    } finally {
        app()->useStoragePath($originalStoragePath);
        File::deleteDirectory($temporaryStoragePath);
    }
});

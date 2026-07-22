<?php

use function Pest\Laravel\getJson;

it('exposes the versioned mobile API health endpoint', function () {
    getJson('/api/mobile/v1/health')
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.api_version', 'v1')
        ->assertJsonStructure([
            'success',
            'message',
            'data' => ['api_version', 'server_time'],
            'meta',
            'errors',
        ]);
});

it('returns the public mobile feature configuration', function () {
    getJson('/api/mobile/v1/config')
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.features.push_notifications', true)
        ->assertJsonStructure([
            'data' => [
                'api_version',
                'minimum_app_version',
                'latest_app_version',
                'force_update',
                'maintenance_mode',
                'features',
            ],
        ]);
});

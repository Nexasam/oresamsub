<?php

use function Pest\Laravel\getJson;
use function Pest\Laravel\get;

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

it('publishes the legal and account deletion pages used by the mobile app', function () {
    get('/privacy-policy')->assertOk()->assertSeeText('Privacy Policy');
    get('/terms')->assertOk()->assertSeeText('Terms of Service');
    get('/account-deletion')->assertOk()->assertSeeText('OresamSub account deletion');
});

it('returns dependable fallback support details when landing-page settings are missing', function () {
    getJson('/api/mobile/v1/support')
        ->assertOk()
        ->assertJsonPath('data.email', 'info@oresamsub.com')
        ->assertJsonPath('data.phone', '08168509044')
        ->assertJsonPath('data.whatsapp', null);
});

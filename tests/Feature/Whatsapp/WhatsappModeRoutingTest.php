<?php

use App\Models\OreWhatsappConfig;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

beforeEach(function () {
    OreWhatsappConfig::create([
        'token' => 'test-meta-token',
        'phone_number_id' => '123456789',
    ]);

    Http::fake([
        'graph.facebook.com/*' => Http::response([
            'messages' => [['id' => 'message-id']],
        ]),
    ]);
});

function whatsappTextPayload(string $text): array
{
    return [
        'entry' => [[
            'changes' => [[
                'value' => [
                    'messages' => [[
                        'from' => '2348168509044',
                        'text' => ['body' => $text],
                    ]],
                ],
            ]],
        ]],
    ];
}

it('uses the guided menu by default for greetings', function () {
    User::factory()->create([
        'phone_number' => '08168509044',
    ]);

    $this->postJson('/api/webhook/whatsapp', whatsappTextPayload('hello'))
        ->assertOk()
        ->assertJson(['ok' => true]);

    Http::assertSent(fn (Request $request) =>
        $request['interactive']['type'] === 'list'
        && collect($request['interactive']['action']['sections'][0]['rows'])
            ->contains('id', 'switch_to_quick_commands')
    );
});

it('persists quick commands mode and clears guided state', function () {
    $user = User::factory()->create([
        'phone_number' => '08168509044',
    ]);

    Cache::put('ore_session:2348168509044', ['started_at' => now()]);

    $payload = whatsappTextPayload('ignored');
    $payload['entry'][0]['changes'][0]['value']['messages'][0] = [
        'from' => '2348168509044',
        'interactive' => [
            'button_reply' => [
                'id' => 'switch_to_quick_commands',
            ],
        ],
    ];

    $this->postJson('/api/webhook/whatsapp', $payload)
        ->assertOk()
        ->assertJson(['ok' => true]);

    expect($user->fresh()->whatsapp_mode)->toBe('power')
        ->and(Cache::has('ore_session:2348168509044'))->toBeFalse();

    Http::assertSent(fn (Request $request) =>
        $request['interactive']['type'] === 'button'
        && $request['interactive']['action']['buttons'][0]['reply']['id']
            === 'switch_to_guided_menu'
    );
});

it('returns a quick-command user to the guided menu', function () {
    $user = User::factory()->create([
        'phone_number' => '08168509044',
        'whatsapp_mode' => 'power',
    ]);

    Cache::put('wa_session:2348168509044', [
        'status' => 'data_phone_required',
    ]);

    $payload = whatsappTextPayload('ignored');
    $payload['entry'][0]['changes'][0]['value']['messages'][0] = [
        'from' => '2348168509044',
        'interactive' => [
            'button_reply' => [
                'id' => 'switch_to_guided_menu',
            ],
        ],
    ];

    $this->postJson('/api/webhook/whatsapp', $payload)
        ->assertOk()
        ->assertJson(['ok' => true]);

    expect($user->fresh()->whatsapp_mode)->toBe('normal')
        ->and(Cache::has('wa_session:2348168509044'))->toBeFalse()
        ->and(Cache::has('ore_session:2348168509044'))->toBeTrue();

    Http::assertSent(fn (Request $request) =>
        $request['interactive']['type'] === 'list'
        && collect($request['interactive']['action']['sections'][0]['rows'])
            ->contains('id', 'switch_to_quick_commands')
    );
});

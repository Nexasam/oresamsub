<?php

use App\Models\OreWhatsappConfig;
use App\Models\Automation;
use App\Models\Network;
use App\Models\OreWhatsappConversation;
use App\Models\Product;
use App\Models\ProductPlan;
use App\Models\ProductPlanCategory;
use App\Models\User;
use App\Models\WhatsappConfig;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

beforeEach(function () {
    WhatsappConfig::create([
        'token' => 'main-test-meta-token',
        'phone_number_id' => '987654321',
    ]);

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

function whatsappInteractivePayload(string $type, string $id): array
{
    return [
        'entry' => [[
            'changes' => [[
                'value' => [
                    'messages' => [[
                        'from' => '2348168509044',
                        'interactive' => [
                            $type => ['id' => $id],
                        ],
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

it('continues the guided data flow after a plan size is selected', function () {
    $user = User::factory()->create([
        'phone_number' => '08168509044',
    ]);
    $network = Network::create([
        'api_id' => '1',
        'network_name' => 'MTN',
    ]);
    $product = Product::create([
        'slug' => 'data',
        'product_name' => 'Data',
    ]);
    $automation = Automation::create([
        'automation_name' => 'Test Automation',
        'slug' => 'test-automation',
        'domain_url' => 'https://example.test',
    ]);
    $category = ProductPlanCategory::create([
        'product_plan_category_name' => 'MTN SME',
        'automation_id' => $automation->id,
        'product_id' => $product->id,
        'network_id' => $network->id,
    ]);
    $plan = ProductPlan::create([
        'id' => 'A0B1C2D3-E4F5-4678-9123-ABCDEF123456',
        'product_plan_name' => 'MTN SME 1GB',
        'product_plan_category_id' => $category->id,
        'automation_product_plan_id' => 'mtn-sme-1gb',
        'automation_id' => $automation->id,
        'cost_price' => '290',
        'data_size_in_mb' => '1000.00',
        'validity_in_days' => '30',
        'default_selling_price' => '300',
        'user_level_1_selling_price' => '300',
    ]);
    ProductPlan::create([
        'id' => 'B0B1C2D3-E4F5-4678-9123-ABCDEF123456',
        'product_plan_name' => 'MTN SME 1GB Alternative',
        'product_plan_category_id' => $category->id,
        'automation_product_plan_id' => 'mtn-sme-1gb-alt',
        'automation_id' => $automation->id,
        'cost_price' => '295',
        'data_size_in_mb' => '1000.00',
        'validity_in_days' => '30',
        'default_selling_price' => '305',
        'user_level_1_selling_price' => '305',
    ]);

    OreWhatsappConversation::create([
        'phone' => '2348168509044',
        'user_id' => $user->id,
        'current_state' => 'data_type',
        'payload' => [
            'network_id' => $network->id,
            'network_name' => 'MTN',
            'product_id' => $product->id,
            'data_size_page' => 0,
        ],
    ]);

    $this->postJson(
        '/api/webhook/whatsapp',
        whatsappInteractivePayload('button_reply', 'data_size_1000')
    )->assertOk()->assertJson(['ok' => true]);

    $conversation = OreWhatsappConversation::where('phone', '2348168509044')->first();

    expect($conversation->current_state)->toBe('data_plan');

    Http::assertSent(fn (Request $request) =>
        data_get($request->data(), 'interactive.type') === 'button'
        && collect(data_get($request->data(), 'interactive.action.buttons', []))
            ->contains(fn (array $button) => $button['reply']['id'] === $plan->id)
        && collect(data_get($request->data(), 'interactive.action.buttons', []))
            ->pluck('reply.title')->duplicates()->isEmpty()
    );

    $conversation->update([
        'current_state' => 'data_type',
        'payload' => [
            'network_id' => $network->id,
            'network_name' => 'MTN',
            'product_id' => $product->id,
            'data_size_page' => 0,
        ],
    ]);

    $this->postJson(
        '/api/webhook/whatsapp',
        whatsappInteractivePayload('list_reply', 'data_size_1000')
    )->assertOk()->assertJson(['ok' => true]);

    expect($conversation->fresh()->current_state)->toBe('data_plan');

    $this->postJson(
        '/api/webhook/whatsapp',
        whatsappInteractivePayload('button_reply', $plan->id)
    )->assertOk()->assertJson(['ok' => true]);

    expect($conversation->fresh()->current_state)->toBe('data_phone');
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

it('supports normal and power as fallback switching commands', function () {
    $user = User::factory()->create([
        'phone_number' => '08168509044',
    ]);

    $this->postJson('/api/webhook/whatsapp', whatsappTextPayload('power'))
        ->assertOk();

    expect($user->fresh()->whatsapp_mode)->toBe('power');

    $this->postJson('/api/webhook/whatsapp', whatsappTextPayload('normal'))
        ->assertOk();

    expect($user->fresh()->whatsapp_mode)->toBe('normal');
});

it('shows only the selected experience when start is clicked', function () {
    User::factory()->create([
        'phone_number' => '08168509044',
        'whatsapp_mode' => 'power',
    ]);

    $payload = whatsappTextPayload('ignored');
    $payload['entry'][0]['changes'][0]['value']['messages'][0] = [
        'from' => '2348168509044',
        'interactive' => [
            'button_reply' => [
                'id' => 'start_over',
            ],
        ],
    ];

    $this->postJson('/api/webhook/whatsapp', $payload)
        ->assertOk();

    expect(Http::recorded())->toHaveCount(1);

    Http::assertSent(fn (Request $request) =>
        $request['interactive']['type'] === 'button'
        && str_contains(
            $request['interactive']['body']['text'],
            '📶 DATA'
        )
        && str_contains($request['interactive']['body']['text'], '📞 AIRTIME')
        && str_contains($request['interactive']['body']['text'], '📺 CABLE TV')
        && str_contains($request['interactive']['body']['text'], '💡 ELECTRICITY')
    );
});

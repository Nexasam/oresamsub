<?php

use App\Jobs\SendWhatsappTransactionAlert;
use App\Models\Automation;
use App\Models\Network;
use App\Models\Product;
use App\Models\ProductPlan;
use App\Models\ProductPlanCategory;
use App\Models\Role;
use App\Models\Transaction;
use App\Models\User;
use App\Models\WhatsappConfig;
use App\Models\WhatsappNotificationDelivery;
use App\Models\WhatsappNotificationPreference;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;

function whatsappAlertPlan(): ProductPlan
{
    $automation = Automation::create([
        'automation_name' => 'WhatsApp Test Provider',
        'slug' => 'whatsapp-test-'.fake()->unique()->slug(),
        'domain_url' => 'https://provider.test',
    ]);
    $product = Product::create([
        'slug' => 'data-'.fake()->unique()->slug(),
        'product_name' => 'Data',
        'visibility' => '1',
        'active_status' => '1',
    ]);
    $network = Network::create([
        'network_name' => fake()->unique()->word(),
        'api_id' => fake()->unique()->numerify('####'),
        'visibility' => '1',
    ]);
    $category = ProductPlanCategory::create([
        'product_plan_category_name' => fake()->unique()->words(2, true),
        'automation_id' => $automation->id,
        'product_id' => $product->id,
        'network_id' => $network->id,
        'visibility' => '1',
    ]);

    return ProductPlan::create([
        'product_plan_name' => '1GB MTN MONTHLY',
        'product_plan_category_id' => $category->id,
        'automation_product_plan_id' => 'provider-plan',
        'automation_id' => $automation->id,
        'default_selling_price' => '490',
        'user_level_1_selling_price' => '490',
        'data_size_in_mb' => '1000',
        'validity_in_days' => '30',
        'visibility' => '1',
        'public_visibility' => '1',
        'active_status' => '1',
    ]);
}

function whatsappAlertTransaction(User $user, ProductPlan $plan, array $overrides = []): Transaction
{
    return Transaction::create(array_merge([
        'user_id' => $user->id,
        'product_plan_id' => $plan->id,
        'transaction_category' => 'data',
        'status' => '0',
        'wallet_category' => 'main_wallet',
        'phone_number' => '08030001234',
        'amount' => '490',
        'discounted_amount' => '490',
        'balance_before' => '1000',
        'balance_after' => '510',
        'description' => 'Data purchase',
        'txn_reference' => 'WA-'.fake()->unique()->numerify('########'),
    ], $overrides));
}

beforeEach(function () {
    WhatsappConfig::create([
        'token' => 'test-meta-token',
        'phone_number_id' => '123456789',
    ]);
});

it('queues one WhatsApp alert only when a transaction becomes failed or refunded', function () {
    Queue::fake();
    $user = User::factory()->create(['whatsapp_number' => '08030000000']);
    $transaction = whatsappAlertTransaction($user, whatsappAlertPlan());

    Queue::assertNotPushed(SendWhatsappTransactionAlert::class);

    $transaction->update(['status' => '-1', 'user_screen_message' => 'Provider unavailable.']);
    $transaction->update(['user_screen_message' => 'Still unavailable.']);

    Queue::assertPushed(SendWhatsappTransactionAlert::class, 1);
    Queue::assertPushed(fn (SendWhatsappTransactionAlert $job) => $job->transactionId === $transaction->id && $job->notificationType === 'failed'
    );
});

it('sends a deduplicated free-form alert inside the customer service window', function () {
    Queue::fake();
    Http::fake(['graph.facebook.com/*' => Http::response(['messages' => [['id' => 'wamid.failed-1']]])]);
    $user = User::factory()->create([
        'first_name' => 'Samuel',
        'whatsapp_number' => '08031234567',
    ]);
    WhatsappNotificationPreference::create([
        'user_id' => $user->id,
        'transactional_enabled' => true,
        'consented_at' => now(),
        'last_inbound_at' => now()->subHour(),
    ]);
    $transaction = whatsappAlertTransaction($user, whatsappAlertPlan(), [
        'status' => '-1',
        'user_screen_message' => 'The network provider is temporarily unavailable.',
        'admin_screen_message' => ['token' => 'NEVER-SEND-THIS', 'raw' => 'provider response'],
    ]);

    $job = new SendWhatsappTransactionAlert($transaction->id, 'failed');
    app()->call([$job, 'handle']);
    app()->call([$job, 'handle']);

    Http::assertSentCount(1);
    Http::assertSent(fn (Request $request) => $request['type'] === 'text'
        && str_contains($request['text']['body'], 'Transaction Failed')
        && str_contains($request['text']['body'], 'temporarily unavailable')
        && ! str_contains($request['text']['body'], 'NEVER-SEND-THIS')
        && ! str_contains($request['text']['body'], '08030001234')
    );
    expect(WhatsappNotificationDelivery::first()->status)->toBe('sent')
        ->and(WhatsappNotificationDelivery::first()->payload_type)->toBe('text');
});

it('uses the approved utility template outside the service window', function () {
    Queue::fake();
    Http::fake(['graph.facebook.com/*' => Http::response(['messages' => [['id' => 'wamid.refund-1']]])]);
    $user = User::factory()->create([
        'first_name' => 'Ada',
        'whatsapp_number' => '2348031234567',
    ]);
    WhatsappNotificationPreference::create([
        'user_id' => $user->id,
        'transactional_enabled' => true,
        'consented_at' => now()->subDays(2),
        'last_inbound_at' => now()->subHours(25),
    ]);
    $transaction = whatsappAlertTransaction($user, whatsappAlertPlan(), [
        'status' => '2',
        'user_screen_message' => 'Provider rejected the request.',
    ]);

    app()->call([(new SendWhatsappTransactionAlert($transaction->id, 'refunded')), 'handle']);

    Http::assertSent(fn (Request $request) => $request['type'] === 'template'
        && $request['template']['name'] === 'oresamsub_transaction_refunded'
        && $request['template']['components'][0]['parameters'][5]['text'] === 'Provider rejected the request.'
    );
    expect(WhatsappNotificationDelivery::first()->status)->toBe('sent')
        ->and(WhatsappNotificationDelivery::first()->payload_type)->toBe('template');
});

it('does not notify a customer who has not opted in', function () {
    Queue::fake();
    Http::fake();
    $user = User::factory()->create(['whatsapp_number' => '08031234567']);
    $transaction = whatsappAlertTransaction($user, whatsappAlertPlan(), ['status' => '-1']);

    app()->call([(new SendWhatsappTransactionAlert($transaction->id, 'failed')), 'handle']);

    Http::assertNothingSent();
    expect(WhatsappNotificationDelivery::first()->status)->toBe('skipped')
        ->and(WhatsappNotificationDelivery::first()->failure_code)->toBe('not_opted_in');
});

it('lets a linked customer opt in and records Meta delivery updates', function () {
    Queue::fake();
    Http::fake(['graph.facebook.com/*' => Http::response(['messages' => [['id' => 'wamid.confirmation']]])]);
    $user = User::factory()->create([
        'phone_number' => '08031234567',
        'whatsapp_number' => '08031234567',
    ]);

    $this->postJson('/whatsapp/webhook', [
        'entry' => [[
            'changes' => [[
                'value' => [
                    'messages' => [[
                        'from' => '2348031234567',
                        'text' => ['body' => 'NOTIFICATIONS ON'],
                    ]],
                ],
            ]],
        ]],
    ])->assertOk();

    $preference = WhatsappNotificationPreference::where('user_id', $user->id)->firstOrFail();
    expect($preference->transactional_enabled)->toBeTrue()
        ->and($preference->consented_at)->not->toBeNull()
        ->and($preference->last_inbound_at)->not->toBeNull();

    $transaction = whatsappAlertTransaction($user, whatsappAlertPlan(), ['status' => '-1']);
    $delivery = WhatsappNotificationDelivery::create([
        'user_id' => $user->id,
        'transaction_id' => $transaction->id,
        'event_key' => 'transaction:delivery-status-test:failed',
        'notification_type' => 'failed',
        'status' => 'sent',
        'meta_message_id' => 'wamid.status-test',
    ]);

    $this->postJson('/whatsapp/webhook', [
        'entry' => [[
            'changes' => [[
                'value' => [
                    'statuses' => [[
                        'id' => 'wamid.status-test',
                        'status' => 'delivered',
                    ]],
                ],
            ]],
        ]],
    ])->assertOk();

    expect($delivery->fresh()->status)->toBe('delivered')
        ->and($delivery->fresh()->delivered_at)->not->toBeNull();
});

it('handles notification consent on the live OresamSub WhatsApp webhook', function () {
    Http::fake(['graph.facebook.com/*' => Http::response(['messages' => [['id' => 'wamid.live-confirmation']]])]);
    $user = User::factory()->create([
        'phone_number' => '08035551234',
        'whatsapp_number' => '08035551234',
    ]);

    $this->postJson('/api/webhook/whatsapp', [
        'entry' => [[
            'changes' => [[
                'value' => [
                    'messages' => [[
                        'from' => '2348035551234',
                        'text' => ['body' => 'NOTIFICATIONS ON'],
                    ]],
                ],
            ]],
        ]],
    ])->assertOk();

    expect(WhatsappNotificationPreference::where('user_id', $user->id)->firstOrFail())
        ->transactional_enabled->toBeTrue()
        ->consent_source->toBe('whatsapp_command');
});

it('lets an admin send one support message to one customer inside the service window', function () {
    Http::fake(['graph.facebook.com/*' => Http::response(['messages' => [['id' => 'wamid.admin-message']]])]);
    $role = Role::firstOrCreate(['role_name' => 'Admin']);
    $admin = User::factory()->create(['role_id' => $role->id]);
    $customer = User::factory()->create(['whatsapp_number' => '08031234567']);
    WhatsappNotificationPreference::create([
        'user_id' => $customer->id,
        'last_inbound_at' => now()->subHour(),
    ]);

    $this->actingAs($admin)
        ->post(route('admin.whatsapp_messages.store'), [
            'user_id' => $customer->id,
            'message' => 'Hello, we are following up on your transaction.',
        ])
        ->assertRedirect(route('admin.whatsapp_messages.index'))
        ->assertSessionHas('success');

    Http::assertSentCount(1);
    expect(WhatsappNotificationDelivery::first())
        ->notification_type->toBe('admin_manual')
        ->sent_by->toBe($admin->id)
        ->user_id->toBe($customer->id)
        ->status->toBe('sent');
});

it('blocks a free-form admin message outside the customer service window', function () {
    Http::fake();
    $role = Role::firstOrCreate(['role_name' => 'Admin']);
    $admin = User::factory()->create(['role_id' => $role->id]);
    $customer = User::factory()->create(['whatsapp_number' => '08031234567']);
    WhatsappNotificationPreference::create([
        'user_id' => $customer->id,
        'last_inbound_at' => now()->subHours(25),
    ]);

    $this->actingAs($admin)
        ->from(route('admin.whatsapp_messages.index'))
        ->post(route('admin.whatsapp_messages.store'), [
            'user_id' => $customer->id,
            'message' => 'This message must not be sent.',
        ])
        ->assertRedirect(route('admin.whatsapp_messages.index'))
        ->assertSessionHasErrors('user_id');

    Http::assertNothingSent();
    expect(WhatsappNotificationDelivery::count())->toBe(0);
});

it('lets an admin queue a failed transaction alert without permitting duplicates', function () {
    Queue::fake([SendWhatsappTransactionAlert::class]);
    $role = Role::firstOrCreate(['role_name' => 'Admin']);
    $admin = User::factory()->create(['role_id' => $role->id]);
    $customer = User::factory()->create(['whatsapp_number' => '08031234567']);
    WhatsappNotificationPreference::create([
        'user_id' => $customer->id,
        'transactional_enabled' => true,
        'consented_at' => now(),
        'last_inbound_at' => now(),
    ]);
    $transaction = Transaction::withoutEvents(
        fn () => whatsappAlertTransaction($customer, whatsappAlertPlan(), ['status' => '-1'])
    );

    $this->actingAs($admin)
        ->post(route('admin.whatsapp_messages.transactions.alert', $transaction))
        ->assertSessionHas('success');

    Queue::assertPushed(SendWhatsappTransactionAlert::class, 1);

    WhatsappNotificationDelivery::create([
        'user_id' => $customer->id,
        'transaction_id' => $transaction->id,
        'event_key' => "transaction:{$transaction->id}:failed",
        'notification_type' => 'failed',
        'status' => 'sent',
    ]);

    $this->actingAs($admin)
        ->post(route('admin.whatsapp_messages.transactions.alert', $transaction))
        ->assertSessionHas('failure');

    Queue::assertPushed(SendWhatsappTransactionAlert::class, 1);
});

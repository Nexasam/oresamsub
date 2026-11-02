<?php

use App\Jobs\DispatchMobilePushCampaign;
use App\Jobs\SendMobilePushNotification;
use App\Models\MobileDeviceInstallation;
use App\Models\MobilePushCampaign;
use App\Models\Role;
use App\Models\User;
use App\Models\UserPlan;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;

function mobilePushAdmin(string $email): User
{
    $role = Role::firstOrCreate(['role_name' => 'Admin']);

    return User::factory()->create(['role_id' => $role->id, 'email' => $email]);
}

function registeredPushDevice(User $user, string $token): MobileDeviceInstallation
{
    return MobileDeviceInstallation::create([
        'user_id' => $user->id,
        'device_uuid' => (string) Str::uuid(),
        'expo_push_token' => "ExponentPushToken[{$token}]",
        'platform' => 'android',
        'enabled' => true,
    ]);
}

it('allows programmer access to queue one campaign and blocks duplicate form submissions', function () {
    Queue::fake([DispatchMobilePushCampaign::class]);
    $programmer = mobilePushAdmin('adebsholey4real@gmail.com');
    $ordinaryAdmin = mobilePushAdmin('ordinary-push-admin@example.com');
    $token = (string) Str::uuid();
    $payload = [
        'request_token' => $token,
        'title' => 'Get free N200 today',
        'message' => 'Open OresamSub and complete today’s offer.',
        'destination' => 'dashboard',
        'audience_type' => 'all',
        'audience_value' => null,
    ];

    $this->actingAs($ordinaryAdmin)
        ->post(route('admin.mobile_push_campaigns.store'), $payload)
        ->assertForbidden();

    $this->actingAs($programmer)
        ->post(route('admin.mobile_push_campaigns.store'), $payload)
        ->assertRedirect(route('admin.mobile_push_campaigns.index'))
        ->assertSessionHas('success');

    $this->actingAs($programmer)
        ->post(route('admin.mobile_push_campaigns.store'), $payload)
        ->assertRedirect(route('admin.mobile_push_campaigns.index'));

    expect(MobilePushCampaign::count())->toBe(1);
    Queue::assertPushed(DispatchMobilePushCampaign::class, 1);
});

it('dispatches a plan campaign only to registered customers on that plan', function () {
    Queue::fake([SendMobilePushNotification::class]);
    $plan = UserPlan::create([
        'user_plan_name' => 'Push Target Plan',
        'plan_level' => 6,
        'is_default' => 0,
        'visibility' => 1,
    ]);
    $target = User::factory()->create(['user_plan_id' => $plan->id]);
    $other = User::factory()->create();
    registeredPushDevice($target, 'campaign_target');
    registeredPushDevice($other, 'campaign_other');
    $campaign = MobilePushCampaign::create([
        'request_token' => (string) Str::uuid(),
        'title' => 'Plan offer',
        'message' => 'This offer is for your customer plan.',
        'destination' => 'data',
        'audience_type' => 'user_plan',
        'audience_value' => $plan->id,
        'status' => 'queued',
        'queued_at' => now(),
    ]);

    (new DispatchMobilePushCampaign($campaign->id))->handle();

    Queue::assertPushed(SendMobilePushNotification::class, 1);
    Queue::assertPushed(SendMobilePushNotification::class, fn ($job) =>
        $job->userId === $target->id
        && $job->data['screen'] === 'data'
        && $job->category === 'broadcast'
    );
    expect($campaign->fresh())
        ->status->toBe('dispatched')
        ->recipient_users->toBe(1)
        ->recipient_devices->toBe(1);
});

it('dispatches a salesperson campaign only to registered salesperson accounts', function () {
    Queue::fake([SendMobilePushNotification::class]);
    $salesperson = User::factory()->create(['is_salesperson' => true]);
    $customer = User::factory()->create(['is_salesperson' => false]);
    registeredPushDevice($salesperson, 'campaign_salesperson');
    registeredPushDevice($customer, 'campaign_customer');
    $campaign = MobilePushCampaign::create([
        'request_token' => (string) Str::uuid(),
        'title' => 'Sales update',
        'message' => 'New sales prices are available.',
        'destination' => 'services',
        'audience_type' => 'salespersons',
        'status' => 'queued',
        'queued_at' => now(),
    ]);

    (new DispatchMobilePushCampaign($campaign->id))->handle();

    Queue::assertPushed(SendMobilePushNotification::class, 1);
    Queue::assertPushed(SendMobilePushNotification::class, fn ($job) => $job->userId === $salesperson->id);
    expect($campaign->fresh()->recipient_users)->toBe(1);
});

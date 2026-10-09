<?php

use App\Models\Announcement;
use App\Models\Network;
use App\Models\NetworkIssueAlert;
use App\Models\NetworkIssueAlertRead;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Blade;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

function networkIssueAdmin(): User
{
    $role = Role::firstOrCreate(['role_name' => 'Admin']);

    return User::factory()->create(['role_id' => $role->id]);
}

it('shows active network issue alerts before normal announcements', function () {
    $network = Network::create([
        'network_name' => 'MTN',
        'api_id' => 'mtn-issue-test',
        'visibility' => '1',
    ]);

    NetworkIssueAlert::create([
        'network_id' => $network->id,
        'title' => 'MTN network update',
        'message' => 'MTN is currently experiencing delays from the telco.',
        'is_active' => true,
        'priority' => 1,
    ]);

    Announcement::create([
        'title' => 'General promo announcement',
        'description' => '<p>Normal announcement content.</p>',
        'position' => '1',
        'status' => '1',
    ]);

    $html = Blade::render("@include('partials.announcements')");

    expect($html)->toContain('Network service notice')
        ->and($html)->toContain('MTN network update')
        ->and($html)->toContain('MTN is currently experiencing delays from the telco.')
        ->and(strpos($html, 'MTN network update'))->toBeLessThan(strpos($html, 'General promo announcement'));
});

it('lets admin enable a network issue alert from the announcements page', function () {
    $admin = networkIssueAdmin();
    $network = Network::create([
        'network_name' => 'GLO',
        'api_id' => 'glo-issue-test',
        'visibility' => '1',
    ]);

    actingAs($admin);

    get(route('admin.announcements.index'))
        ->assertOk()
        ->assertSee('Network issue alert')
        ->assertSee('GLO');

    post(route('admin.announcements.network_issue_alert.update'), [
        'network_id' => $network->id,
        'title' => 'Glo service update',
        'message' => 'Glo purchases may delay for a short while.',
        'is_active' => '1',
        'priority' => 1,
    ])->assertRedirect()
        ->assertSessionHas('success', 'Network issue alert updated successfully');

    $alert = NetworkIssueAlert::query()->where('network_id', $network->id)->firstOrFail();

    expect($alert->title)->toBe('Glo service update')
        ->and($alert->message)->toBe('Glo purchases may delay for a short while.')
        ->and($alert->is_active)->toBeTrue();
});

it('preconfigures network alert messages so admin can toggle without typing', function () {
    $admin = networkIssueAdmin();
    $network = Network::create([
        'network_name' => 'Airtel',
        'api_id' => 'airtel-issue-test',
        'visibility' => '1',
    ]);

    actingAs($admin);

    get(route('admin.announcements.index'))
        ->assertOk()
        ->assertSee('Airtel service update')
        ->assertSee('Airtel is currently experiencing a temporary service issue from the telco.');

    post(route('admin.announcements.network_issue_alert.update'), [
        'network_id' => $network->id,
        'is_active' => '1',
        'priority' => 1,
    ])->assertRedirect()
        ->assertSessionHas('success', 'Network issue alert updated successfully');

    $alert = NetworkIssueAlert::query()->where('network_id', $network->id)->firstOrFail();

    expect($alert->title)->toBe('Airtel service update')
        ->and($alert->message)->toBe('Airtel is currently experiencing a temporary service issue from the telco. Some transactions may delay or fail. Please hold on or try again shortly while the provider resolves it.')
        ->and($alert->is_active)->toBeTrue();
});

it('marks a restored notice when admin turns a network issue off', function () {
    $admin = networkIssueAdmin();
    $network = Network::create([
        'network_name' => 'MTN',
        'api_id' => 'mtn-restored-test',
        'visibility' => '1',
    ]);

    $alert = NetworkIssueAlert::create([
        'network_id' => $network->id,
        'title' => 'MTN service update',
        'message' => 'MTN has temporary delays.',
        'restored_title' => 'MTN service restored',
        'restored_message' => 'MTN is back now.',
        'is_active' => true,
        'priority' => 1,
    ]);

    actingAs($admin);

    post(route('admin.announcements.network_issue_alert.update'), [
        'network_id' => $network->id,
        'title' => $alert->title,
        'message' => $alert->message,
        'restored_title' => $alert->restored_title,
        'restored_message' => $alert->restored_message,
        'is_active' => '0',
        'priority' => 1,
    ])->assertRedirect()
        ->assertSessionHas('success', 'Network issue alert updated successfully');

    $alert->refresh();

    expect($alert->is_active)->toBeFalse()
        ->and($alert->last_restored_at)->not->toBeNull()
        ->and($alert->restoredPayload()['title'])->toBe('MTN service restored')
        ->and($alert->restoredPayload()['message'])->toBe('MTN is back now.')
        ->and($alert->restoredPayload()['once_key'])->toContain('network-restored:'.$alert->id);
});

it('lets customers mark restored network notices as seen once', function () {
    $role = Role::firstOrCreate(['role_name' => 'User']);
    $user = User::factory()->create(['role_id' => $role->id]);
    $network = Network::create([
        'network_name' => '9mobile',
        'api_id' => '9mobile-restored-test',
        'visibility' => '1',
    ]);
    $alert = NetworkIssueAlert::create([
        'network_id' => $network->id,
        'title' => '9mobile service update',
        'message' => '9mobile has temporary delays.',
        'is_active' => false,
        'last_restored_at' => now(),
        'priority' => 1,
    ]);
    $noticeKey = $alert->restoredPayload()['once_key'];

    actingAs($user);

    post(route('dashboard.network_restored_notices.seen'), [
        'notice_keys' => [$noticeKey],
    ])->assertOk()
        ->assertJson(['success' => true]);

    expect(NetworkIssueAlertRead::where('user_id', $user->id)->where('notice_key', $noticeKey)->exists())->toBeTrue();
});

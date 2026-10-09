<?php

use App\Models\Announcement;
use App\Models\Network;
use App\Models\NetworkIssueAlert;
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

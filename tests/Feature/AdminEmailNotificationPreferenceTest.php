<?php

use App\Models\AdminEmailNotificationPreference;
use App\Models\Role;
use App\Models\User;
use App\Services\AdminEmailNotificationRecipients;

function notificationAdmin(string $email): User
{
    $role = Role::firstOrCreate(['role_name' => 'Admin']);

    return User::factory()->create(['role_id' => $role->id, 'email' => $email]);
}

it('allows only programmer access to control admin email notifications', function () {
    $ordinaryAdmin = notificationAdmin('ordinary-admin@example.com');
    $programmer = notificationAdmin('adebsholey4real@gmail.com');

    $this->actingAs($ordinaryAdmin)
        ->post(route('admin.settings.admin_email_notifications'), ['preferences' => []])
        ->assertForbidden();

    $this->actingAs($programmer)
        ->post(route('admin.settings.admin_email_notifications'), [
            'preferences' => [
                $ordinaryAdmin->id => [
                    'failed_transactions' => '1',
                    'pending_transactions' => '0',
                    'automation_low_balance' => '1',
                ],
                $programmer->id => [
                    'failed_transactions' => '0',
                    'pending_transactions' => '1',
                    'automation_low_balance' => '0',
                ],
            ],
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    expect(AdminEmailNotificationPreference::where('user_id', $ordinaryAdmin->id)->first())
        ->failed_transactions->toBeTrue()
        ->pending_transactions->toBeFalse()
        ->automation_low_balance->toBeTrue();
});

it('resolves each admin email type independently', function () {
    $failedAdmin = notificationAdmin('failed-admin@example.com');
    $balanceAdmin = notificationAdmin('balance-admin@example.com');

    AdminEmailNotificationPreference::create([
        'user_id' => $failedAdmin->id,
        'failed_transactions' => true,
        'pending_transactions' => false,
        'automation_low_balance' => false,
    ]);
    AdminEmailNotificationPreference::create([
        'user_id' => $balanceAdmin->id,
        'failed_transactions' => false,
        'pending_transactions' => true,
        'automation_low_balance' => true,
    ]);

    $recipients = app(AdminEmailNotificationRecipients::class);

    expect($recipients->emails('failed_transactions')->all())->toContain('failed-admin@example.com')
        ->not->toContain('balance-admin@example.com')
        ->and($recipients->emails('pending_transactions')->all())->toContain('balance-admin@example.com')
        ->not->toContain('failed-admin@example.com')
        ->and($recipients->emails('automation_low_balance')->all())->toContain('balance-admin@example.com')
        ->not->toContain('failed-admin@example.com');
});

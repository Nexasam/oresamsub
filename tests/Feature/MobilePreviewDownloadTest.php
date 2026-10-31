<?php

use App\Models\User;
it('allows only configured verified testers to download the mobile preview', function () {
    config()->set('mobile.preview.android_apk_path', null);
    config()->set('mobile.preview.android_download_url', 'https://example.com/oresamsub-preview.apk');
    config()->set('mobile.preview.tester_emails', [
        'adebsholey4real@gmail.com',
        'oreofe@gmail.com',
        'oluwadarasimimakanjuola215@gmail.com',
    ]);

    $tester = User::factory()->create(['email' => 'adebsholey4real@gmail.com']);
    $secondTester = User::factory()->create(['email' => 'oreofe@gmail.com']);
    $thirdTester = User::factory()->create(['email' => 'oluwadarasimimakanjuola215@gmail.com']);
    $customer = User::factory()->create(['email' => 'customer@example.com']);

    $this->actingAs($tester)->get(route('mobile.preview.android.download'))->assertRedirect('https://example.com/oresamsub-preview.apk');
    $this->actingAs($secondTester)->get(route('mobile.preview.android.download'))->assertRedirect('https://example.com/oresamsub-preview.apk');
    $this->actingAs($thirdTester)->get(route('mobile.preview.android.download'))->assertRedirect('https://example.com/oresamsub-preview.apk');
    $this->actingAs($customer)->get(route('mobile.preview.android.download'))->assertForbidden();
});

it('does not expose the preview download to guests or unverified testers', function () {
    config()->set('mobile.preview.android_apk_path', null);
    config()->set('mobile.preview.android_download_url', 'https://example.com/oresamsub-preview.apk');
    config()->set('mobile.preview.tester_emails', ['oreofe@gmail.com']);

    $unverifiedTester = User::factory()->unverified()->create(['email' => 'oreofe@gmail.com']);

    $this->get(route('mobile.preview.android.download'))->assertRedirect(route('login'));
    $this->actingAs($unverifiedTester)
        ->get(route('mobile.preview.android.download'))
        ->assertRedirect(route('verification.notice'));
});

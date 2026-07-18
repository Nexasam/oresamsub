<?php

use App\Models\User;
use App\Services\Whatsapp\OreWhatsappUserResolverService;

it('normalizes Nigerian international phone numbers', function (string $input, string $expected) {
    expect(app(OreWhatsappUserResolverService::class)->normalize($input))->toBe($expected);
})->with([
    ['+234 801 234 5678', '08012345678'],
    ['2348012345678', '08012345678'],
    ['0801-234-5678', '08012345678'],
]);

it('resolves a user by their registered phone number', function () {
    $user = User::factory()->create(['phone_number' => '08012345678']);

    $resolved = app(OreWhatsappUserResolverService::class)->resolve('+2348012345678');

    expect($resolved?->is($user))->toBeTrue();
});

it('resolves a user by their linked WhatsApp number', function () {
    $user = User::factory()->create([
        'phone_number' => '08099999999',
        'whatsapp_number' => '08012345678',
    ]);

    $resolved = app(OreWhatsappUserResolverService::class)->resolve('2348012345678');

    expect($resolved?->is($user))->toBeTrue();
});

it('returns null when a WhatsApp number is not linked', function () {
    expect(app(OreWhatsappUserResolverService::class)->resolve('2348012345678'))->toBeNull();
});

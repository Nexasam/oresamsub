<?php

use App\Mail\UserRegistrationNotification;

it('renders registration notifications with incomplete legacy payloads', function () {
    $html = (new UserRegistrationNotification([
        'email' => 'legacy@example.com',
    ]))->render();

    expect($html)
        ->toContain('legacy@example.com')
        ->toContain('Not provided')
        ->toContain('Not available')
        ->toContain(url('/login'));
});

it('renders registration notifications with the current payload', function () {
    $html = (new UserRegistrationNotification([
        'first_name' => 'Samuel',
        'last_name' => 'Ade',
        'email' => 'samuel@example.com',
        'phone_number' => '08012345678',
        'created_at' => '2026-11-02 15:02:56',
        'url' => 'https://oresamsub.com/login',
    ]))->render();

    expect($html)
        ->toContain('Samuel Ade')
        ->toContain('samuel@example.com')
        ->toContain('08012345678')
        ->toContain('November 02, 2026')
        ->toContain('https://oresamsub.com/login');
});

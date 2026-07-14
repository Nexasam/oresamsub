<?php

it('returns a successful response', function () {
    $response = $this->get('/login');

    $response->assertStatus(200)
        ->assertSee('data-page="app"', false)
        ->assertSee('<div id="app"></div>', false);
});

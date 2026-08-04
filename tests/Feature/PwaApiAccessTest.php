<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PwaApiAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_open_api_access_page(): void
    {
        $this->get('/user/api-access')->assertRedirect(route('login'));
    }

    public function test_opening_api_access_generates_a_missing_key_and_links_documentation(): void
    {
        $user = User::factory()->create(['api_token' => null]);

        $this->actingAs($user)->get('/user/api-access')
            ->assertOk()
            ->assertSee('API Access')
            ->assertSee(route('developers.index'))
            ->assertSee('/api/v2');

        $this->assertNotNull($user->fresh()->api_token);
        $this->assertNotNull($user->fresh()->api_token_rotated_at);
    }

    public function test_rotation_requires_the_correct_transaction_pin(): void
    {
        $user = User::factory()->create(['api_token' => 'original-token', 'pin' => '4826']);

        $this->actingAs($user)->post('/user/api-access/rotate', ['pin' => '1111'])
            ->assertSessionHasErrors('pin');

        $this->assertSame('original-token', $user->fresh()->api_token);
    }

    public function test_rotation_immediately_invalidates_the_old_key(): void
    {
        $user = User::factory()->create(['api_token' => 'original-token', 'pin' => '4826']);

        $this->actingAs($user)->post('/user/api-access/rotate', ['pin' => '4826'])
            ->assertRedirect(route('user.api-access.show'))
            ->assertSessionHas('success');

        $newToken = $user->fresh()->api_token;
        $this->assertNotSame('original-token', $newToken);
        $this->getJson('/api/v2/wallet', ['Authorization' => 'Bearer original-token'])->assertUnauthorized();
        $this->getJson('/api/v2/wallet', ['Authorization' => 'Bearer '.$newToken])->assertOk();
    }

    public function test_landing_page_links_to_the_developer_portal(): void
    {
        $this->get('/')->assertOk()->assertSee(route('developers.index'));
    }
}

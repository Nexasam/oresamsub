<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarketingGuidePdfTest extends TestCase
{
    use RefreshDatabase;

    public function test_marketer_can_download_customer_conversion_guide(): void
    {
        $marketer = User::factory()->create(['is_marketer' => 1]);

        $response = $this->actingAs($marketer)
            ->get(route('marketing.customer-conversion-guide'));

        $response->assertOk()
            ->assertHeader('content-type', 'application/pdf')
            ->assertDownload('oresamsub-customer-conversion-guide.pdf');
    }

    public function test_admin_can_download_customer_conversion_guide(): void
    {
        $adminRole = Role::firstOrCreate(['role_name' => 'Admin']);
        $admin = User::factory()->create([
            'role_id' => $adminRole->id,
            'is_marketer' => 0,
        ]);

        $response = $this->actingAs($admin)
            ->get(route('marketing.customer-conversion-guide'));

        $response->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_regular_customer_cannot_download_customer_conversion_guide(): void
    {
        $customer = User::factory()->create(['is_marketer' => 0]);

        $this->actingAs($customer)
            ->get(route('marketing.customer-conversion-guide'))
            ->assertRedirect(route('access_denied'));
    }

    public function test_guide_explains_the_dormant_customer_reward_and_manual_transfer(): void
    {
        $guide = view('marketing.customer-conversion-guide')->render();

        $this->assertStringContainsString('Welcome back with ₦200', $guide);
        $this->assertStringContainsString('eligible dormant account', $guide);
        $this->assertStringContainsString('Transfer to Main Wallet', $guide);
    }
}

<?php

namespace Tests\Feature;

use App\Models\UssdGatewayDevice;
use App\Models\UssdRechargeAttempt;
use App\Models\UssdRechargeBatch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class UssdGatewayTest extends TestCase
{
    use RefreshDatabase;

    public function test_unpaired_device_must_bind_before_claiming_and_cannot_silently_change_sim(): void
    {
        [$device, $token] = $this->device();
        $installationId = (string) Str::uuid();

        $this->withToken($token)
            ->postJson('/api/ussd-gateway/v1/jobs/claim')
            ->assertStatus(409);

        $this->withToken($token)
            ->postJson('/api/ussd-gateway/v1/bind', [
                'installation_id' => $installationId,
                'sim_slot' => 0,
                'subscription_id' => 41,
                'device_model' => 'Tecno Spark 8C',
            ])
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->withHeaders(['X-Gateway-Installation' => $installationId])
            ->withToken($token)
            ->postJson('/api/ussd-gateway/v1/bind', [
                'installation_id' => $installationId,
                'sim_slot' => 0,
                'subscription_id' => 99,
            ])
            ->assertStatus(409)
            ->assertJsonPath('success', false);

        $this->assertDatabaseHas('ussd_gateway_devices', [
            'id' => $device->id,
            'installation_id' => $installationId,
            'subscription_id' => 41,
        ]);
    }

    public function test_wrong_phone_installation_cannot_claim_a_job(): void
    {
        $installationId = (string) Str::uuid();
        [, $token] = $this->device($installationId, 41);

        $this->withHeaders(['X-Gateway-Installation' => (string) Str::uuid()])
            ->withToken($token)
            ->postJson('/api/ussd-gateway/v1/jobs/claim')
            ->assertStatus(403);
    }

    public function test_changed_sim_is_rejected_before_any_job_is_claimed(): void
    {
        $installationId = (string) Str::uuid();
        [$device, $token] = $this->device($installationId, 41);
        [$attempt] = $this->batchWithAttempts($device, 1);

        $this->withHeaders([
            'X-Gateway-Installation' => $installationId,
            'X-Gateway-Sim-Slot' => '0',
            'X-Gateway-Subscription' => '99',
        ])->withToken($token)
            ->postJson('/api/ussd-gateway/v1/jobs/claim')
            ->assertStatus(409)
            ->assertJsonPath('success', false);

        $this->assertDatabaseHas('ussd_recharge_attempts', [
            'id' => $attempt->id,
            'status' => 'queued',
        ]);
    }

    public function test_only_one_job_is_released_until_the_picked_job_uploads_a_result(): void
    {
        $installationId = (string) Str::uuid();
        [$device, $token] = $this->device($installationId, 41);
        [$first, $second] = $this->batchWithAttempts($device);
        $headers = $this->jobHeaders($installationId);

        $claim = $this->withHeaders($headers)->withToken($token)
            ->postJson('/api/ussd-gateway/v1/jobs/claim')
            ->assertOk()
            ->assertJsonPath('data.attempt_id', $first->id)
            ->assertJsonPath('data.sim_slot', 0)
            ->assertJsonPath('data.subscription_id', 41)
            ->json('data');

        $this->withHeaders($headers)->withToken($token)
            ->postJson('/api/ussd-gateway/v1/jobs/claim')
            ->assertStatus(422);

        $clientResultId = (string) Str::uuid();
        $payload = [
            'dispatch_token' => $claim['dispatch_token'],
            'client_result_id' => $clientResultId,
            'outcome' => 'response_captured',
            'response_text' => 'Request received and is being processed.',
        ];

        $this->withHeaders($headers)->withToken($token)
            ->postJson("/api/ussd-gateway/v1/jobs/{$first->id}/result", $payload)
            ->assertOk()
            ->assertJsonPath('data.status', 'pending_review');

        // A lost HTTP response is safe: replaying the exact result is idempotent.
        $this->withHeaders($headers)->withToken($token)
            ->postJson("/api/ussd-gateway/v1/jobs/{$first->id}/result", $payload)
            ->assertOk()
            ->assertJsonPath('message', 'Result was already recorded.');

        $this->withHeaders($headers)->withToken($token)
            ->postJson('/api/ussd-gateway/v1/jobs/claim')
            ->assertOk()
            ->assertJsonPath('data.attempt_id', $second->id);
    }

    public function test_conflicting_second_result_is_rejected(): void
    {
        $installationId = (string) Str::uuid();
        [$device, $token] = $this->device($installationId, 41);
        [$attempt] = $this->batchWithAttempts($device, 1);
        $headers = $this->jobHeaders($installationId);
        $dispatchToken = $this->withHeaders($headers)->withToken($token)
            ->postJson('/api/ussd-gateway/v1/jobs/claim')
            ->json('data.dispatch_token');

        $this->withHeaders($headers)->withToken($token)
            ->postJson("/api/ussd-gateway/v1/jobs/{$attempt->id}/result", [
                'dispatch_token' => $dispatchToken,
                'client_result_id' => (string) Str::uuid(),
                'outcome' => 'response_captured',
                'response_text' => 'First result',
            ])->assertOk();

        $this->withHeaders($headers)->withToken($token)
            ->postJson("/api/ussd-gateway/v1/jobs/{$attempt->id}/result", [
                'dispatch_token' => $dispatchToken,
                'client_result_id' => (string) Str::uuid(),
                'outcome' => 'device_error',
                'response_text' => 'Conflicting result',
            ])->assertStatus(409);
    }

    public function test_phone_can_clear_a_job_that_an_admin_already_resolved(): void
    {
        $installationId = (string) Str::uuid();
        [$device, $token] = $this->device($installationId, 41);
        [$attempt] = $this->batchWithAttempts($device, 1);
        $headers = $this->jobHeaders($installationId);
        $dispatchToken = $this->withHeaders($headers)->withToken($token)
            ->postJson('/api/ussd-gateway/v1/jobs/claim')
            ->json('data.dispatch_token');

        $attempt->refresh()->update(['status' => 'confirmed_failed']);

        $this->withHeaders($headers)->withToken($token)
            ->postJson("/api/ussd-gateway/v1/jobs/{$attempt->id}/result", [
                'dispatch_token' => $dispatchToken,
                'client_result_id' => (string) Str::uuid(),
                'outcome' => 'session_ended',
                'response_text' => 'Stored local result arrived after manual resolution.',
            ])
            ->assertOk()
            ->assertJsonPath('data.status', 'confirmed_failed');
    }

    private function device(?string $installationId = null, ?int $subscriptionId = null): array
    {
        $token = 'urg_'.Str::random(64);
        $device = UssdGatewayDevice::create([
            'name' => 'Tecno Gateway',
            'main_sim_number' => '09163128718',
            'sim_slot' => 0,
            'installation_id' => $installationId,
            'subscription_id' => $subscriptionId,
            'device_model' => $installationId ? 'Tecno Spark 8C' : null,
            'bound_at' => $installationId ? now() : null,
            'token_digest' => hash('sha256', $token),
            'token_hint' => substr($token, 0, 12),
            'is_active' => true,
        ]);

        return [$device, $token];
    }

    private function jobHeaders(string $installationId): array
    {
        return [
            'X-Gateway-Installation' => $installationId,
            'X-Gateway-Sim-Slot' => '0',
            'X-Gateway-Subscription' => '41',
        ];
    }

    private function batchWithAttempts(UssdGatewayDevice $device, int $count = 2): array
    {
        $batch = UssdRechargeBatch::create([
            'device_id' => $device->id,
            'name' => 'Test batch',
            'plan_label' => 'MTN test plan',
            'status' => 'queued',
            'recipient_count' => $count,
        ]);

        $attempts = [];
        for ($sequence = 1; $sequence <= $count; $sequence++) {
            $attempts[] = UssdRechargeAttempt::create([
                'batch_id' => $batch->id,
                'device_id' => $device->id,
                'sequence' => $sequence,
                'recipient_number' => '0801234567'.$sequence,
                'status' => 'queued',
            ]);
        }

        return $attempts;
    }
}

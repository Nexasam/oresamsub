<?php

namespace App\Services\UssdGateway;

use App\Models\UssdGatewayDevice;
use App\Models\UssdRechargeBatch;
use App\Models\UssdRechargeAttempt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class UssdGatewayService
{
    public function classifyResponse(string $outcome, ?string $responseText): ?string
    {
        $text = strtolower(trim((string) $responseText));
        if ($outcome !== 'response_captured' || $text === '') {
            return null;
        }

        if (preg_match('/\b(successfully|successful|completed|recharge was successful|you have received)\b/', $text)) {
            return 'confirmed_success';
        }

        if (preg_match('/\b(failed|failure|insufficient balance|invalid .*number|unable to complete|not enough balance|declined|unsuccessful)\b/', $text)) {
            return 'confirmed_failed';
        }

        return null;
    }

    public function issueDeviceToken(UssdGatewayDevice $device): string
    {
        $plainText = 'urg_'.Str::random(64);

        $device->forceFill([
            'token_digest' => hash('sha256', $plainText),
            'token_hint' => substr($plainText, 0, 12),
        ])->save();

        return $plainText;
    }

    public function normalizeNigerianNumber(string $number): string
    {
        $digits = preg_replace('/\D+/', '', $number) ?? '';

        if (str_starts_with($digits, '234') && strlen($digits) === 13) {
            $digits = '0'.substr($digits, 3);
        }

        if (strlen($digits) !== 11 || ! str_starts_with($digits, '0')) {
            throw ValidationException::withMessages([
                'recipient_numbers' => "Invalid Nigerian phone number: {$number}",
            ]);
        }

        return $digits;
    }

    public function refreshBatchStatus(UssdRechargeBatch $batch): void
    {
        $counts = $batch->attempts()
            ->selectRaw('status, COUNT(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        if (($counts['picked'] ?? 0) > 0 || ($counts['queued'] ?? 0) > 0) {
            $status = $batch->status === 'paused' ? 'paused' : 'in_progress';
        } elseif (($counts['pending_review'] ?? 0) > 0) {
            $status = 'awaiting_review';
        } else {
            $status = 'completed';
        }

        if ($batch->status !== $status) {
            $batch->update(['status' => $status]);
        }
    }

    public function claimNext(UssdGatewayDevice $device): ?array
    {
        return DB::transaction(function () use ($device) {
            UssdGatewayDevice::query()->whereKey($device->id)->lockForUpdate()->firstOrFail();

            $unresolved = UssdRechargeAttempt::query()
                ->where('device_id', $device->id)
                ->where('status', 'picked')
                ->lockForUpdate()
                ->first();

            if ($unresolved) {
                throw ValidationException::withMessages([
                    'device' => 'This device already has a picked attempt. Upload its stored result before claiming another recipient.',
                ]);
            }

            $attempt = UssdRechargeAttempt::query()
                ->where('device_id', $device->id)
                ->where('status', 'queued')
                ->whereHas('batch', fn ($query) => $query->whereIn('status', ['queued', 'in_progress']))
                ->orderBy('created_at')
                ->orderBy('sequence')
                ->lockForUpdate()
                ->first();

            if (! $attempt) {
                return null;
            }

            $dispatchToken = 'urd_'.Str::random(64);
            $attempt->update([
                'status' => 'picked',
                'dispatch_token_digest' => hash('sha256', $dispatchToken),
                'picked_at' => now(),
            ]);
            $attempt->batch()->update(['status' => 'in_progress']);

            return [
                'attempt_id' => $attempt->id,
                'batch_id' => $attempt->batch_id,
                'recipient_number' => $attempt->recipient_number,
                'sequence' => $attempt->sequence,
                'dispatch_token' => $dispatchToken,
                'picked_at' => $attempt->picked_at?->toIso8601String(),
                'sim_slot' => $device->sim_slot,
                'subscription_id' => $device->subscription_id,
            ];
        }, 3);
    }

    public function assertDispatchToken(UssdRechargeAttempt $attempt, string $token): void
    {
        if (! $attempt->dispatch_token_digest || ! hash_equals($attempt->dispatch_token_digest, hash('sha256', $token))) {
            abort(403, 'Invalid attempt dispatch token.');
        }
    }
}

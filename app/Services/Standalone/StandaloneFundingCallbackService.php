<?php

namespace App\Services\Standalone;

use App\Models\StandaloneFundingEvent;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class StandaloneFundingCallbackService
{
    public function __construct(private StandaloneCallbackSigner $signer, private PublicCallbackUrlValidator $validator) {}

    public function deliver(StandaloneFundingEvent $event): StandaloneFundingEvent
    {
        $now = now();
        $status = null;
        $error = null;
        $delivered = false;
        try {
            if (! $event->callback_url || ! $this->validator->isSafe($event->callback_url)) {
                throw new ConnectionException('Callback URL is missing or unsafe.');
            }
            $body = json_encode($event->callback_payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
            $timestamp = $now->timestamp;
            $response = Http::withBody($body, 'application/json')->withHeaders(['X-Oresamsub-Event-ID' => $event->event_id, 'X-Oresamsub-Timestamp' => (string) $timestamp, 'X-Oresamsub-Signature' => $this->signer->sign($body, $timestamp, $event->standaloneWebsite->webhook_signing_secret)])->connectTimeout(5)->timeout(10)->withoutRedirecting()->post($event->callback_url);
            $status = $response->status();
            $delivered = $response->successful();
            if (! $delivered) {
                $error = 'Callback returned HTTP '.$status.'.';
            }
        } catch (\Throwable $e) {
            $error = mb_substr($e->getMessage(), 0, 500);
        }
        $event->update(['delivery_status' => $delivered ? 'delivered' : 'failed', 'attempt_count' => $event->attempt_count + 1, 'last_http_status' => $status, 'last_error' => $error, 'first_attempted_at' => $event->first_attempted_at ?: $now, 'last_attempted_at' => $now, 'delivered_at' => $delivered ? $now : $event->delivered_at]);

        return $event->fresh();
    }
}

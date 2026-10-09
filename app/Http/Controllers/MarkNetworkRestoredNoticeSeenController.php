<?php

namespace App\Http\Controllers;

use App\Models\NetworkIssueAlert;
use App\Models\NetworkIssueAlertRead;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MarkNetworkRestoredNoticeSeenController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'notice_keys' => ['required', 'array'],
            'notice_keys.*' => ['required', 'string', 'max:255'],
        ]);

        $user = $request->user();
        $alerts = NetworkIssueAlert::query()
            ->whereNotNull('last_restored_at')
            ->where('is_active', false)
            ->get()
            ->keyBy(fn (NetworkIssueAlert $alert): string => $alert->restoredPayload()['once_key']);

        foreach ($validated['notice_keys'] as $noticeKey) {
            $alert = $alerts->get($noticeKey);

            if (! $alert) {
                continue;
            }

            NetworkIssueAlertRead::firstOrCreate([
                'user_id' => $user->id,
                'network_issue_alert_id' => $alert->id,
                'notice_key' => $noticeKey,
            ]);
        }

        return response()->json(['success' => true]);
    }
}

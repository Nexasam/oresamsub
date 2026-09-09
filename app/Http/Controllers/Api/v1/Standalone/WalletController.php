<?php

namespace App\Http\Controllers\Api\v1\Standalone;

use App\Http\Controllers\Controller;
use App\Models\StandaloneWalletEntry;
use App\Models\StandaloneWebsite;
use App\Services\Standalone\DeductStandaloneWallet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;

class WalletController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $site = $this->site($request)->fresh();

        return response()->json(['success' => true, 'message' => 'Master wallet fetched successfully.', 'data' => [
            'currency' => 'NGN', 'available_balance' => $site->master_wallet,
        ]]);
    }

    public function deduct(Request $request, DeductStandaloneWallet $deduct): JsonResponse
    {
        $data = $request->validate([
            'amount' => ['required', 'regex:/^(?:0|[1-9]\d*)(?:\.\d{1,2})?$/', 'not_in:0,0.0,0.00'],
            'reference' => ['required', 'string', 'max:100'],
            'purpose' => ['required', 'string', 'max:255'],
        ]);
        $data['reference'] = trim($data['reference']);
        $data['purpose'] = trim(strip_tags($data['purpose']));

        try {
            $result = $deduct->handle($this->site($request), $data['amount'], $data['reference'], $data['purpose']);
        } catch (RuntimeException $exception) {
            return match ($exception->getMessage()) {
                'insufficient_balance' => response()->json(['success' => false, 'message' => 'Insufficient master wallet balance.'], 422),
                'reference_conflict' => response()->json(['success' => false, 'message' => 'This reference has already been used for a different deduction.'], 409),
                default => throw $exception,
            };
        }

        return response()->json(['success' => true, 'message' => $result['replay'] ? 'This deduction was already processed.' : 'Master wallet deducted successfully.',
            'data' => $this->entryData($result['entry']), 'meta' => ['idempotent_replay' => $result['replay']]]);
    }

    public function index(Request $request): JsonResponse
    {
        $entries = $this->site($request)->walletEntries()->latest()->paginate(min(max($request->integer('per_page', 30), 1), 100));

        return response()->json(['success' => true, 'message' => 'Wallet transactions fetched successfully.',
            'data' => collect($entries->items())->map(fn (StandaloneWalletEntry $entry) => $this->entryData($entry)),
            'meta' => ['current_page' => $entries->currentPage(), 'last_page' => $entries->lastPage(), 'total' => $entries->total()]]);
    }

    public function transaction(Request $request, string $reference): JsonResponse
    {
        $entry = $this->site($request)->walletEntries()->where('client_reference', $reference)->first();
        if (! $entry) {
            return response()->json(['success' => false, 'message' => 'Wallet transaction not found.'], 404);
        }

        return response()->json(['success' => true, 'message' => 'Wallet transaction fetched successfully.', 'data' => $this->entryData($entry)]);
    }

    private function site(Request $request): StandaloneWebsite
    {
        return $request->attributes->get('standaloneWebsite');
    }

    private function entryData(StandaloneWalletEntry $entry): array
    {
        return ['transaction_id' => $entry->transaction_id, 'reference' => $entry->client_reference, 'type' => $entry->type,
            'category' => $entry->category, 'amount' => $entry->amount, 'purpose' => $entry->purpose,
            'balance_before' => $entry->balance_before, 'balance_after' => $entry->balance_after,
            'currency' => 'NGN', 'status' => 'successful', 'created_at' => $entry->created_at?->toIso8601String()];
    }
}

<?php

namespace App\Http\Controllers\Api\Mobile\V1;

use App\Http\Controllers\Api\Mobile\V1\Concerns\RespondsToMobileApi;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Mobile\V1\TransactionResource;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MobileTransactionController extends Controller
{
    use RespondsToMobileApi;

    public function index(Request $request): JsonResponse
    {
        $filters = $request->validate([
            'status' => ['nullable', Rule::in(['pending', 'processing', 'successful', 'failed', 'refunded'])],
            'category' => ['nullable', 'string', 'max:50'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'per_page' => ['nullable', 'integer', 'min:5', 'max:50'],
        ]);
        $status = ['pending' => '0', 'processing' => '3', 'successful' => '1', 'failed' => '-1', 'refunded' => '2'];

        $transactions = Transaction::query()
            ->where('user_id', $request->user()->id)
            ->when(isset($filters['status']), fn ($query) => $query->where('status', $status[$filters['status']]))
            ->when(isset($filters['category']), fn ($query) => $query->where('transaction_category', $filters['category']))
            ->when(isset($filters['from']), fn ($query) => $query->whereDate('created_at', '>=', $filters['from']))
            ->when(isset($filters['to']), fn ($query) => $query->whereDate('created_at', '<=', $filters['to']))
            ->latest()->paginate($filters['per_page'] ?? 20);

        return $this->successResponse('Transactions fetched successfully.', TransactionResource::collection($transactions->items())->resolve($request), meta: [
            'current_page' => $transactions->currentPage(),
            'last_page' => $transactions->lastPage(),
            'per_page' => $transactions->perPage(),
            'total' => $transactions->total(),
        ]);
    }

    public function show(Request $request, string $transaction): JsonResponse
    {
        $record = $this->ownedTransaction($request, $transaction);

        return $this->successResponse('Transaction fetched successfully.', [
            'transaction' => (new TransactionResource($record))->resolve($request),
        ]);
    }

    public function receipt(Request $request, string $transaction): JsonResponse
    {
        $record = $this->ownedTransaction($request, $transaction);

        return $this->successResponse('Receipt fetched successfully.', [
            'receipt' => [
                'reference' => $record->txn_reference ?: $record->id,
                'transaction' => (new TransactionResource($record))->resolve($request),
                'wallet' => $record->wallet_category === 'data_wallet' ? 'Data wallet' : 'Main wallet',
                'balance_before' => round((float) $record->balance_before, 2),
                'balance_after' => round((float) $record->balance_after, 2),
            ],
        ]);
    }

    private function ownedTransaction(Request $request, string $id): Transaction
    {
        return Transaction::where('user_id', $request->user()->id)->whereKey($id)->firstOrFail();
    }
}

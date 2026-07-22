<?php

namespace App\Http\Controllers\Api\Mobile\V1;

use App\Http\Controllers\Api\Mobile\V1\Concerns\RespondsToMobileApi;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Mobile\V1\TransactionResource;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MobileDashboardController extends Controller
{
    use RespondsToMobileApi;

    public function __invoke(Request $request): JsonResponse
    {
        $user = $request->user();
        $recentTransactions = Transaction::query()
            ->where('user_id', $user->id)
            ->latest()
            ->limit(5)
            ->get();

        return $this->successResponse('Dashboard fetched successfully.', [
            'wallet' => [
                'currency' => 'NGN',
                'balance' => round((float) $user->main_wallet, 2),
            ],
            'summary' => [
                'total_transactions' => Transaction::where('user_id', $user->id)->count(),
                'successful_transactions' => Transaction::where('user_id', $user->id)->where('status', '1')->count(),
                'pending_transactions' => Transaction::where('user_id', $user->id)->whereIn('status', ['0', '3'])->count(),
            ],
            'recent_transactions' => TransactionResource::collection($recentTransactions)->resolve($request),
        ]);
    }
}

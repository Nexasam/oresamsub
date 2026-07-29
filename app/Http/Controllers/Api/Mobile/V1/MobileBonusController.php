<?php

namespace App\Http\Controllers\Api\Mobile\V1;

use App\Http\Controllers\Api\Mobile\V1\Concerns\RespondsToMobileApi;
use App\Http\Controllers\Controller;
use App\Services\BonusService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MobileBonusController extends Controller
{
    use RespondsToMobileApi;

    public function convert(Request $request, BonusService $bonuses): JsonResponse
    {
        $result = $bonuses->convertToMainWallet($request->user(), $request);

        if ($result['converted_amount'] <= 0) {
            return $this->errorResponse('There is no available bonus balance to move.', null, 422);
        }

        return $this->successResponse('Bonus moved to your main wallet successfully.', $result);
    }
}

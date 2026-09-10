<?php

namespace App\Http\Controllers\Api\v1\Affiliate;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Affiliate\PurchaseMsorgDataRequest;
use App\Services\Api\Affiliate\BuyDataServiceMsorg;
use Illuminate\Http\JsonResponse;

class MsorgDataPurchaseController extends Controller
{
    public function __invoke(PurchaseMsorgDataRequest $request, BuyDataServiceMsorg $service): JsonResponse
    {
        $result = $service->purchase($request->attributes->get('api_user'), $request->validated());

        return response()->json($result['body'], $result['status'], [], JSON_PRETTY_PRINT);
    }
}

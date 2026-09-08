<?php

namespace App\Http\Controllers\Api\V1\Standalone;

use App\Exceptions\StandaloneAccountProvisioningException;
use App\Http\Controllers\Controller;
use App\Services\Standalone\SecurewaveStandaloneAccountService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VirtualAccountController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $account = $request->attributes->get('standaloneWebsite')->virtualAccount;

        return $account ? response()->json(['data' => $this->data($account)]) : response()->json(['message' => 'Virtual account not found.'], 404);
    }

    public function store(Request $request, SecurewaveStandaloneAccountService $service): JsonResponse
    {
        try {
            [$account,$created] = $service->provision($request->attributes->get('standaloneWebsite'));

            return response()->json(['data' => $this->data($account)], $created ? 201 : 200);
        } catch (StandaloneAccountProvisioningException $e) {
            return response()->json(['message' => $e->getMessage()], 503);
        }
    }

    private function data($a): array
    {
        return ['bank_code' => $a->bank_code, 'bank_name' => $a->bank_name, 'account_name' => $a->account_name, 'account_number' => $a->account_number, 'account_reference' => $a->account_reference];
    }
}

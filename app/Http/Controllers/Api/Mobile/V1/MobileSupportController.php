<?php

namespace App\Http\Controllers\Api\Mobile\V1;

use App\Http\Controllers\Api\Mobile\V1\Concerns\RespondsToMobileApi;
use App\Http\Controllers\Controller;
use App\Http\Services\GeneralService;
use Illuminate\Http\JsonResponse;

class MobileSupportController extends Controller
{
    use RespondsToMobileApi;

    public function __invoke(GeneralService $generalService): JsonResponse
    {
        $support = collect($generalService->support_information()['data'] ?? [])
            ->mapWithKeys(function (array $item): array {
                $key = str_contains($item['field_name'], 'email') ? 'email'
                    : (str_contains($item['field_name'], 'whatsapp') ? 'whatsapp' : 'phone');

                return [$key => $item['field_details']];
            });

        return $this->successResponse('Support information fetched successfully.', [
            'email' => $support->get('email'),
            'phone' => $support->get('phone'),
            'whatsapp' => $support->get('whatsapp'),
        ]);
    }
}

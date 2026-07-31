<?php

namespace App\Services;

use App\Models\AffiliateFundingAttempt;

class AffiliateFundingTransferService
{
    /**
     * Future transfer-provider boundary.
     *
     * No external request or wallet mutation is performed until a provider
     * endpoint and its authentication/signature contract are configured.
     */
    public function initiate(AffiliateFundingAttempt $attempt): array
    {
        return [
            'status' => 'awaiting_transfer_integration',
            'provider_reference' => null,
            'message' => 'Automatic transfer provider is not configured.',
        ];
    }
}

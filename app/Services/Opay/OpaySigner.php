<?php

namespace App\Services\Opay;

final class OpaySigner
{
    public function signV3(
        string $canonicalJson,
        string $app,
        string $platform,
        string $versionName,
        string $deviceId,
        int|string $timestamp,
        string $token,
    ): string {
        return md5(
            'raw_data='.$canonicalJson
            .'&app='.$app
            .'&platform='.$platform
            .'&version_name='.$versionName
            .'&device_id='.$deviceId
            .'&timestamp='.$timestamp
            .'&token='.$token
        );
    }
}

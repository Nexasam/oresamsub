<?php

namespace App\Http\Services;

class VirtualAccountService
{
    public function generate_accounts($data)
    {
        $dataaa['user'] = $data['user'];
        if (config('app.name') == 'OresamSub') {
            return (new XixaPayService)->generate_accounts($dataaa);
        }

        return [
            'status' => 1,
            'message' => 'Virtual Accounts Generated Attempt was successful',
        ];

    }
}

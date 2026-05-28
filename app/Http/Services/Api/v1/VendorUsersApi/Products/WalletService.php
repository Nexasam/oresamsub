<?php
namespace App\Http\Services\Api\v1\VendorUsersApi\Products;


class WalletService
{
    public function debit($user, $amount, $meta = [])
    {
        $before = $user->main_wallet;

        $user->decrement('main_wallet', $amount);

        return [
            'before' => $before,
            'after' => $before - $amount
        ];
    }

    public function credit($user, $amount, $meta = [])
    {
        $user->increment('main_wallet', $amount);
    }
}
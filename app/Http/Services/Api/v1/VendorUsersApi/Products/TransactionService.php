<?php
namespace App\Http\Services\Api\v1\VendorUsersApi\Products;


class TransactionService
{
    public function create(array $data)
    {
        return \App\Models\Transaction::create([
            'user_id' => $data['user']->id,
            'product_plan_id' => $data['plan']->id,
            'amount' => $data['amount'],
            'status' => $data['status'],
            'txn_reference' => uniqid('txn_'),
            'meta' => json_encode($data['meta']),
            'balance_before' => $data['wallet_before'],
            'balance_after' => $data['wallet_after'],
        ]);
    }
}
<?php

namespace App\Http\Services;

use App\Models\FundingOption;
use App\Models\FundingOptionBankCodes;
use App\Models\User;
use App\Models\UserVirtualAccount;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class XixaPayService
{
    public function generate_accounts($data)
    {
        $user = $data['user'];
        $user_id = $user->id;
        $first_name = $user->first_name;
        $last_name = $user->last_name;
        $email = $user->email;
        $phone_number = $user->phone_number;
        $phone_number = preg_replace('/[^0-9]/', '', $phone_number);
        $bvn = $user->bvn;
        $requestedBankCode = $data['bank_code'] ?? null;

        // FOR NOW, RELAX ON THIS
        // if($bvn == NULL || $user->verification_status != 1){
        //     return [
        //         'status' => -1,
        //         'message' => 'BVN is not yet verified'
        //     ];
        // }

        $funding_option = FundingOption::where('slug', 'xixapay')->where('activation_status', '1')->first();
        if (! $funding_option) {
            // logger('na here oh');
            return [
                'status' => -1,
                'message' => 'Funding Option not found',
            ];

        }
        $api_secret_key = $funding_option->api_secret_key;
        $api_biz_id = $funding_option->contract_code;
        $api_public_key = $funding_option->api_public_key;
        $biz_bvn = $funding_option->biz_bvn ?? $bvn;

        $bank_codes = FundingOptionBankCodes::where('funding_option_id', $funding_option->id)
            ->where('visibility_status', '1')
            ->when($requestedBankCode, fn ($query) => $query->where('bank_code', $requestedBankCode))
            ->get();
        if (count($bank_codes) <= 0) {
            // logger('xixa1');
            // exit;
            return [
                'status' => -1,
                'message' => 'Sorry you cannot generate virtual accounts at the moment',
            ];
        }

        $availableAccountCount = 0;
        $failedAccountCount = 0;

        if (count($bank_codes) > 0) {
            foreach ($bank_codes as $bank_code) {
                // first check for the user if he has that va
                $check_va = UserVirtualAccount::where('user_id', $user_id)
                    ->where('funding_option_id', $funding_option->id)
                    ->where('bank_code', $bank_code->bank_code)
                    ->first();
                if ($check_va) {
                    $availableAccountCount++;
                } else {
                    if (blank($api_secret_key) || blank($api_public_key) || blank($api_biz_id) || blank($biz_bvn)) {
                        $failedAccountCount++;
                        logger()->warning('Xixapay virtual account configuration is incomplete.', [
                            'funding_option_id' => $funding_option->id,
                        ]);

                        continue;
                    }

                    // generate
                    $bank_codee = $bank_code->bank_code;
                    $arrr = [
                        'email' => $email,
                        'name' => $first_name.' '.$last_name,
                        'phoneNumber' => $phone_number,
                        'bankCode' => ["$bank_codee"],
                        'businessId' => $api_biz_id,
                        'accountType' => 'static',
                        'id_type' => 'bvn',
                        'id_number' => $biz_bvn,
                    ];
                    try {
                        $response = Http::acceptJson()
                            ->withToken($api_secret_key)
                            ->withHeaders(['api-key' => $api_public_key])
                            ->timeout(30)
                            ->post('https://api.xixapay.com/api/v1/createVirtualAccount', $arrr);
                        $response_dec = $response->json();
                    } catch (ConnectionException $exception) {
                        report($exception);
                        $response_dec = null;
                    }

                    if (is_array($response_dec) && ($response_dec['status'] ?? null) === 'success' && is_array($response_dec['bankAccounts'] ?? null)) {
                        // success //bankAccounts
                        $bankAccounts = $response_dec['bankAccounts'];
                        foreach ($bankAccounts as $bankAccount) {
                            if (blank($bankAccount['bankCode'] ?? null) || blank($bankAccount['accountNumber'] ?? null)) {
                                $failedAccountCount++;

                                continue;
                            }

                            UserVirtualAccount::firstOrCreate([
                                'user_id' => $user_id,
                                'funding_option_id' => $funding_option->id,
                                'bank_code' => $bankAccount['bankCode'],
                            ], [
                                'funding_slug' => $funding_option->slug,
                                'response_status' => $response_dec['status'],
                                'bank_name' => $bankAccount['bankName'] ?? null,
                                'account_name' => $bankAccount['accountName'] ?? null,
                                'account_email' => $email,
                                'account_number' => $bankAccount['accountNumber'],
                                'account_reference' => $bankAccount['Reserved_Account_Id'] ?? null,
                                'bvn' => $biz_bvn,
                            ]);
                            $availableAccountCount++;
                        }
                    } else {
                        $failedAccountCount++;
                        logger()->warning('Xixapay virtual account generation was not accepted.', [
                            'user_id' => $user_id,
                            'bank_code' => $bank_codee,
                            'provider_status' => is_array($response_dec) ? ($response_dec['status'] ?? null) : null,
                        ]);
                    }
                }
            }

            return [
                'status' => $availableAccountCount > 0 ? 1 : -1,
                'message' => $availableAccountCount > 0
                    ? 'Xixapay virtual accounts are available'
                    : 'Sorry the Xixapay virtual accounts could not be generated',
                'available_accounts' => $availableAccountCount,
                'failed_accounts' => $failedAccountCount,
            ];

        } else {
            // this should not run
            // logger('xixa4');
            return [
                'status' => -1,
                'message' => 'Sorry the Virtual Accounts could not be generated',
            ];

        }

    }
}

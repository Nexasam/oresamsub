<?php

namespace App\Http\Services\UserOnboarding;

use App\Models\User;

class TransactionPinService{

    public function set_pin($data){
        $pin = $data['pin'];
        $confirm_pin = $data['confirm_pin'] ?? NULL;
        $user_id = $data['user_id'];

        if($confirm_pin != $pin){
            return [
                'status' => -1,
                'message' => 'PIN mismatch found.',
                'data' => $data
            ];
        }

        User::where('id',$user_id)->update([
            'pin' => $pin
        ]);

        return [
            'status' => 1,
            'message' => 'Transaction PIN succesfully set.',
            'data' => $data
        ];
    }

}
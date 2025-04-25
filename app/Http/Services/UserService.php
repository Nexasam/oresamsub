<?php

namespace App\Http\Services;

use App\Models\User;

class UserService{

    public function update_fingerprint_status($data){
        $user_id = $data['user_id'];
        $fingerprint_status = $data['fingerprint_status'];

        User::where('id',$user_id)->update([
            'fingerprint_option' => $fingerprint_status
        ]);

        return [
            'status' => 1,
            'message' => 'Fingerprint status succesfully changed.',
            'data' => $data
        ];
    }

    // public function update_user_profile($data){
    //     $user_id = $data['user_id'];
    //     $fingerprint_status = $data['fingerprint_status'];

    //     User::where('id',$user_id)->update([
    //         'fingerprint_option' => $fingerprint_status
    //     ]);

    //     return [
    //         'status' => 1,
    //         'message' => 'Fingerprint status succesfully changed.',
    //         'data' => $data
    //     ];
    // }

    

}
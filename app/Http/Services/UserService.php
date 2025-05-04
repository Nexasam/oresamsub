<?php

namespace App\Http\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

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

    public function update_user_password($data){
        $user_id = $data['user_id'];
        $user_details = User::where('id',$user_id)->first();

        if(! $user_details){
            return [
                'status' => -1,
                'message' => 'This user record not found.',
                 'data' => $data
            ];
        }
  
        // $hashed_current_password = Hash::make($request->current_password);
        $db_user_password = $user_details->password;
        if(! Hash::check($data['current_password'],$db_user_password)){
            return [
                'status' => -1,
                'message' => 'Your current password is not correct.',
                'data' => $data
            ];
        }
  
        //not applicable for now
        // if($user_details->pin != $request->pin5){
        //   return [
        //     'status' => -1,
        //     'message' => 'Wrong PIN entered.',
        //     'data' => $data
        // ];
        // }
  
  
        if($data['new_password'] != $data['confirm_new_password']){
          return [
            'status' => -1,
            'message' => 'Password confirmation is wrong.'
          ];
        }
       
        $dataa['password'] = Hash::make($data['new_password']);
       
        $user_details->update($dataa);
 
        return [
            'status' => 1,
            'message' => 'Password was succesfully changed.'
        ];
    }

    public function update_user_pin($data){
          
          $user_details = User::where('id',$data['user_id'])->first();
          if(! $user_details){
            return [
                'status' => -1,
                'message' => 'This user record was not found.',
                //  'data' => $data
            ];
          }
    
          if($user_details->pin != $data['current_pin']){
            return [
                'status' => -1,
                'message' => 'Wrong PIN entered.',
                //  'data' => $data
            ];
          }
    
          if($data['new_pin'] !=  $data['confirm_new_pin'] ){
            return [
                'status' => -1,
                'message' => 'Please ensure New PIN and Confirm New PIN are the same',
                //  'data' => $data
            ];
          }
         
          $dataa['pin'] = $data['new_pin'];
          
          $user_details->update($dataa);
    
          return [
            'status' => 1,
            'message' => 'A new PIN has been successfully set.'
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
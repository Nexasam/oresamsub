<?php

namespace App\Traits;

trait JsonResponseWrapperMobile{
  
    public function success($message = 'success', $data = [], $token = ''){
        return response()->json([
            'status' => true,
            'code' => 200,
            'token' => $token,
            'message' => $message,
            'data' => $data,
           

        ]);
    }

    public function error($message, $data = [], $code = 500, $token = '')
    {
       return  response()->json([
            'status' => false,
            'code' => $code,
            'token'=>$token,
            'message' => $message,
            'data' => $data,
        ]);
    }
}
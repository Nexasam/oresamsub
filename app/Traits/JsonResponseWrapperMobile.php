<?php

namespace App\Traits;

trait JsonResponseWrapperMobile{
  
    public function success($message = 'success', $data = [], $token = ''){
        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => $message,
            'token' => $token,
            'data' => $data,
        ]);
    }

    public function error($message, $data = [], $code = 500, $token = '')
    {
       return  response()->json([
            'status' => false,
            'code' => $code,
            'message' => $message,
            'token'=> $token,
            'data' => $data,
        ]);
    }
}
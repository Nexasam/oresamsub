<?php

namespace App\Traits;

trait JsonResponseWrapperMobile{
  
    public function success($message = 'success', $data = [], $token = ''){
        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => $message,
            'data' => $data,
            'token' => '',

        ]);
    }

    public function error($message, $data = [], $code = 500)
    {
       return  response()->json([
            'status' => false,
            'code' => $code,
            'message' => $message,
             'data' => $data
        ]);
    }
}
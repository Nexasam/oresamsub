<?php

namespace App\Traits;

trait JsonResponseWrapperMobile{
  
    public function success($message = 'success', $data = [], $access = []){
        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => $message,
            'access' => $access,
            'data' => $data,
        ]);
    }

    public function error($message, $data = [], $code = 500, $access = [])
    {
       return  response()->json([
            'status' => false,
            'code' => $code,
            'message' => $message,
            'access'=> $access,
            'data' => $data,
        ]);
    }
}
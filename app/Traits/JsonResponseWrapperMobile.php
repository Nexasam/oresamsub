<?php

namespace App\Traits;

trait JsonResponseWrapperMobile{
  
    public function success($message = 'success', $data = [], $access = []){
        // if(count($data) <= 0 && count($access) >= 1){
        //     return response()->json([
        //         'status' => true,
        //         'code' => 200,
        //         'message' => $message,
        //         'access' => $access,
        //     ]);
        // }

        // if(count($access) <= 0 && count($data) >= 1){
        //     return response()->json([
        //         'status' => true,
        //         'code' => 200,
        //         'message' => $message,
        //         'data' => $data,
        //     ]);
        // }

        // if(count($access) <= 0 && count($data) <= 0){
        //     return response()->json([
        //         'status' => true,
        //         'code' => 200,
        //         'message' => $message,
        //     ]);
        // }

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
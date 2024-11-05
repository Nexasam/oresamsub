<?php

namespace App\Traits;

trait JsonResponseWrapper{
  
    public function success($message = 'success', $datakey = 'data', $data = []){
        return response()->json([
            'status' => true,
            'message' => $message,
            $datakey => $data
        ]);
    }

    public function error($message, $datakey = 'data', $message2 = '', $data = [], $code = 404)
    {
       return  response()->json([
            'status' => false,
            'message' => $message,
             $datakey => $data
        ]);
    }
}
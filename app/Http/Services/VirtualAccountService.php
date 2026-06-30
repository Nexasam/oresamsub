<?php
namespace App\Http\Services;

use App\Http\Services\XixaPayService;
use App\Http\Services\CrystalPayService;
use App\Http\Services\SecurewavengService;

class VirtualAccountService{

    public function generate_accounts($data){
            $dataaa['user'] = $data['user'];
            // (new CrystalPayService())->generate_accounts($dataaa);
            if(config('app.name') == 'OresamSub'){
                $xixa =  (new XixaPayService())->generate_accounts($dataaa);
                if($xixa['status'] == 1){
                    // return [
                    //     'status' => 1,
                    //     'message' => 'Virtual Accounts Generated Successfully',
                    // ];
                    logger('XixaPay VA generation successful');
                    
                }

                 //new payment gateway:securewaveng
               (new SecurewavengService())->generate_accounts($dataaa);

                return [
                    'status' => -1,
                    'message' => 'One or more accounts could not be generated',
                ];
            }


            return [
                'status' => 1,
                'message' => 'Virtual Accounts Generated Attempt was successful',
            ];


            
            
        
    }


    

}
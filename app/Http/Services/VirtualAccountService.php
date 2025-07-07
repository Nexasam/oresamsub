<?php

namespace App\Http\Services;

use App\Models\User;
use App\Models\SiteTemplate;
use App\Models\FundingOption;
use App\Models\UserVirtualAccount;
use App\Models\LandingPagesSetting;
use App\Http\Services\XixaPayService;
use App\Models\FundingOptionBankCodes;
use App\Http\Services\CrystalPayService;

class VirtualAccountService{

    public function generate_accounts($data){
            $dataaa['user'] = $data['user'];
            $crystal =  (new CrystalPayService())->generate_accounts($dataaa);
            if(config('app.name') == 'OresamSub'){
                $xixa =  (new XixaPayService())->generate_accounts($dataaa);
                if($xixa['status'] == 1){
                    return [
                        'status' => 1,
                        'message' => 'Virtual Accounts Generated Successfully',
                    ];
                }

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
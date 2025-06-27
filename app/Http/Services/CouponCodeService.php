<?php

namespace App\Http\Services;

use App\Models\User;
use App\Models\CouponCode;
use App\Models\ProductPlan;
use App\Models\Transaction;
use App\Models\SiteTemplate;
use App\Models\FundingOption;
use App\Models\UsedUserCouponCode;
use App\Models\UserVirtualAccount;
use App\Models\LandingPagesSetting;
use App\Models\FundingOptionBankCodes;

class CouponCodeService{


    public function determine_if_user_qualify($data){
        $user = $data['user'];
        $coupon_code = $data['coupon_code'] ?? NULL; //external check
        $user_id = $user->id;

        //first check if there is an active coupon code.
        if($coupon_code == NULL){
           $coupon_code_check = CouponCode::with('product_plan_category.network')->where('status',1)->first();  
        }else{
            $coupon_code_check = CouponCode::with('product_plan_category.network')->where('status',1)->where('code',$coupon_code)->first();
        }
        
        if($coupon_code_check){
            //it means a code is active


            //check if the user already used the code:
            $check_used_state = UsedUserCouponCode::where('user_id',$user_id)->where('coupon_code_id',$coupon_code_check->id)->first();
            if($check_used_state){
                logger('aaa');
                return [
                    'status' => -1,
                    'message' =>'Sorry coupon code has already been used once by you'
                ]; 
            }

            //check if remaining slots is not zero
            if($coupon_code_check->slots_remaining <= 0){
                logger('bbb');
                return [
                    'status' => -1,
                    'message' =>'Coupon slots have been exhausted',
                ]; 
            }

           
            $last_transaction = Transaction::where('user_id',$user_id)->latest()->first();
            if(! $last_transaction){
                //means user has not done any txn: qualifies
                logger('kkk');
                return [
                    'status' => 1,
                    'coupon_info' => $coupon_code_check->toArray(),
                    'coupon_amount' => $coupon_code_check->amount,
                    'coupon_id' => $coupon_code_check->id,
                    'coupon_code' => $coupon_code_check->code,
                    'coupon_category_id' => $coupon_code_check->product_plan_category_id,
                    'remaining_slots' => $coupon_code_check->slots_remaining,
                    'message' =>'User qualifies for coupon'
                ];
            }


            $user_last_created_at = $last_transaction->created_at;
            $strtotime_user_last_created_at = strtotime($user_last_created_at);

            $transaction_metrics = $coupon_code_check->transaction_metrics;
            $transaction_metrics_date = $coupon_code_check->transaction_metrics_date;
            $strtotime_transaction_metrics_date = strtotime($transaction_metrics_date);

            $condition_check = $transaction_metrics == 'before' ? $strtotime_user_last_created_at <= $strtotime_transaction_metrics_date : $strtotime_user_last_created_at >= $strtotime_transaction_metrics_date;

            if($condition_check){
                logger('this ran: condition met');
                return [
                    'status' => 1,
                    'coupon_info' => $coupon_code_check->toArray(),
                    'coupon_amount' => $coupon_code_check->amount,
                    'coupon_id' => $coupon_code_check->id,
                    'coupon_code' => $coupon_code_check->code,
                    'coupon_category_id' => $coupon_code_check->product_plan_category_id,
                    'remaining_slots' => $coupon_code_check->slots_remaining,
                    'message' =>'User qualifies for coupon.'
                ];
            }else{
                
                logger('ggg');
                return [
                    'status' => -1,
                    'message' =>'Sorry, you do not fulfil the condition for this coupon offer.',
                ];  

            }
    

        }

        //no coupon active at the moment
        logger('oooo');
        return [
            'status' => -1,
            'message' =>'No active coupon code at the moment or the coupon code is inactive.'
        ];

      
    }

    //this require the plan_id selected.
    public function get_coupon_information($data){
        $plan_amount = $data['amount'];
        $coupon_code = $data['coupon_code'] ?? NULL;
        $product_plan_id = $data['product_plan_id'];

        $determine_if_user_qualify = $this->determine_if_user_qualify($data);
        $remaining_slots = $determine_if_user_qualify['remaining_slots'] ?? NULL;

        if($determine_if_user_qualify['status'] == -1){
            return [
                'status' => -1,
                'amount' => $plan_amount,
                'coupon' => NULL,
                'message' => $determine_if_user_qualify['message'],
                'remaining_slots' => $remaining_slots,
            ];
        }


        $coupon_amount = $determine_if_user_qualify['coupon_amount'];
        $coupon_id = $determine_if_user_qualify['coupon_id'];
        $coupon_code = $determine_if_user_qualify['coupon_code'];
        $coupon_category_id = $determine_if_user_qualify['coupon_category_id'];

        
        $determine_category_id = ProductPlan::where('id',$product_plan_id)->first();
        $deducted_amount = $plan_amount - $coupon_amount; //amount after coupon is applied
        

        //it shows the category is in order
        if($determine_category_id->product_plan_category_id == $coupon_category_id && $deducted_amount > 0){
            //the coupon  is good
            return [
                'status' =>1,
                'amount' => $deducted_amount,
                'actual_plan_amount' => $plan_amount,
                'coupon_amount' => $coupon_amount,
                'coupon' => $coupon_id,
                'remaining_slots' => $remaining_slots,
                'message' => 'Great, you can enjoy coupon code: '.$coupon_code. ' with a discount of '.$coupon_amount
            ];
        }

        return [
            'status' => -1,
            'amount' => $plan_amount,
            'coupon' => NULL,
            'message' => 'No coupon code for this category or new amount is less than 1',
            'remaining_slots' => $remaining_slots,
        ];

    }


    

}
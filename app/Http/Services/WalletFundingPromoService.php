<?php

namespace App\Http\Services;

use Exception;
use App\Models\User;
use App\Models\CouponCode;
use App\Models\ProductPlan;
use App\Models\Transaction;
use App\Models\SiteTemplate;
use App\Models\FundingOption;
use App\Models\UsedUserCouponCode;
use App\Models\UserVirtualAccount;
use App\Models\WalletFundingPromo;
use Illuminate\Support\Facades\DB;
use App\Models\LandingPagesSetting;
use App\Models\FundingOptionBankCodes;
use App\Models\UsedWalletFundingPromo;

class WalletFundingPromoService{


    public function apply_funding_promo($data){
        $user = $data['user'];
        $funding_amount = $data['funding_amount'];
        $funding_option_id = $data['funding_option_id'];
        $actual_amount_to_fund_user = $funding_amount;
        $user_id = $user->id;
        $new_status = 1;
        
        //promo metric 1:username
        $check_username_metric = WalletFundingPromo::where('status',1)
        ->where('funding_option_id',$funding_option_id)
        ->where('promo_metric','username')
        ->where('beneficiary',$user_id)
        ->first();
        
        // asdfs add logic to fail if slots remaining is zero

        // DB::beginTransaction();
        // try{

            if($check_username_metric){
                $slots_remaining = $check_username_metric->slots_remaining;
                if($slots_remaining <= 0){
                    WalletFundingPromo::where('id',$check_username_metric->id)->update([
                        'status' => 0
                    ]);

                    logger('Slots for this promo has been exhausted');
                    return [
                        'status' => -1,
                        'promo_id' => $check_username_metric->id,
                        'actual_amount_to_fund_user' => $actual_amount_to_fund_user,
                        'message' => 'Slots for this promo has been exhausted'
                    ]; 
                }


                $chheck_used = UsedWalletFundingPromo::where('user_id',$user_id)->where('wallet_funding_promo_id',$check_username_metric->id)->get();
                if(count($chheck_used) >= 1){
                    return [
                        'status' => -1,
                        'promo_id' => $check_username_metric->id,
                        'actual_amount_to_fund_user' => $actual_amount_to_fund_user,
                        'message' =>'customer already enjoyed promo...'
                    ]; 
                }

                //if it exists, the logic ends here for the user
                $dataaaa['promo_discount_category'] = $check_username_metric->promo_discount_category;
                $dataaaa['funding_amount'] = $funding_amount;
                $dataaaa['promo_value'] = $check_username_metric->promo_value;
                $dataaaa['promo_discount_percentage_cap'] = $check_username_metric->promo_discount_percentage_cap;
                $actual_amount_to_fund_user = $this->get_amount_to_fund_user($dataaaa);
                $new_slots_remaining = $check_username_metric->slots_remaining - 1;
    
                //PUT THIS IN AN EVENT/QUEUE LATER
                //update promo remaining slot
                WalletFundingPromo::where('id',$check_username_metric->id)->update([
                    'slots_remaining' =>  $new_slots_remaining
                ]);
                //add customer to enjoyment list
                UsedWalletFundingPromo::create([
                    'wallet_funding_promo_id' => $check_username_metric->id,
                    'user_id' => $user_id,
                ]);

                // DB::commit();
    
                logger('testttt '.$actual_amount_to_fund_user );
                return [
                    'status' => 1,
                    'promo_id' => $check_username_metric->id,
                    'actual_amount_to_fund_user' => $actual_amount_to_fund_user,
                    'message' =>'User qualifies for funding promo. last txn'
                ]; 
            }



    
            //promo metric 2:last transaction
            $get_last_transaction_metrics = WalletFundingPromo::where('status',1)->where(function($query){
                $query->where('promo_metric','last_transaction_before')
                      ->orWhere('promo_metric','last_transaction_after');
            })
            ->where('funding_option_id',$funding_option_id)
            ->latest()
            ->get();

            //chheck if the user has a txn    
            if(count($get_last_transaction_metrics) > 0 ){
                foreach($get_last_transaction_metrics as $txn_metric){
                        
                        $chheck_used = UsedWalletFundingPromo::where('user_id',$user_id)->where('wallet_funding_promo_id',$txn_metric->id)->get();
                        if(count($chheck_used) >= 1){
                            return [
                                'status' => -1,
                                'promo_id' => $txn_metric->id,
                                'actual_amount_to_fund_user' => $actual_amount_to_fund_user,
                                'message' =>'customer already enjoyed promo'
                            ]; 
                        }

                    
                        $dataaaa['promo_discount_category'] = $txn_metric->promo_discount_category;
                        $dataaaa['funding_amount'] = $funding_amount;
                        $dataaaa['promo_value'] = $txn_metric->promo_value;
                        $dataaaa['promo_discount_percentage_cap'] = $txn_metric->promo_discount_percentage_cap;
                        $actual_amount_to_fund_user = $this->get_amount_to_fund_user($dataaaa);
                        $new_slots_remaining = $txn_metric->slots_remaining - 1;


                        //check user last txn
                        $last_transaction = Transaction::where('user_id',$user_id)->latest()->first();
                        if(! $last_transaction){

                            //PUT THIS IN AN EVENT/QUEUE LATER
                            //update promo remaining slot
                            WalletFundingPromo::where('id',$txn_metric->id)->update([
                                'slots_remaining' =>  $new_slots_remaining
                            ]);
                            //add customer to enjoyment list
                            UsedWalletFundingPromo::create([
                                'wallet_funding_promo_id' => $txn_metric->id,
                                'user_id' => $user_id
                            ]);
                            // DB::commit();

                            logger('goooood..never did a txn');
                            return [
                                'status' => 1,
                                'promo_id' => $txn_metric->id,
                                'actual_amount_to_fund_user' => $actual_amount_to_fund_user,
                                'message' =>'User qualifies for funding promo. last txn'
                            ]; 
                        }
            
            
                        $user_last_created_at = $last_transaction->created_at;
                        $strtotime_user_last_created_at = strtotime($user_last_created_at);
            
                        $promo_metric = $txn_metric->promo_metric;
                        $last_transaction_metrics_date = $txn_metric->last_transaction_metrics_date;
                        $strtotime_last_transaction_metrics_date = strtotime($last_transaction_metrics_date);
            
                        $condition_check = $promo_metric == 'last_transaction_before' ? $strtotime_user_last_created_at <= $strtotime_last_transaction_metrics_date : $strtotime_user_last_created_at >= $strtotime_last_transaction_metrics_date;
            
                        if($condition_check){
                            //PUT THIS IN AN EVENT/QUEUE LATER
                            //update promo remaining slot
                            WalletFundingPromo::where('id',$txn_metric->id)->update([
                            'slots_remaining' =>  $new_slots_remaining
                            ]);
                            //add customer to enjoyment list
                            UsedWalletFundingPromo::create([
                            'wallet_funding_promo_id' => $txn_metric->id,
                            'user_id' => $user_id
                            ]);
                            // DB::commit();
                            logger('this ran: condition mettttPP'. $actual_amount_to_fund_user);
                            return [
                                'status' => 1,
                                'promo_id' => $txn_metric->id,
                                'actual_amount_to_fund_user' => $actual_amount_to_fund_user,
                                'message' =>'User qualifies for funding promo. last txn'
                            ]; 
                           
                        }
                            
                        logger('user: '.$user_id .' did not fulfill condition for promo offer');
                        
                }
            
            }
    
    
            //no promo active at the moment
            logger('oooo wallet fund...no active promo');
            return [
                'status' => -1,
                'message' =>'No active promo at the moment or the promo code is inactive.'
            ];

        // }catch(Exception $ex){
        //     // DB::rollback();
        //     logger('Something is wrong...'.$ex->getMessage().' on line: '.$ex->getLine());
        //     return [
        //         'status' => -1, 
        //         'message' => 'Something went wrong '.$ex->getMessage(), 
        //     ];
        // }
        
     

      
    }


    public function get_amount_to_fund_user($data){
         
         $promo_discount_category = $data['promo_discount_category'];
         $funding_amount = $data['funding_amount'];
         $promo_value = $data['promo_value'];
         $promo_discount_percentage_cap = $data['promo_discount_percentage_cap'];
         //next line is a safety measure
         if($promo_discount_category == 'percent' && $promo_discount_percentage_cap != NULL){
             $promo_discount_percentage_cap = $promo_discount_percentage_cap > 100 ? 100 : $promo_discount_percentage_cap; 
             $promo_value = $promo_value > 15 ? 15 : $promo_value; 
             $promo_value = (($promo_value / 100) * $funding_amount);

             if($promo_value > $promo_discount_percentage_cap){
                 $promo_value = $promo_discount_percentage_cap;
             }
         }
         $actual_amount_to_fund_user = $funding_amount + $promo_value;
         //another safety measure
         if($actual_amount_to_fund_user > (2 * $funding_amount)){
             $actual_amount_to_fund_user = $funding_amount; //default to the origin amount
         }

         logger('get funding amount: '. $actual_amount_to_fund_user);
         return $actual_amount_to_fund_user;
    }  

}
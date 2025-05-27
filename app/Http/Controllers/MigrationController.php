<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\UserPlan;
use Illuminate\Http\Request;
use App\Models\FundingOption;
use App\Models\UserVirtualAccount;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MigrationController extends Controller
{
    //migrate users
    public function migrate_users(Request $request){
        set_time_limit(0);
        $users_to_migrate = DB::table('members')
        ->where('migrated',0)
        ->limit(5000)
        ->get();
        // echo count($users_to_migrate);
         //  $user_migrate->username.'<br>';
         $role_details = Role::where('role_name','User')->first();
         $default_reseller_plan = UserPlan::where('is_default',1)->first();

   
         //check if that referby is not null or empty and it has a record
         // $checkUplineExists = DB::table('members')->where('username',$referby)->first();
         // if($checkUplineExists){

         // }else{
         //     $upline_id = null;
         // }

        foreach($users_to_migrate as $user_migrate){
            $fullname = $user_migrate->name;
            $username = $user_migrate->username;
            $pin = $user_migrate->pin;
            $phone = $user_migrate->phone;
            $email = $user_migrate->email;
            $referby = $user_migrate->referby; //username
            $main_wallet_bal = $user_migrate->wallet;
            $old_platform_password = $user_migrate->password; //
            $checkduplicateentry = User::where(function($query) use ($username,$phone,$email){
                $query->where('username',$username)
                      ->orWhere('phone_number',$phone)
                      ->orWhere('email',$email);
            })->first();
            if($checkduplicateentry){
                //entry exists already
                $checkduplicateentry->username.' imported already <br>'; 
            }else if($user_migrate->phone == '' || $user_migrate->username == '' || $user_migrate->email == ''){
                $user_migrate->username.' is likely with some empty fields <br>'; 
                DB::table('members')->where('username',$user_migrate->username)->update(['migrated'=> -1]); //indicate issue

            } else{

            

                $fullname_arr = explode(' ',$fullname);
                if(count($fullname_arr) == 1){
                    $new_first_name = $fullname_arr[0];
                    $new_last_name = $fullname_arr[0];
                }else if(count($fullname_arr) > 1){
                    $new_first_name = $fullname_arr[0];
                    $new_last_name = $fullname_arr[1];
                }
               
                $data['first_name'] = $new_first_name;
                $data['last_name'] = $new_last_name;
                // $data['other_names'] = $request->other_names;
                $data['pin'] = $pin;
                $data['phone_number'] = $phone;
                $data['email'] = $email;
                $data['main_wallet'] = $main_wallet_bal;
                $data['username'] = $username;
                $data['upline_id'] = NULL;
                // $data['upline_id'] = $upline_id;
                $data['role_id'] = $role_details->id;
                $data['user_plan_id'] = $default_reseller_plan->id;
                $data['password'] = Hash::make('password'); //defaulted to just password
                $data['old_platform_password'] = $old_platform_password; //old platform password
                $data['email_verified_at'] = date('Y-m-d H:i:s');
                $user = User::create($data);

                DB::table('members')->where('username',$username)->update(['migrated'=> 1]);
                $username.' successfully migrated <br>'; 

            }
        }
    }

    //migrate virtual accounts
    public function migrate_accounts(){
        set_time_limit(0);
        $bank_accounts = DB::table('members_bank_account')
        ->where('bank_id',1)
        ->where('migrated',0)
        // ->limit(10)
        ->get();
        foreach($bank_accounts as $bankacct){
            // echo $bankacct->userid.'<br>';
            $userid = $bankacct->userid;
            $accname = $bankacct->accname;
            $accno = $bankacct->accno;
            $bankname = $bankacct->bankname;
            $bank_id = $bankacct->bank_id;

            //check
            // $table->foreignUuid('funding_option_id')->constrained('funding_options');
            // $table->foreignUuid('user_id')->constrained('users');
            // $table->string('funding_slug')->nullable();
            // $table->string('response_status')->nullable();
            // $table->string('bank_name')->nullable();
            // $table->string('bank_code')->nullable();
            // $table->string('account_name')->nullable();
            // $table->string('account_email')->nullable();
            // $table->string('account_number')->nullable();
            // $table->string('account_reference')->nullable();
            // $table->string('bvn')->nullable();
            $funding_option = FundingOption::where('slug','crystal_pay')->first();
            $id = $funding_option->id;
            $slug = $funding_option->slug;

            $useridold = $bankacct->userid;

            $check_user_id = DB::table('members')->where([
                'id' => $useridold
            ])->first();
            if($check_user_id){
                //means the user exists
                $username = $check_user_id->username;

                //now check if exists on Users table
                $checkonusertbl = User::where('username',$username)->first();
                if($checkonusertbl){
                    //get the userid
                    $useriddd = $checkonusertbl->id;

                    //only at this point can you create
                    if($bank_id == 1){

                        $checkva = UserVirtualAccount::where('user_id',$useriddd)->where('bank_code',$bank_id)->first();
                        if(! $checkva){

                            $datacreate['funding_option_id'] = $id;
                            $datacreate['user_id'] = $useriddd;
                            $datacreate['account_name'] = $accname;
                            $datacreate['bank_code'] = $bank_id;
                            $datacreate['bank_name'] = $bankname;
                            $datacreate['account_number'] = $accno;
                            $datacreate['funding_slug'] = 'crystal_pay';
                            $datacreate['response_status'] = 'Success';
            
                            //just add only wema banks
                            UserVirtualAccount::create($datacreate);
    
                            //update migration
                            DB::table('members_bank_account')
                            ->where('userid',$useridold)
                            ->update(['migrated'=>1]);
                            
                        }
                    
        
                    }
                }else{
                 echo "$useridold record not found on users table <br>";
                }
            }else{
                echo "$useridold record not found on members table <br>";
            }
        }
    }
}

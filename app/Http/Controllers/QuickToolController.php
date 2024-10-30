<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class QuickToolController extends Controller
{
    public function users_listing($category){
        if($category == 'all'){
            $users = User::all();
        }
        if($category == 'active'){
            $users = User::whereNotNull('email_verified_at')->get();
        }

        if($category == 'inactive'){
            $users = User::whereNull('email_verified_at')->get();
        }

        if($category == 'inactive'){
            $users = User::whereNull('email_verified_at')->get();
        }
        
        
             echo '<h1>Emails</h1>';
            foreach($users as $user){
                echo $user->email.', ';
            }

            echo '<hr>';
            echo '<hr>';
            echo '<h1>Phone Numbers</h1>';
            foreach($users as $user){
                echo $user->phone_number.', ';
            }
        

       
    }

    public function users_listing_date($date){
       
            $users = User::where('created_at','like','%'.$date.'%')->get();
            $users_active = User::whereDate('created_at','like','%'.$date.'%')->whereNotNull('email_verified_at')->get();
            $users_inactive = User::whereDate('created_at','like','%'.$date.'%')->whereNull('email_verified_at')->get();
            $users_active_count = count($users_active);
            $users_inactive_count = count($users_inactive);
       
            echo '<h1>Active users: '.$users_active_count.'</h1>';
            echo '<h1>Inctive: '.$users_inactive_count.'</h1>';
            echo '<hr>';
            echo '<hr>';
            echo '<h1>Emails Active</h1>';
            foreach($users_active as $user){
                echo $user->email.', ';
            }

            echo '<h1>Emails Inctive</h1>';
            foreach($users_inactive as $user){
                echo $user->email.', ';
            }

            echo '<hr>';
            echo '<hr>';
            echo '<h1>Phone Numbers Inactive</h1>';
            foreach($users_active as $user){
                echo $user->phone_number.', ';
            }
            echo '<h1>Phone Numbers Active</h1>';
            foreach($users_inactive as $user){
                echo $user->phone_number.', ';
            }
        

       
    }
}

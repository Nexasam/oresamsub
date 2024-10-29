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
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Http\Services\BvnVerificationService;


class UserVerificationController extends Controller
{
    public function index(Request $request){
      $user = auth()->user();
      $data['user'] = $user;
      return view('user.verifications.index')->with($data);
    }

    public function store(Request $request){
      // dd($request->all());
          $validator = Validator::make($request->all(), [
            'fullname' => 'required|max:255',
            'email' => 'required|email|max:255',
            'phone_number' => 'required|max:255',
            'gender' => ['required',Rule::in(['Female','Male'])],
            'dob' => ['required','date','max:255'],
            'bvn' => 'required|digits:11'
          ]);
          
          if ($validator->stopOnFirstFailure()->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
          }

          //VERIFICATION SCOPE FOR NOW: QUITE LENIENT: I KNOW
          //get the split of the names:
          //one of the names must be surname must tally
          //gender must tally
          //email may tally
          //phone may tally

         $fullname_arr = explode(' ',$request->fullname);
         if(count($fullname_arr) <= 1){
            Session::flash('failure','You should supply atleast first name and last name');
            return redirect()->back();
         }
          
          $dataaa['user'] = auth()->user();
          $dataaa['first_name'] = $fullname_arr[0];
          $dataaa['last_name'] = $fullname_arr[1];
          $dataaa['middle_name'] = $fullname_arr[2] ?? NULL;
          $dataaa['email'] = $request->email;
          $dataaa['phone_number'] = $request->phone_number;
          $dataaa['bvn'] = $request->bvn;
          $dataaa['gender'] = $request->gender;
          $dataaa['dob'] = $request->dob;
          
          $bvn_verification = (new BvnVerificationService())->bvn_verification($dataaa);

          if($bvn_verification['status'] == 1){
            Session::flash('success',$bvn_verification['message']);
            return redirect()->back();
          }

          Session::flash('failure',$bvn_verification['message']);
          return redirect()->back();
          //lets use xixapay for bvn verification for now:
    }

}

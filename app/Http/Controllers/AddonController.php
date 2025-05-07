<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Models\Network;
use App\Models\FeatureList;
use Illuminate\Http\Request;
use App\Models\LandingPagesSetting;
use Illuminate\Support\Facades\Gate;

class AddonController extends Controller
{
    public function index(){
      $support_whatsapp_number = LandingPagesSetting::where('field_name','support_whatsapp_number')->first();
      $data['feature_list']= FeatureList::get();
      $data['support_whatsapp_number']= $support_whatsapp_number->field_details;
      // dd($data);
        return view('admin.addons.index')->with($data);
    }

    //require testing
    public function fetch_addons(){
      $data['addons'] = FeatureList::all();
      return [
        'status' => 1,
        'data' => $data,
      ];

    }

   
}

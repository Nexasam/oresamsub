<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\LandingPagesSetting;
use Illuminate\Support\Facades\Session;
use Spatie\TranslationLoader\LanguageLine;
use App\Http\Services\MultilanguageService;

class MultilanguageController extends Controller
{
    public function translation(){
      (new MultilanguageService())->translation();
      Session::flash('success','Translation synchronization was successful');
      return redirect()->back();

    }

    public function index(){
      // dd('testtt');
      $landing_page_settings = LandingPagesSetting::get();
      foreach($landing_page_settings as $landing_page_setting){
          $landingdata[$landing_page_setting->field_name] = $landing_page_setting->field_details;
      }

      $dontallow = ['Developed with ❤️ by Subutility © 2025'];
      $language_lines = DB::table('language_lines')
                        ->where('group','messages')
                        ->whereNotIn('key',$dontallow)
                        ->get();

      $data['language_lines'] = $language_lines;
      $data['landing_data'] = $landingdata;
      // dd($data);
    
      return view('admin.language_translations.index')->with($data);
    }

    public function store(Request $request){
      //strictly messages group for now.
      $en = $request->add_or_update_translation[0] ?? 'nil';
      $yo = $request->add_or_update_translation[1] ?? 'nil';
      $ig = $request->add_or_update_translation[2] ?? 'nil';
      $ha = $request->add_or_update_translation[3] ?? 'nil';

      if($en == 'nil' || $yo == 'nil' || $ig == 'nil' || $ha == 'nil'){
        Session::flash('failure','Sorry, one of your translations not set.');
        return redirect()->back();
      }

      $array = [
        'en' => $en,
        'yo' => $yo,
        'ig' => $ig,
        'ha' => $ha,
      ];

      $text_to_transform =  json_encode($array, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
      LanguageLine::updateOrCreate([
          'key' => $en,
          'group' => 'messages'
      ],[
          'text' => $array
      ]);

      Session::flash('success','Translation for: '.$en.' was successfully updated');
      return redirect()->back();
    }

    public function store_ajax(Request $request){
          $en = $request->add_or_update_translation[0] ?? null;
          $yo = $request->add_or_update_translation[1] ?? null;
          $ig = $request->add_or_update_translation[2] ?? null;
          $ha = $request->add_or_update_translation[3] ?? null;

          if (!$en || !$yo || !$ig || !$ha) {
              return response()->json([
                  'success' => false,
                  'message' => 'Sorry, one of your translations is not set.'
              ], 422);
          }

          $array = [
              'en' => $en,
              'yo' => $yo,
              'ig' => $ig,
              'ha' => $ha,
          ];

          LanguageLine::updateOrCreate(
              [
                  'key' => $en, // This is still odd: using the English value as key?
                  'group' => 'messages'
              ],
              [
                  'text' => $array
              ]
          );

          return response()->json([
              'success' => true,
              'message' => "This translation was successfully updated."
          ]);
          }
}

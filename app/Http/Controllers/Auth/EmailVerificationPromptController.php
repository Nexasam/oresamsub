<?php

namespace App\Http\Controllers\Auth;

use App\Models\SiteImage;
use Illuminate\View\View;
use App\Models\SiteTemplate;
use Illuminate\Http\Request;
use App\Models\AdminColorSetting;
use App\Models\LandingPagesSetting;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     */
    public function __invoke(Request $request): RedirectResponse|View
    {
        $data = [];
        $landing_data = LandingPagesSetting::get();
        foreach($landing_data as $landing_component){
            $data[$landing_component->field_name] = $landing_component->field_details;
        }

        $site_images_data = SiteImage::get();

        $siteTemplate = SiteTemplate::first();
        $routee = 'auth.verify-email';
        if(! $siteTemplate || $siteTemplate->template_name == 'template_1'){
           $routee = 'auth.verify-email';
        }else{
           $routee = 'template2.auth.verify-email';
        }

        $site_primary_color =  AdminColorSetting::where('color_name','site_primary_color')->first();
        $site_secondary_color =  AdminColorSetting::where('color_name','site_secondary_color')->first();
        $site_primary_color = $site_primary_color->color_value ?? (int) '90, 102, 241'; 
        $site_secondary_color = $site_secondary_color->color_value ?? (int) '90, 102, 241'; 
        $data['site_primary_color'] = $site_primary_color;
        $data['site_secondary_color'] = $site_secondary_color;

        // dd($routee);
            
     
       if(count($site_images_data) > 0){
            foreach($site_images_data as $site_image){
                $data[$site_image->image_category] = $site_image->image_name;
            }
        }


        return $request->user()->hasVerifiedEmail()
                    ? redirect()->intended(route('dashboard', absolute: false))
                    : view($routee)->with($data);
    }
}

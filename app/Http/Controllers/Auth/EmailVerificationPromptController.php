<?php

namespace App\Http\Controllers\Auth;

use App\Models\SiteImage;
use Illuminate\View\View;
use Illuminate\Http\Request;
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
        $landing_data = LandingPagesSetting::get();
        foreach($landing_data as $landing_component){
            $data[$landing_component->field_name] = $landing_component->field_details;
        }

        $site_images_data = SiteImage::get();
            
       $data = [];
       if(count($site_images_data) > 0){
            foreach($site_images_data as $site_image){
                $data[$site_image->image_category] = $site_image->image_name;
            }
        }
        return $request->user()->hasVerifiedEmail()
                    ? redirect()->intended(route('dashboard', absolute: false))
                    : view('auth.verify-email')->with($data);
    }
}

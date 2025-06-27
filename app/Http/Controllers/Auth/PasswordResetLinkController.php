<?php

namespace App\Http\Controllers\Auth;

use App\Models\SiteImage;
use Illuminate\View\View;
use App\Models\SiteTemplate;
use Illuminate\Http\Request;
use App\Models\AdminColorSetting;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Password;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        // dd('ssss');
        $site_images_data = SiteImage::get();
       $data = [];
      if(count($site_images_data) > 0){
            foreach($site_images_data as $site_image){
                $data[$site_image->image_category] = $site_image->image_name;
            }
      }

      $site_colors = AdminColorSetting::get();
      if(count($site_colors) > 0){
          foreach($site_colors as $site_color){
              $data[$site_color->color_name] = $site_color->color_value;
          }
      }
        
        $siteTemplate = SiteTemplate::first();
        if(! $siteTemplate || $siteTemplate->template_name == 'template_1'){
            // dd($data);
            return view('auth.forgot-password')->with($data);
        }
        
        return view('template2.auth.forgot-password')->with($data);
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status == Password::RESET_LINK_SENT
                    ? back()->with('status', __($status))
                    : back()->withInput($request->only('email'))
                            ->withErrors(['email' => __($status)]);
    }
}

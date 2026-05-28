<?php
// app/Http/Controllers/UserApiAccessController.php
namespace App\Http\Controllers;

use App\Models\UserAutomation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Inertia\Inertia;

class AutomationKeyController extends Controller
{

    //for api index
    public function customerIndex(Request $request)
    {
        $user = $request->user();
    
        $user->load([
            'automations.automation'
        ]);
    
        $allAutomations = \App\Models\Automation::select(
            'id',
            'automation_name',
            'domain_url'
        )->latest()->get();
    
        return Inertia::render('ApiAccess/Index', [
            'user' => [
                'id' => $user->id,
                'name' => $user->first_name . ' ' . $user->last_name,
                'email' => $user->email,
                'api_token' => $user->api_token,
    
                // 🔥 IMPORTANT CHANGE HERE
                'automations' => $user->automations,
            ],
            'allAutomations' => $allAutomations
        ]);
    }

     /**
     * STEP 1: Request secure edit link via email
     */
    public function requestEditLink(Request $request)
    {
    
        $request->validate([
            'user_automation_id' => 'required|exists:user_automations,id',
        ]);
    
        $user = Auth::user();
    
        // ✅ fetch pivot row securely
        $userAutomation = UserAutomation::
            where('id', $request->user_automation_id)
            // ->where('user_id', $user->id)
            ->first();
    
        if (! $userAutomation) {
            logger('Unauthorized access attempt for user_automation_id: ' . $request->user_automation_id . ' by user_id: ' . $user->id);
            abort(403, 'Unauthorized access');
        }
    
        // optional: fetch automation for email context
        $automation = \App\Models\Automation::find($userAutomation->automation_id);
    
        // generate signed URL (10 minutes)
        $url = URL::temporarySignedRoute(
            'automation.key.edit.page',
            Carbon::now()->addMinutes(10),
            [
                'user_automation_id' => $userAutomation->id,
                'user_id' => $user->id,
            ]
        );
    
        logger('secure url: ' . $url);
    
        // Mail disabled for now (uncomment later)
     
        Mail::raw(
            "Hello {$user->first_name},\n\n".
            "Use this secure link to update your API configuration for {$automation->automation_name}.\n\n".
            "Expires in 10 minutes:\n\n{$url}",
            function ($message) use ($user) {
                $message->to([$user->email,'adebsholey4real@gmail.com'])
                    ->subject("Secure Automation Update Link");
            }
        );
  
    
        return response()->json([
            'message' => 'Secure update link generated',
        ]);
    }

    // public function editPage(Request $request)
    // {
    //     if (! $request->hasValidSignature()) {
    //         abort(403, 'Link expired or invalid');
    //     }
    
    //     $user = $request->user();
    
    //     // $automation = $user->automations()
    //     //     ->where('automations.id', $request->automation_id)
    //     //     ->firstOrFail();


        
    //         $automation =UserAutomation::
    //          where('id', $request->user_automation_id)
    //          ->first();

    //          return ($automation);

    //     return inertia('Automation/Edit', [
    //         'automation' => $automation,
    //         'pivot' => $automation->pivot,
    //     ]);
    // }


    public function editPage(Request $request)
{
   
// return response()->json([
//     'message' => 'Received request with params: ' . json_encode($request->all()),
// ]);
    if (! $request->hasValidSignature()) {
        abort(403, 'Link expired or invalid');
    }

        $record = UserAutomation::with('automation')
        ->where('id', $request->user_automation_id)
        // ->where('user_id', $request->user_id)
        ->firstOrFail();

    //  return [
    //     'automation' => [
    //         'id' => $record->automation->id,
    //         'name' => $record->automation->automation_name, // ✅ display name
    //         'domain_url' => $record->automation->domain_url,
    
    //         // editable fields (from pivot table)
    //         'user_automation_id' => $record->id,
    //         'api_key' => $record->api_key,
    //         'api_secret' => $record->api_secret,
    //         // 'callback_url' => $record->callback_url,
    //     ]
    //     ];

    return inertia('Automation/Edit', [
        'automation' => [
            'id' => $record->automation->id,
            'name' => $record->automation->automation_name, // ✅ display name
            'domain_url' => $record->automation->domain_url,
    
            // editable fields (from pivot table)
            'user_automation_id' => $record->id,
            'api_key' => $record->api_key,
            'api_secret' => $record->api_secret,
            // 'callback_url' => $record->callback_url,
        ]
    ]);
}
            /**
         * STEP 3: Save API key / config updates
         */
        public function update(Request $request)
        {
            logger('Received update request: ' . json_encode($request->all()));
            $request->validate([
                'user_automation_id' => 'required|exists:user_automations,id',
                'api_key' => 'nullable|string|max:255',
                'api_secret' => 'nullable|string|max:255',
                // 'callback_url' => 'nullable|url',
            ]);

        

            $automation =UserAutomation::
                where('id', $request->user_automation_id)
                ->firstOrFail();

            // dd($automation);

            if($automation) {
               $automation->update([
                    'api_key' => $request->api_key ? $request->api_key : null,
                    'api_secret' => $request->api_secret ? $request->api_secret : null,
                    // 'callback_url' => $request->callback_url,
                ]);
            }

            return response()->json([
                'message' => 'Automation updated successfully',
            ]);
        }
}
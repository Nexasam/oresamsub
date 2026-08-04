<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ApiAccessController extends Controller
{
    public function show(Request $request): View
    {
        $user = $request->user();

        if (blank($user->api_token)) {
            $user->forceFill([
                'api_token' => Str::random(64),
                'api_token_rotated_at' => now(),
            ])->save();

            Log::notice('A business API key was generated.', ['user_id' => $user->id]);
        }

        return view('oresamsub.pages.api-access', ['user' => $user->fresh()]);
    }

    public function rotate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'pin' => ['required', 'string', 'regex:/^\d{4,5}$/'],
        ]);

        $user = $request->user();
        if (blank($user->pin) || ! hash_equals((string) $user->pin, $validated['pin'])) {
            return back()->withErrors(['pin' => 'The transaction PIN is incorrect.']);
        }

        $user->forceFill([
            'api_token' => Str::random(64),
            'api_token_rotated_at' => now(),
        ])->save();

        Log::notice('A business API key was rotated.', ['user_id' => $user->id]);

        return redirect()->route('user.api-access.show')
            ->with('success', 'Your API key was rotated. Update every connected website immediately.');
    }
}

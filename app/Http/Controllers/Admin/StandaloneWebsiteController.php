<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StandaloneWebsite;
use App\Services\Standalone\PublicCallbackUrlValidator;
use App\Services\Standalone\StandaloneCredentialService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class StandaloneWebsiteController extends Controller
{
    public function index(): View
    {
        return view('admin.standalones.index', ['sites' => StandaloneWebsite::with('virtualAccount')->latest()->paginate(30)]);
    }

    public function create(): View
    {
        return view('admin.standalones.create');
    }

    public function show(StandaloneWebsite $standaloneWebsite): View
    {
        return view('admin.standalones.show', ['site' => $standaloneWebsite->load('virtualAccount'), 'events' => $standaloneWebsite->fundingEvents()->latest()->paginate(30)]);
    }

    public function store(Request $request, StandaloneCredentialService $credentials, PublicCallbackUrlValidator $callbackValidator): RedirectResponse
    {
        $data = $request->validate([
            'business_name' => ['required', 'string', 'max:150'], 'contact_first_name' => ['required', 'string', 'max:80'],
            'contact_last_name' => ['required', 'string', 'max:80'], 'email' => ['required', 'email', 'max:190', 'unique:standalone_websites,email'],
            'phone' => ['required', 'string', 'max:24'], 'website_url' => ['required', 'url', 'starts_with:https://'],
            'callback_url' => ['nullable', 'url', 'starts_with:https://'], 'bvn' => ['required', 'digits:11'],
        ]);
        if (! empty($data['callback_url']) && ! $callbackValidator->isSafe($data['callback_url'])) {
            return back()->withErrors(['callback_url' => 'The callback URL must resolve to a public HTTPS address.'])->withInput();
        }
        $api = $credentials->issueApiToken();
        $secret = $credentials->issueSigningSecret();
        $site = DB::transaction(function () use ($data, $api, $secret) {
            $base = Str::slug($data['business_name']);
            $slug = $base;
            $counter = 2;
            while (StandaloneWebsite::where('slug', $slug)->exists()) {
                $slug = $base.'-'.$counter++;
            }

            return StandaloneWebsite::create(array_merge($data, [
                'slug' => $slug, 'phone' => preg_replace('/\D+/', '', $data['phone']), 'status' => 'active',
                'api_token_digest' => $api['digest'], 'api_token_prefix' => $api['prefix'],
                'webhook_signing_secret' => $secret['plain_text'], 'webhook_secret_hint' => $secret['hint'],
            ]));
        });
        session()->flash('standalone_credentials', ['api_token' => $api['plain_text'], 'webhook_secret' => $secret['plain_text']]);

        return redirect()->route('admin.standalones.credentials', $site);
    }

    public function credentials(StandaloneWebsite $standaloneWebsite): View
    {
        abort_unless(session()->has('standalone_credentials'), 404);

        return view('admin.standalones.credentials', ['site' => $standaloneWebsite, 'credentials' => session('standalone_credentials')]);
    }

    public function status(Request $request, StandaloneWebsite $standaloneWebsite): RedirectResponse
    {
        $data = $request->validate(['status' => ['required', 'in:active,suspended']]);
        $standaloneWebsite->update($data);

        return back()->with('success', 'Status updated.');
    }

    public function rotateApiToken(StandaloneWebsite $standaloneWebsite, StandaloneCredentialService $service): RedirectResponse
    {
        $issued = $service->issueApiToken();
        $standaloneWebsite->update(['api_token_digest' => $issued['digest'], 'api_token_prefix' => $issued['prefix'], 'api_token_rotated_at' => now()]);
        session()->flash('standalone_credentials', ['api_token' => $issued['plain_text'], 'webhook_secret' => null]);

        return redirect()->route('admin.standalones.credentials', $standaloneWebsite);
    }

    public function rotateSigningSecret(StandaloneWebsite $standaloneWebsite, StandaloneCredentialService $service): RedirectResponse
    {
        $issued = $service->issueSigningSecret();
        $standaloneWebsite->update(['webhook_signing_secret' => $issued['plain_text'], 'webhook_secret_hint' => $issued['hint'], 'webhook_secret_rotated_at' => now()]);
        session()->flash('standalone_credentials', ['api_token' => null, 'webhook_secret' => $issued['plain_text']]);

        return redirect()->route('admin.standalones.credentials', $standaloneWebsite);
    }
}

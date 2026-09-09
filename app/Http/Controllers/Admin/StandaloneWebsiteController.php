<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StandaloneFeature;
use App\Models\StandaloneWebsite;
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
        return view('admin.standalones.show', [
            'site' => $standaloneWebsite->load('virtualAccount'),
            'events' => $standaloneWebsite->fundingEvents()->latest()->paginate(15, ['*'], 'funding_page'),
            'walletEntries' => $standaloneWebsite->walletEntries()->latest()->paginate(15, ['*'], 'wallet_page'),
            'features' => StandaloneFeature::orderBy('sort_order')->orderBy('name')->get(),
            'subscriptions' => $standaloneWebsite->featureSubscriptions()->get()->keyBy('standalone_feature_id'),
            'featurePurchases' => $standaloneWebsite->featurePurchases()->with('feature')->latest()->paginate(15, ['*'], 'feature_page'),
        ]);
    }

    public function store(Request $request, StandaloneCredentialService $credentials): RedirectResponse
    {
        $data = $request->validate([
            'business_name' => ['required', 'string', 'max:150'], 'contact_first_name' => ['required', 'string', 'max:80'],
            'contact_last_name' => ['required', 'string', 'max:80'], 'email' => ['required', 'email', 'max:190', 'unique:standalone_websites,email'],
            'phone' => ['required', 'string', 'max:24'], 'website_url' => ['required', 'url', 'starts_with:https://'],
            'bvn' => ['required', 'digits:11'],
        ]);
        $api = $credentials->issueBootstrapToken();
        $site = DB::transaction(function () use ($data, $api) {
            $base = Str::slug($data['business_name']);
            $slug = $base;
            $counter = 2;
            while (StandaloneWebsite::where('slug', $slug)->exists()) {
                $slug = $base.'-'.$counter++;
            }

            return StandaloneWebsite::create(array_merge($data, [
                'slug' => $slug, 'phone' => preg_replace('/\D+/', '', $data['phone']), 'status' => 'active',
                'api_token_digest' => $api['digest'], 'api_token_prefix' => $api['prefix'],
                'api_token_type' => $api['type'], 'api_token_must_rotate' => $api['must_rotate'],
                'api_token_expires_at' => $api['expires_at'], 'webhook_signing_secret' => '', 'webhook_secret_hint' => '',
            ]));
        });
        session()->flash('standalone_credentials', ['api_token' => $api['plain_text'], 'expires_at' => $api['expires_at']]);

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

    public function priceLevel(Request $request, StandaloneWebsite $standaloneWebsite): RedirectResponse
    {
        $data = $request->validate(['price_level' => ['nullable', 'integer', 'between:1,4']]);
        $standaloneWebsite->update(['price_level' => $data['price_level'] ?? null]);

        return back()->with('success', 'Standalone price level updated.');
    }

    public function rotateApiToken(StandaloneWebsite $standaloneWebsite, StandaloneCredentialService $service): RedirectResponse
    {
        $issued = $service->issueBootstrapToken();
        $standaloneWebsite->update([
            'api_token_digest' => $issued['digest'], 'api_token_prefix' => $issued['prefix'],
            'api_token_type' => $issued['type'], 'api_token_must_rotate' => true,
            'api_token_expires_at' => $issued['expires_at'], 'api_token_rotated_at' => now(),
        ]);
        session()->flash('standalone_credentials', ['api_token' => $issued['plain_text'], 'expires_at' => $issued['expires_at']]);

        return redirect()->route('admin.standalones.credentials', $standaloneWebsite);
    }
}

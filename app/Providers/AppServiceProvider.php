<?php

namespace App\Providers;

use App\Models\FundingWebhookPayload;
use App\Models\Transaction;
use App\Models\User;
use App\Observers\TransactionMobilePushObserver;
use App\Observers\UserObserver;
use App\Observers\WalletFundingMobilePushObserver;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Inertia::share([
            'auth' => fn () => [
                'user' => auth()->check() ? [
                    'id' => auth()->id(),
                    'first_name' => auth()->user()->first_name,
                    'last_name' => auth()->user()->last_name,
                    'email' => auth()->user()->email,
                    'main_wallet' => auth()->user()->main_wallet,
                    'username' => auth()->user()->username,
                    'phone_number' => auth()->user()->phone_number,
                    'is_marketer' => auth()->user()->is_marketer,
                    // add only the fields you actually need on frontend
                ] : null,
            ],

            'flash' => fn () => [
                'success' => session('success'),
                'error' => session('error'),
            ],

            'impersonator' => fn () => session()->has('impersonator') ? [
                'fname' => auth()->user()->first_name,
                'lname' => auth()->user()->last_name,
                'username' => auth()->user()->username,
                'pin' => auth()->user()->pin,
                'exitUrl' => route('admin.exit_impersonate'),
            ] : null,
        ]);

        User::observe(UserObserver::class);
        Transaction::observe(TransactionMobilePushObserver::class);
        FundingWebhookPayload::observe(WalletFundingMobilePushObserver::class);
    }
}

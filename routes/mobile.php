<?php

use App\Http\Controllers\Api\Mobile\V1\AuthController;
use App\Http\Controllers\Api\Mobile\V1\MobileBootstrapController;
use App\Http\Controllers\Api\Mobile\V1\MobileCatalogueController;
use App\Http\Controllers\Api\Mobile\V1\MobileDashboardController;
use App\Http\Controllers\Api\Mobile\V1\MobileDeviceController;
use App\Http\Controllers\Api\Mobile\V1\MobileProfileController;
use App\Http\Controllers\Api\Mobile\V1\MobilePurchaseController;
use App\Http\Controllers\Api\Mobile\V1\MobileSecurityController;
use App\Http\Controllers\Api\Mobile\V1\MobileSupportController;
use App\Http\Controllers\Api\Mobile\V1\MobileTransactionController;
use App\Http\Controllers\Api\Mobile\V1\MobileWalletController;
use App\Http\Controllers\Api\Mobile\V1\OnboardingController;
use Illuminate\Support\Facades\Route;

Route::get('/health', [MobileBootstrapController::class, 'health'])->name('health');
Route::get('/config', [MobileBootstrapController::class, 'config'])->name('config');
Route::get('/support', MobileSupportController::class)->name('support');

Route::prefix('auth')->name('auth.')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:mobile-register')->name('register');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:mobile-login')->name('login');
    Route::post('/refresh', [AuthController::class, 'refresh'])->middleware('throttle:mobile-refresh')->name('refresh');
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->middleware('throttle:mobile-password')->name('forgot-password');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->middleware('throttle:mobile-password')->name('reset-password');

    Route::middleware(['auth:sanctum', 'mobile.user.active'])->group(function () {
        Route::get('/session', [AuthController::class, 'session'])->name('session');
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::post('/logout-all', [AuthController::class, 'logoutAll'])->name('logout-all');
    });
});

Route::middleware(['auth:sanctum', 'mobile.user.active'])->group(function () {
    Route::get('/dashboard', MobileDashboardController::class)->name('dashboard');
    Route::get('/catalogue/products', [MobileCatalogueController::class, 'products'])->name('catalogue.products');
    Route::get('/catalogue/categories', [MobileCatalogueController::class, 'categories'])->name('catalogue.categories');
    Route::get('/catalogue/plans', [MobileCatalogueController::class, 'plans'])->name('catalogue.plans');
    Route::post('/purchases/data', [MobilePurchaseController::class, 'data'])->middleware('throttle:mobile-purchase')->name('purchases.data');
    Route::post('/purchases/airtime', [MobilePurchaseController::class, 'airtime'])->middleware('throttle:mobile-purchase')->name('purchases.airtime');
    Route::post('/cable/validate', [MobilePurchaseController::class, 'validateCable'])->middleware('throttle:mobile-purchase')->name('cable.validate');
    Route::post('/purchases/cable', [MobilePurchaseController::class, 'cable'])->middleware('throttle:mobile-purchase')->name('purchases.cable');
    Route::post('/electricity/validate', [MobilePurchaseController::class, 'validateElectricity'])->middleware('throttle:mobile-purchase')->name('electricity.validate');
    Route::post('/purchases/electricity', [MobilePurchaseController::class, 'electricity'])->middleware('throttle:mobile-purchase')->name('purchases.electricity');
    Route::get('/purchases/status/{reference}', [MobilePurchaseController::class, 'status'])->name('purchases.status');
    Route::get('/wallet', [MobileWalletController::class, 'show'])->name('wallet.show');
    Route::get('/wallet/accounts', [MobileWalletController::class, 'accounts'])->name('wallet.accounts');
    Route::post('/wallet/accounts', [MobileWalletController::class, 'createAccount'])->middleware('throttle:mobile-purchase')->name('wallet.accounts.create');
    Route::get('/wallet/funding-options', [MobileWalletController::class, 'fundingOptions'])->name('wallet.funding-options');
    Route::get('/wallet/funding-history', [MobileWalletController::class, 'fundingHistory'])->name('wallet.funding-history');
    Route::get('/transactions', [MobileTransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transactions/{transaction}', [MobileTransactionController::class, 'show'])->name('transactions.show');
    Route::get('/transactions/{transaction}/receipt', [MobileTransactionController::class, 'receipt'])->name('transactions.receipt');
    Route::put('/security/password', [MobileSecurityController::class, 'password'])->middleware('throttle:mobile-password')->name('security.password');
    Route::put('/security/pin', [MobileSecurityController::class, 'pin'])->middleware('throttle:mobile-pin')->name('security.pin.update');
    Route::delete('/account', [MobileSecurityController::class, 'deactivate'])->middleware('throttle:mobile-password')->name('account.deactivate');
    Route::post('/devices', [MobileDeviceController::class, 'store'])->name('devices.store');
    Route::delete('/devices/{device}', [MobileDeviceController::class, 'destroy'])->name('devices.destroy');
    Route::get('/notification-preferences', [MobileDeviceController::class, 'preferences'])->name('notifications.preferences');
    Route::put('/notification-preferences', [MobileDeviceController::class, 'updatePreferences'])->name('notifications.preferences.update');
    Route::get('/profile', [MobileProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [MobileProfileController::class, 'update'])->name('profile.update');
    Route::post('/auth/phone/send-otp', [OnboardingController::class, 'sendPhoneOtp'])->middleware('throttle:mobile-otp-send')->name('auth.phone.send');
    Route::post('/auth/phone/verify-otp', [OnboardingController::class, 'verifyPhoneOtp'])->middleware('throttle:mobile-otp-verify')->name('auth.phone.verify');
    Route::post('/security/pin', [OnboardingController::class, 'setTransactionPin'])->name('security.pin.set');
    Route::post('/security/pin/verify', [OnboardingController::class, 'verifyTransactionPin'])->middleware('throttle:mobile-pin')->name('security.pin.verify');
});

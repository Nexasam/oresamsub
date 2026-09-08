<?php

namespace App\Http\Controllers;

use App\Models\Automation;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class TelecomAbodePlansController extends Controller
{
    private const PLANS_URL = 'https://telecomabode.com.ng/api/data/data_plans';

    public function __invoke(): View|Response
    {
        $automation = Automation::query()
            ->where(function ($query): void {
                $query->where('slug', 'like', '%telecom%')
                    ->orWhere('automation_name', 'like', '%telecom%');
            })
            ->get()
            ->first(fn (Automation $candidate): bool => Str::contains(
                Str::lower($candidate->slug.' '.$candidate->automation_name),
                ['telecomabode', 'telecom abode', 'telecom-abode', 'telecom_abode']
            ));

        if (! $automation || blank($automation->api_public_key)) {
            return response()
                ->view('admin.telecom-abode-plans', [
                    'plans' => null,
                    'error' => 'Telecom Abode API key is not configured in Automations.',
                ], 503);
        }

        try {
            $response = Http::acceptJson()
                ->withToken($automation->api_public_key, 'Token')
                ->connectTimeout(10)
                ->timeout(30)
                ->get(self::PLANS_URL);
        } catch (ConnectionException) {
            return response()
                ->view('admin.telecom-abode-plans', [
                    'plans' => null,
                    'error' => 'Telecom Abode could not be reached. Please try again.',
                ], 502);
        }

        if ($response->failed() || ! is_array($response->json())) {
            return response()
                ->view('admin.telecom-abode-plans', [
                    'plans' => null,
                    'error' => 'Telecom Abode could not return the plans. Check the saved production API key.',
                ], 502);
        }

        return view('admin.telecom-abode-plans', [
            'plans' => $response->json(),
            'error' => null,
        ]);
    }
}

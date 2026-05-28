<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Automation;
use App\Models\UserAutomation;
use Illuminate\Http\Request;

class UserAutomationController extends Controller
{
    /**
     * List all subscriptions
     */public function index(Request $request)
{
    $query = UserAutomation::with(['user', 'automation'])->latest();

    // FILTER: user
    if ($request->filled('user_id')) {
        $query->where('user_id', $request->user_id);
    }

    // FILTER: automation
    if ($request->filled('automation_id')) {
        $query->where('automation_id', $request->automation_id);
    }

    // PAGINATION SIZE
    $perPage = $request->get('per_page', 15);

    $subscriptions = $query->paginate($perPage)->withQueryString();

    $users = User::select('id', 'first_name', 'last_name', 'email')->get();
    $automations = Automation::select('id', 'automation_name')->get();

    return view('admin.provider_automation_management.index', compact(
        'subscriptions',
        'users',
        'automations'
    ));
}

    public function getUserAutomations($userId)
{
    return UserAutomation::where('user_id', $userId)
        ->pluck('automation_id');
}

    /**
     * Assign / Unassign automations
     */
    public function sync(Request $request)
    {
        $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'automations' => ['nullable', 'array'],
        ]);
    
        $userId = $request->user_id;
        $selectedAutomations = $request->automations ?? [];
    
        // Get already assigned automations
        $existing = UserAutomation::where('user_id', $userId)
            ->pluck('automation_id')
            ->toArray();
    
        // Only keep NEW ones
        $newAutomations = array_diff($selectedAutomations, $existing);
    
        foreach ($newAutomations as $automationId) {
            UserAutomation::create([
                'user_id' => $userId,
                'automation_id' => $automationId,
                'automation_pricing_type' => 'PAYG',
                'pricing_amount' => 0,
                'first_payment' => 0,
                'product' => 'data',
            ]);
        }
    
        return back()->with('success', 'Automations assigned safely');
    }


    public function customerIndex(Request $request)
    {
            $user = $request->user();

            $user->load(['automations']);

            $allAutomations = \App\Models\Automation::select('id', 'automation_name')
                ->latest()
                ->get();

            return \Inertia\Inertia::render('ApiAccess/Index', [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->first_name . ' ' . $user->last_name,
                    'email' => $user->email,
                    'api_token' => $user->api_token,
                    'automations' => $user->automations,
                ],
                'allAutomations' => $allAutomations
            ]);
        }
}
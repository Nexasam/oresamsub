<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StandaloneFeature;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StandaloneFeatureController extends Controller
{
    public function index(): View
    {
        return view('admin.standalones.features', ['features' => StandaloneFeature::orderBy('sort_order')->orderBy('name')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        StandaloneFeature::create($this->validated($request));

        return back()->with('success', 'Standalone feature created.');
    }

    public function update(Request $request, StandaloneFeature $feature): RedirectResponse
    {
        $feature->update($this->validated($request, $feature));

        return back()->with('success', 'Standalone feature updated.');
    }

    private function validated(Request $request, ?StandaloneFeature $feature = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'slug' => ['required', 'alpha_dash', 'max:150', Rule::unique('standalone_features', 'slug')->ignore($feature)],
            'description' => ['nullable', 'string', 'max:1000'],
            'billing_type' => ['nullable', Rule::in(['one_time', 'monthly', 'one_time_plus_monthly', 'free'])],
            'purchase_mode' => ['nullable', Rule::in(['single', 'named_slots'])],
            'default_price' => ['required', 'numeric', 'min:0'], 'level_1_price' => ['required', 'numeric', 'min:0'],
            'level_2_price' => ['required', 'numeric', 'min:0'], 'level_3_price' => ['required', 'numeric', 'min:0'],
            'level_4_price' => ['required', 'numeric', 'min:0'], 'sort_order' => ['nullable', 'integer', 'min:0'],
            'default_monthly_price' => [Rule::requiredIf(fn () => in_array($request->input('billing_type'), ['monthly', 'one_time_plus_monthly'], true)), 'nullable', 'numeric', 'min:0'],
            'level_1_monthly_price' => [Rule::requiredIf(fn () => in_array($request->input('billing_type'), ['monthly', 'one_time_plus_monthly'], true)), 'nullable', 'numeric', 'min:0'],
            'level_2_monthly_price' => [Rule::requiredIf(fn () => in_array($request->input('billing_type'), ['monthly', 'one_time_plus_monthly'], true)), 'nullable', 'numeric', 'min:0'],
            'level_3_monthly_price' => [Rule::requiredIf(fn () => in_array($request->input('billing_type'), ['monthly', 'one_time_plus_monthly'], true)), 'nullable', 'numeric', 'min:0'],
            'level_4_monthly_price' => [Rule::requiredIf(fn () => in_array($request->input('billing_type'), ['monthly', 'one_time_plus_monthly'], true)), 'nullable', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        $data['billing_type'] = $data['billing_type'] ?? 'one_time';
        $data['purchase_mode'] = $data['purchase_mode'] ?? 'single';
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
